<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $query = Chat::with([
            'organization', 
            'creator', 
            'assignedTo', 
            'participants.user',
            'messages' => function($q) {
                $q->latest()->limit(1);
            },
            'client'
        ]);

        // Поиск по названию, описанию, телефону
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('wazzup_chat_id', 'like', '%' . $search . '%');
            });
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // По умолчанию показываем только активные чаты для администраторов
            $query->where('status', 'active');
        }

        // Фильтр по типу
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Фильтр по организации
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        // Фильтр по назначенному пользователю
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Фильтр по дате создания
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['title', 'status', 'type', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $chats = $query->paginate(20);

        // Статистика
        $stats = [
            'total' => Chat::count(),
            'active' => Chat::where('status', 'active')->count(),
            'closed' => Chat::where('status', 'closed')->count(),
            'transferred' => Chat::where('status', 'transferred')->count(),
            'pending' => Chat::where('status', 'pending')->count(),
            'rejected' => Chat::where('status', 'rejected')->count(),
        ];

        // Данные для фильтров
        $organizations = \App\Models\Organization::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.chats.index', compact('chats', 'stats', 'organizations', 'users'));
    }

    public function show(Chat $chat)
    {
        $chat->load(['organization', 'creator', 'assignedTo', 'participants.user', 'messages.user']);
        
        $messages = $chat->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return view('admin.chats.show', compact('chat', 'messages'));
    }

    public function transfer(Request $request, Chat $chat)
    {
        $request->validate([
            'new_user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $newUser = User::find($request->new_user_id);

        // Проверяем, что новый пользователь принадлежит к той же организации
        if (!$newUser->organizations()->where('organization_id', $chat->organization_id)->exists()) {
            return back()->with('error', 'Пользователь не принадлежит к этой организации');
        }

        // Добавляем нового пользователя как админа чата
        $chat->participants()->create([
            'user_id' => $newUser->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Обновляем назначение чата
        $chat->update([
            'assigned_to' => $newUser->id,
            'status' => 'transferred',
        ]);

        // Отправляем системное сообщение о передаче
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => auth()->id(),
            'content' => $request->reason 
                ? "Чат передан пользователю {$newUser->name}. Причина: {$request->reason}"
                : "Чат передан пользователю {$newUser->name}",
            'type' => 'system',
        ]);

        return back()->with('success', 'Чат успешно передан');
    }

    public function close(Request $request, Chat $chat)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $chat->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        // Отправляем системное сообщение о закрытии
        $closeMessage = $request->reason 
            ? "Беседа завершена. Причина: {$request->reason}"
            : "Беседа завершена";

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => auth()->id(),
            'content' => $closeMessage,
            'type' => 'system',
        ]);

        return back()->with('success', 'Чат успешно закрыт');
    }

    /**
     * Принятие нового чата
     */
    public function accept(Request $request, Chat $chat)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'comment' => 'nullable|string|max:500',
        ]);

        // Проверяем, что чат в статусе pending
        if ($chat->status !== 'pending') {
            return back()->with('error', 'Чат уже не в статусе ожидания');
        }

        // Назначаем чат текущему пользователю или указанному пользователю
        $assignedUserId = $request->assigned_to ?: auth()->id();

        $chat->update([
            'status' => 'active',
            'assigned_to' => $assignedUserId,
        ]);

        // Добавляем назначенного пользователя как участника, если его еще нет
        if (!$chat->participants()->where('user_id', $assignedUserId)->exists()) {
            $chat->participants()->create([
                'user_id' => $assignedUserId,
                'role' => 'admin',
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        // Отправляем системное сообщение о принятии
        $acceptMessage = $request->comment 
            ? "Чат принят пользователем " . auth()->user()->name . ". Комментарий: {$request->comment}"
            : "Чат принят пользователем " . auth()->user()->name;

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => auth()->id(),
            'content' => $acceptMessage,
            'type' => 'system',
        ]);

        return back()->with('success', 'Чат успешно принят');
    }

    /**
     * Отклонение нового чата
     */
    public function reject(Request $request, Chat $chat)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Проверяем, что чат в статусе pending
        if ($chat->status !== 'pending') {
            return back()->with('error', 'Чат уже не в статусе ожидания');
        }

        $chat->update([
            'status' => 'rejected',
            'closed_at' => now(),
        ]);

        // Отправляем системное сообщение об отклонении
        $rejectMessage = "Чат отклонен пользователем " . auth()->user()->name . ". Причина: {$request->reason}";

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => auth()->id(),
            'content' => $rejectMessage,
            'type' => 'system',
        ]);

        return back()->with('success', 'Чат отклонен');
    }

    /**
     * Массовое принятие чатов
     */
    public function bulkAccept(Request $request)
    {
        $request->validate([
            'chat_ids' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Парсим JSON массив chat_ids
        $chatIds = json_decode($request->chat_ids, true);
        
        if (!is_array($chatIds)) {
            return back()->with('error', 'Неверный формат данных');
        }

        $assignedUserId = $request->assigned_to ?: auth()->id();

        $chats = Chat::whereIn('id', $chatIds)->where('status', 'pending')->get();
        
        $accepted = 0;
        $errors = [];

        foreach ($chats as $chat) {
            try {
                $chat->update([
                    'status' => 'active',
                    'assigned_to' => $assignedUserId,
                ]);

                // Добавляем назначенного пользователя как участника
                if (!$chat->participants()->where('user_id', $assignedUserId)->exists()) {
                    $chat->participants()->create([
                        'user_id' => $assignedUserId,
                        'role' => 'admin',
                        'is_active' => true,
                        'joined_at' => now(),
                    ]);
                }

                // Отправляем системное сообщение
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => auth()->id(),
                    'content' => "Чат принят пользователем " . auth()->user()->name,
                    'type' => 'system',
                ]);

                $accepted++;
            } catch (\Exception $e) {
                $errors[] = "Ошибка принятия чата {$chat->id}: " . $e->getMessage();
            }
        }

        $message = "Принято чатов: {$accepted}";
        if (!empty($errors)) {
            $message .= ". Ошибки: " . count($errors);
        }

        return back()->with('success', $message);
    }

    public function export(Request $request)
    {
        $query = Chat::with(['organization', 'creator', 'assignedTo', 'participants.user']);

        // Применяем те же фильтры, что и в index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('wazzup_chat_id', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $chats = $query->latest()->get();

        $filename = 'chats_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($chats) {
            $file = fopen('php://output', 'w');
            
            // Заголовки CSV
            fputcsv($file, [
                'ID', 'Название', 'Описание', 'Тип', 'Статус', 'Организация', 
                'Создатель', 'Назначен', 'Участники', 'Сообщения', 'Телефон', 
                'WhatsApp ID', 'Дата создания', 'Дата закрытия'
            ]);

            foreach ($chats as $chat) {
                fputcsv($file, [
                    $chat->id,
                    $chat->title,
                    $chat->description,
                    $chat->type,
                    $chat->status,
                    $chat->organization->name,
                    $chat->creator->name,
                    $chat->assignedTo ? $chat->assignedTo->name : '',
                    $chat->participants->count(),
                    $chat->messages->count(),
                    $chat->phone,
                    $chat->wazzup_chat_id,
                    $chat->created_at->format('d.m.Y H:i:s'),
                    $chat->closed_at ? $chat->closed_at->format('d.m.Y H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
