<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Wazzup24Service;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $wazzupService;

    public function __construct(Wazzup24Service $wazzupService)
    {
        $this->wazzupService = $wazzupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Поиск
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->latest()->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Только администраторы могут создавать клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут создавать клиентов.');
        }

        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Только администраторы могут создавать клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут создавать клиентов.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'uuid_wazzup' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Client::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'uuid_wazzup' => $request->uuid_wazzup,
            'comment' => $request->comment,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Клиент успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['chats' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        // Только администраторы могут редактировать клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут редактировать клиентов.');
        }

        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Только администраторы могут редактировать клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут редактировать клиентов.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'uuid_wazzup' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'uuid_wazzup' => $request->uuid_wazzup,
            'comment' => $request->comment,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Клиент успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Только администраторы могут удалять клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут удалять клиентов.');
        }

        // Проверяем, есть ли чаты у клиента
        if ($client->chats()->count() > 0) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Нельзя удалить клиента, у которого есть чаты');
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Клиент успешно удален');
    }

    /**
     * Импорт клиентов из Wazzup24
     */
    public function importFromWazzup(Request $request)
    {
        // Только администраторы могут импортировать клиентов
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут импортировать клиентов.');
        }

        try {
            $limit = $request->get('limit', 100);
            $specificPhone = $request->get('phone');
            
            // Получаем клиентов из Wazzup24
            $result = $this->wazzupService->getClients($limit);
            
            if (!$result['success']) {
                return redirect()->route('admin.clients.index')
                    ->with('error', 'Ошибка получения данных из Wazzup24: ' . $result['error']);
            }

            $imported = 0;
            $updated = 0;
            $errors = [];

            // Фильтруем клиентов, если указан конкретный телефон
            $clientsToImport = $result['clients'];
            if ($specificPhone) {
                $clientsToImport = array_filter($result['clients'], function($client) use ($specificPhone) {
                    return $client['phone'] === $specificPhone;
                });
            }

            foreach ($clientsToImport as $clientData) {
                try {
                    // Проверяем, существует ли клиент с таким телефоном
                    $existingClient = Client::where('phone', $clientData['phone'])->first();
                    
                    if ($existingClient) {
                        // Обновляем существующего клиента
                        $existingClient->update([
                            'name' => $clientData['name'],
                            'uuid_wazzup' => $clientData['uuid_wazzup'],
                            'comment' => 'Обновлено из Wazzup24: ' . now()->format('d.m.Y H:i:s')
                        ]);
                        $updated++;
                    } else {
                        // Создаем нового клиента
                        Client::create([
                            'name' => $clientData['name'],
                            'phone' => $clientData['phone'],
                            'uuid_wazzup' => $clientData['uuid_wazzup'],
                            'comment' => 'Импортировано из Wazzup24: ' . now()->format('d.m.Y H:i:s'),
                            'is_active' => true
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Ошибка импорта клиента {$clientData['phone']}: " . $e->getMessage();
                }
            }

            $message = "Импорт завершен. Импортировано: {$imported}, обновлено: {$updated}";
            
            if (!empty($errors)) {
                $message .= ". Ошибки: " . count($errors);
            }

            // Если импорт был из предварительного просмотра, возвращаемся туда
            if ($request->has('from_preview')) {
                return redirect()->route('admin.clients.wazzup.preview', ['limit' => $limit])
                    ->with('success', $message);
            }

            return redirect()->route('admin.clients.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Ошибка импорта: ' . $e->getMessage());
        }
    }

    /**
     * Просмотр клиентов из Wazzup24 (без импорта)
     */
    public function previewWazzupClients(Request $request)
    {
        // Только администраторы могут просматривать клиентов из Wazzup24
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ запрещен. Только администраторы могут просматривать клиентов из Wazzup24.');
        }

        try {
            $limit = $request->get('limit', 50);
            
            $result = $this->wazzupService->getClients($limit);
            
            if (!$result['success']) {
                return redirect()->route('admin.clients.index')
                    ->with('error', 'Ошибка получения данных из Wazzup24: ' . $result['error']);
            }

            return view('admin.clients.wazzup-preview', [
                'clients' => $result['clients'],
                'total' => $result['total']
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Ошибка предварительного просмотра: ' . $e->getMessage());
        }
    }

    /**
     * Начать чат с клиентом
     */
    public function startChat(Request $request, Client $client)
    {
        try {
            // Проверяем, есть ли уже активный чат с этим клиентом
            $existingChat = \App\Models\Chat::where('phone', $client->phone)
                ->where('status', 'active')
                ->first();

            if ($existingChat) {
                // Если чат уже существует, перенаправляем на него
                return redirect()->route('user.chat.show', $existingChat)
                    ->with('info', 'Чат с этим клиентом уже существует');
            }

            // Создаем новый чат
            $chat = \App\Models\Chat::create([
                'title' => "Чат с {$client->name}",
                'description' => "Чат с клиентом {$client->name} ({$client->phone})",
                'type' => 'private',
                'phone' => $client->phone,
                'status' => 'active',
                'created_by' => auth()->id(),
                'assigned_to' => auth()->id(),
                'organization_id' => auth()->user()->organizations->first()?->id,
                'is_messenger_chat' => false,
                'last_activity_at' => now(),
            ]);

            // Добавляем текущего пользователя как участника чата
            \App\Models\ChatParticipant::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Создаем системное сообщение
            \App\Models\Message::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'content' => "Чат с клиентом {$client->name} ({$client->phone}) начат",
                'type' => 'system',
            ]);

            return redirect()->route('user.chat.show', $chat)
                ->with('success', 'Чат с клиентом успешно создан');

        } catch (\Exception $e) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Ошибка создания чата: ' . $e->getMessage());
        }
    }
}
