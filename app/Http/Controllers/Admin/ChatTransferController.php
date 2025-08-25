<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Department;
use App\Models\User;
use App\Services\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatTransferController extends Controller
{
    protected $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    /**
     * Показать форму передачи чата
     */
    public function showTransferForm($chatId)
    {
        $chat = Chat::with(['department', 'assignedTo'])->findOrFail($chatId);
        
        // Проверяем права доступа
        if (!$this->canTransferChat($chat)) {
            return redirect()->back()->with('error', 'У вас нет прав для передачи этого чата');
        }

        $availableDepartments = $this->messengerService->getAvailableDepartments($chat->department_id);
        $availableManagers = $this->messengerService->getAvailableManagers(
            $chat->assigned_to, 
            $chat->department_id
        );

        return view('admin.chat-transfer.form', compact('chat', 'availableDepartments', 'availableManagers'));
    }

    /**
     * Передача чата в другой отдел
     */
    public function transferToDepartment(Request $request, $chatId)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $chat = Chat::findOrFail($chatId);
        
        if (!$this->canTransferChat($chat)) {
            return redirect()->back()->with('error', 'У вас нет прав для передачи этого чата');
        }

        $result = $this->messengerService->transferToDepartmentWithNotification(
            $chat,
            $request->department_id,
            $request->reason
        );

        if ($result) {
            return redirect()->route('user.chat.show', $chatId)
                ->with('success', 'Чат успешно передан в другой отдел');
        }

        return redirect()->back()->with('error', 'Ошибка при передаче чата');
    }

    /**
     * Передача чата другому менеджеру
     */
    public function transferToUser(Request $request, $chatId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $chat = Chat::findOrFail($chatId);
        
        if (!$this->canTransferChat($chat)) {
            return redirect()->back()->with('error', 'У вас нет прав для передачи этого чата');
        }

        $result = $this->messengerService->transferToUserWithNotification(
            $chat,
            $request->user_id,
            $request->reason
        );

        if ($result) {
            return redirect()->route('user.chat.show', $chatId)
                ->with('success', 'Чат успешно передан другому менеджеру');
        }

        return redirect()->back()->with('error', 'Ошибка при передаче чата');
    }

    /**
     * Массовая передача чатов
     */
    public function bulkTransfer(Request $request)
    {
        $request->validate([
            'chat_ids' => 'required|array',
            'chat_ids.*' => 'exists:chats,id',
            'transfer_type' => 'required|in:department,user',
            'target_id' => 'required|integer',
            'reason' => 'nullable|string|max:500'
        ]);

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->chat_ids as $chatId) {
            $chat = Chat::find($chatId);
            
            if (!$chat || !$this->canTransferChat($chat)) {
                $errorCount++;
                continue;
            }

            if ($request->transfer_type === 'department') {
                $result = $this->messengerService->transferToDepartmentWithNotification(
                    $chat,
                    $request->target_id,
                    $request->reason
                );
            } else {
                $result = $this->messengerService->transferToUserWithNotification(
                    $chat,
                    $request->target_id,
                    $request->reason
                );
            }

            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = "Передано чатов: {$successCount}";
        if ($errorCount > 0) {
            $message .= ", ошибок: {$errorCount}";
        }

        return redirect()->route('user.chat.index')
            ->with('success', $message);
    }

    /**
     * Получение доступных отделов для AJAX
     */
    public function getAvailableDepartments(Request $request)
    {
        $currentDepartmentId = $request->get('current_department_id');
        $departments = $this->messengerService->getAvailableDepartments($currentDepartmentId);
        
        return response()->json($departments);
    }

    /**
     * Получение доступных менеджеров для AJAX
     */
    public function getAvailableManagers(Request $request)
    {
        $currentUserId = $request->get('current_user_id');
        $departmentId = $request->get('department_id');
        
        $managers = $this->messengerService->getAvailableManagers($currentUserId, $departmentId);
        
        return response()->json($managers);
    }

    /**
     * История передач чата
     */
    public function transferHistory($chatId)
    {
        $chat = Chat::findOrFail($chatId);
        $history = $this->messengerService->getChatTransferHistory($chat);
        
        return view('admin.chat-transfer.history', compact('chat', 'history'));
    }

    /**
     * Проверка прав на передачу чата
     */
    protected function canTransferChat($chat)
    {
        $user = Auth::user();
        
        // Админы могут передавать любые чаты
        if ($user->role === 'admin') {
            return true;
        }
        
        // Менеджеры могут передавать чаты из своего отдела
        if ($user->role === 'manager') {
            return $chat->department_id === $user->department_id;
        }
        
        // Сотрудники могут передавать только свои чаты
        if ($user->role === 'employee') {
            return $chat->assigned_to === $user->id;
        }
        
        return false;
    }
}
