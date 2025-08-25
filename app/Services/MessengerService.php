<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Department;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MessengerService
{
    public function __construct()
    {
        // Убрали зависимость от Wazzup24Service
    }

    /**
     * Обработка входящего сообщения в мессенджере
     */
    public function handleIncomingMessage($phone, $message, $contactData = null)
    {
        try {
            Log::info('=== MESSENGER SERVICE: Processing message ===', [
                'phone' => $phone,
                'message' => $message,
                'contact' => $contactData,
                'contact_type' => gettype($contactData)
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found/created:', ['client_id' => $client->id, 'name' => $client->name]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found/created:', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status,
                'is_new' => $isNewChat
            ]);
            
            // Обрабатываем сообщение в зависимости от статуса
            $this->processMessage($chat, $message, $client);
            
            // Если это новый чат, отправляем меню
            if ($isNewChat) {
                $this->handleMenuMessage($chat, $message, $client);
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingMessage:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Обработка сообщения в зависимости от статуса чата
     */
    protected function processMessage($chat, $message, $client)
    {
        $message = trim($message);
        
        // Сохраняем каждое входящее сообщение клиента
        $this->saveClientMessage($chat, $message, $client);
        
        switch ($chat->messenger_status) {
            case 'menu':
                return $this->handleMenuMessage($chat, $message, $client);
            
            case 'department_selected':
                return $this->handleDepartmentSelection($chat, $message, $client);
            
            case 'active':
                return $this->handleActiveChat($chat, $message, $client);
            
            case 'completed':
                return $this->handleCompletedChat($chat, $message, $client);
            
            default:
                return $this->resetToMenu($chat, $client);
        }
    }

    /**
     * Обработка сообщения в главном меню
     */
    protected function handleMenuMessage($chat, $message, $client)
    {
        // Специальная обработка для тестового номера
        if ($chat->messenger_phone === '77476644108') {
            $this->handleTestNumberMenu($chat, $message, $client);
            return;
        }
        
        $departments = Department::orderBy('name')->get();
        $menuText = $this->generateMenuText($departments);
        
        if (in_array($message, ['1', '2', '3', '4', '0'])) {
            if ($message === '0') {
                $this->sendMessage($chat, "До свидания! Обращайтесь снова.");
                $chat->update(['messenger_status' => 'completed']);
                return;
            }
            
            $department = Department::find($message);
            if ($department) {
                $chat->update([
                    'department_id' => $department->id,
                    'messenger_status' => 'department_selected'
                ]);
                
                $this->sendMessage($chat, "Вы выбрали отдел: {$department->name}\n\nТеперь напишите ваш вопрос:");
                return;
            }
        }
        
        // Если сообщение не распознано, показываем меню
        $this->sendMessage($chat, $menuText);
    }

    /**
     * Обработка меню для тестового номера
     */
    protected function handleTestNumberMenu($chat, $message, $client)
    {
        $menuText = "Добрый день. Это Акжол Фарм.\n\nЧто вас интересует:\n1. Бухгалтерия\n2. IT отдел\n3. HR отдел\n4. Вопросы по товарам в аптеке\n\n0. Выход";
        
        if (in_array($message, ['1', '2', '3', '4', '0'])) {
            if ($message === '0') {
                $this->sendMessage($chat, "До свидания! Обращайтесь снова.");
                $chat->update(['messenger_status' => 'completed']);
                return;
            }
            
            $departments = [
                '1' => ['name' => 'Бухгалтерия', 'id' => 1],
                '2' => ['name' => 'IT отдел', 'id' => 2], 
                '3' => ['name' => 'HR отдел', 'id' => 9], // ID 9 в базе данных
                '4' => ['name' => 'Вопросы по товарам в аптеке', 'id' => 4]
            ];
            
            if (isset($departments[$message])) {
                $department = $departments[$message];
                $chat->update([
                    'messenger_status' => 'department_selected',
                    'department_id' => $department['id']
                ]);
                
                $this->sendMessage($chat, "Подключаем с {$department['name']}. Пожалуйста, можете задать вопрос.");
                return;
            }
        }
        
        // Если сообщение не распознано, показываем меню
        $this->sendMessage($chat, $menuText);
    }

    /**
     * Обработка выбора отдела
     */
    protected function handleDepartmentSelection($chat, $message, $client)
    {
        if (empty(trim($message))) {
            $this->sendMessage($chat, "Пожалуйста, напишите ваш вопрос:");
            return;
        }
        
        // Создаем активный чат
        $chat->update([
            'messenger_status' => 'active',
            'title' => "Вопрос клиента: " . substr($message, 0, 50) . "...",
            'last_activity_at' => now()
        ]);
        
        // Сохраняем сообщение клиента
        $this->saveClientMessage($chat, $message, $client);
        
        // Уведомляем отдел
        $this->notifyDepartment($chat, $message);
        
        $this->sendMessage($chat, "Ваш вопрос отправлен в отдел {$chat->department->name}. Ожидайте ответа.");
    }

    /**
     * Обработка активного чата
     */
    protected function handleActiveChat($chat, $message, $client)
    {
        // Сохраняем сообщение клиента
        $this->saveClientMessage($chat, $message, $client);
        
        // Обновляем время активности
        $chat->update(['last_activity_at' => now()]);
        
        // Уведомляем назначенного сотрудника
        if ($chat->assigned_to) {
            $this->notifyAssignedUser($chat, $message);
        } else {
            // Если никто не назначен, уведомляем отдел
            $this->notifyDepartment($chat, $message);
        }
    }

    /**
     * Обработка завершенного чата
     */
    protected function handleCompletedChat($chat, $message, $client)
    {
        if ($message === '1') {
            // Продолжить чат с тем же менеджером
            $chat->update(['messenger_status' => 'active']);
            $this->sendMessage($chat, "Чат продолжен. Можете задать новый вопрос.");
        } elseif ($message === '0') {
            // Вернуться в главное меню
            $this->resetToMenu($chat, $client);
        } else {
            $this->sendMessage($chat, "1 - Продолжить чат\n0 - Вернуться в главное меню");
        }
    }



    /**
     * Сброс к главному меню
     */
    protected function resetToMenu($chat, $client)
    {
        $chat->update([
            'messenger_status' => 'menu',
            'department_id' => null,
            'assigned_to' => null
        ]);
        
        // Специальная обработка для тестового номера
        if ($chat->messenger_phone === '77476644108') {
            $menuText = $this->generateTestMenuText();
        } else {
            $departments = Department::orderBy('name')->get();
            $menuText = $this->generateMenuText($departments);
        }
        
        $this->sendMessage($chat, $menuText);
    }

    /**
     * Генерация текста меню
     */
    protected function generateMenuText($departments)
    {
        $text = "Добро пожаловать! С кем хотите связаться?\n\n";
        
        foreach ($departments as $department) {
            $text .= "{$department->id}. {$department->name}\n";
        }
        
        $text .= "\n0. Выход";
        
        return $text;
    }

    /**
     * Генерация текста меню для тестового номера
     */
    protected function generateTestMenuText()
    {
        return "Добрый день. Это Акжол Фарм.\n\nЧто вас интересует:\n\n1. Бухгалтерия\n2. IT отдел\n3. HR отдел\n4. Вопросы по товарам в аптеке\n\n0. Выход";
    }

    /**
     * Сохранение сообщения клиента
     */
    protected function saveClientMessage($chat, $message, $client)
    {
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $client->id,
            'content' => $message, // Сохраняем только оригинальное сообщение
            'type' => 'text',
            'metadata' => [
                'original_message' => $message,
                'client_name' => $client->name,
                'direction' => 'incoming'
            ]
        ]);
    }

    /**
     * Отправка сообщения клиенту
     */
    protected function sendMessage($chat, $message)
    {
        // Сохраняем в базу
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Системный пользователь
            'content' => $message,
            'type' => 'system',
            'metadata' => [
                'direction' => 'outgoing',
                'is_bot_message' => true
            ]
        ]);
        
        // Сообщение сохранено только локально
        Log::info("Системное сообщение сохранено локально в чате {$chat->id}");
    }

    /**
     * Отправка сообщения от менеджера клиенту
     */
    public function sendManagerMessage($chat, $message, $manager)
    {
        // Просто сохраняем сообщение в локальную базу данных
        $messageRecord = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $manager->id,
            'content' => $message,
            'type' => 'text',
            'direction' => 'out',
            'metadata' => [
                'direction' => 'outgoing',
                'is_manager_message' => true,
                'manager_name' => $manager->name
            ]
        ]);
        
        // Обновляем время активности чата
        $chat->update(['last_activity_at' => now()]);
        
        Log::info("Сообщение менеджера сохранено локально", [
            'chat_id' => $chat->id,
            'message_id' => $messageRecord->id,
            'manager' => $manager->name
        ]);
        
        return $messageRecord;
    }

    /**
     * Уведомление отдела
     */
    protected function notifyDepartment($chat, $message)
    {
        $department = $chat->department;
        $users = $department->users;
        
        foreach ($users as $user) {
            // Здесь можно добавить уведомления (email, push, etc.)
            Log::info("Уведомление пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Уведомление назначенного пользователя
     */
    protected function notifyAssignedUser($chat, $message)
    {
        $user = $chat->assignedTo;
        if ($user) {
            Log::info("Уведомление назначенному пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Поиск или создание клиента
     */
    protected function findOrCreateClient($phone, $contactData = null)
    {
        $client = Client::where('phone', $phone)->first();
        
        if (!$client) {
            $client = Client::create([
                'name' => $contactData['name'] ?? 'Клиент ' . $phone,
                'phone' => $phone,
                'is_active' => true,
                'avatar' => $contactData['avatarUri'] ?? $contactData['avatar'] ?? null
            ]);
            
            Log::info('Создан новый клиент с контактами', [
                'client_id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'avatar' => $client->avatar,
                'contact_data' => $contactData
            ]);
        } else {
            // Обновляем данные клиента если они изменились
            $updated = false;
            $updates = [];
            
            if ($contactData && isset($contactData['name']) && $client->name !== $contactData['name']) {
                $updates['name'] = $contactData['name'];
                $updated = true;
            }
            
            if ($contactData && isset($contactData['avatarUri']) && $client->avatar !== $contactData['avatarUri']) {
                $updates['avatar'] = $contactData['avatarUri'];
                $updated = true;
            }
            
            if ($updated) {
                $client->update($updates);
                Log::info('Обновлены данные клиента', [
                    'client_id' => $client->id,
                    'updates' => $updates
                ]);
            }
        }
        
        return $client;
    }

    /**
     * Поиск или создание мессенджер чата
     */
    protected function findOrCreateMessengerChat($phone, $client)
    {
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();
        
        $isNewChat = false;
        
        if (!$chat) {
            $chat = Chat::create([
                'organization_id' => 1, // Используем ID 1 по умолчанию
                'title' => 'Мессенджер чат: ' . $phone,
                'type' => 'private', // Используем разрешенный тип
                'status' => 'active',
                'created_by' => 1, // Системный пользователь
                'is_messenger_chat' => true,
                'messenger_phone' => $phone,
                'messenger_status' => 'menu',
                'last_activity_at' => now()
            ]);
            $isNewChat = true;
        }
        
        // Примечание: меню будет отправлено в handleMenuMessage после обработки входящего сообщения
        
        return $chat;
    }

    /**
     * Передача чата другому отделу
     */
    public function transferToDepartment($chat, $newDepartmentId, $reason = null)
    {
        $newDepartment = Department::find($newDepartmentId);
        if (!$newDepartment) {
            return false;
        }
        
        $oldDepartment = $chat->department;
        
        $chat->update([
            'department_id' => $newDepartmentId,
            'assigned_to' => null, // Сбрасываем назначение
            'last_activity_at' => now()
        ]);
        
        // Отправляем уведомление клиенту
        $message = "Ваш диалог был перемещен в отдел {$newDepartment->name}";
        if ($reason) {
            $message .= ". Причина: {$reason}";
        }
        
        $this->sendMessage($chat, $message);
        
        // Сохраняем системное сообщение о передаче
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => null,
            'content' => "Чат передан из отдела '{$oldDepartment->name}' в отдел '{$newDepartment->name}'",
            'type' => 'system',
            'metadata' => [
                'transfer_reason' => $reason,
                'old_department' => $oldDepartment->name,
                'new_department' => $newDepartment->name
            ]
        ]);
        
        return true;
    }

    /**
     * Передача чата другому сотруднику
     */
    public function transferToUser($chat, $newUserId, $reason = null)
    {
        $newUser = User::find($newUserId);
        if (!$newUser) {
            return false;
        }
        
        $oldUser = $chat->assignedTo;
        
        $chat->update([
            'assigned_to' => $newUserId,
            'last_activity_at' => now()
        ]);
        
        // Сохраняем системное сообщение о передаче
        $message = "Чат передан от '{$oldUser->name}' к '{$newUser->name}'";
        if ($reason) {
            $message .= ". Причина: {$reason}";
        }
        
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => null,
            'content' => $message,
            'type' => 'system',
            'metadata' => [
                'transfer_reason' => $reason,
                'old_user' => $oldUser ? $oldUser->name : 'Не назначен',
                'new_user' => $newUser->name
            ]
        ]);
        
        return true;
    }

    /**
     * Завершение чата
     */
    public function completeChat($chat, $reason = null)
    {
        $chat->update([
            'messenger_status' => 'completed',
            'last_activity_at' => now()
        ]);
        
        $message = "Разговор завершен.";
        if ($reason) {
            $message .= " Причина: {$reason}";
        }
        $message .= "\n\n1 - Продолжить чат\n0 - Вернуться в главное меню";
        
        $this->sendMessage($chat, $message);
        
        // Сохраняем системное сообщение о завершении
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Системный пользователь
            'content' => "Чат завершен. Причина: " . ($reason ?: 'Не указана'),
            'type' => 'system',
            'metadata' => [
                'completion_reason' => $reason
            ]
        ]);
    }

    /**
     * Назначение менеджера на чат
     */
    public function assignManager($chat, $manager)
    {
        $chat->update([
            'assigned_to' => $manager->id,
            'last_activity_at' => now()
        ]);
        
        // Сохраняем системное сообщение о назначении
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Системный пользователь
            'content' => "Чат назначен менеджеру: {$manager->name}",
            'type' => 'system',
            'metadata' => [
                'assigned_manager' => $manager->name,
                'assigned_manager_id' => $manager->id
            ]
        ]);
        
        return true;
    }

    /**
     * Получение доступных отделов для передачи
     */
    public function getAvailableDepartments($currentDepartmentId = null)
    {
        $departments = Department::orderBy('name')->get();
        
        if ($currentDepartmentId) {
            $departments = $departments->filter(function($dept) use ($currentDepartmentId) {
                return $dept->id != $currentDepartmentId;
            });
        }
        
        return $departments;
    }

    /**
     * Получение доступных менеджеров для передачи
     */
    public function getAvailableManagers($currentUserId = null, $departmentId = null)
    {
        $query = User::where('role', 'manager')->orWhere('role', 'admin');
        
        if ($currentUserId) {
            $query->where('id', '!=', $currentUserId);
        }
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Передача чата в другой отдел с уведомлением клиента
     */
    public function transferToDepartmentWithNotification($chat, $newDepartmentId, $reason = null, $notifyClient = true)
    {
        $result = $this->transferToDepartment($chat, $newDepartmentId, $reason);
        
        if ($result && $notifyClient) {
            $newDepartment = Department::find($newDepartmentId);
            $message = "Ваш диалог был перемещен в отдел '{$newDepartment->name}'";
            if ($reason) {
                $message .= ". Причина: {$reason}";
            }
            $message .= "\n\nОжидайте ответа от специалистов отдела.";
            
            $this->sendMessage($chat, $message);
        }
        
        return $result;
    }

    /**
     * Передача чата другому менеджеру с уведомлением клиента
     */
    public function transferToUserWithNotification($chat, $newUserId, $reason = null, $notifyClient = true)
    {
        $result = $this->transferToUser($chat, $newUserId, $reason);
        
        if ($result && $notifyClient) {
            $newUser = User::find($newUserId);
            $message = "Ваш диалог был передан менеджеру '{$newUser->name}'";
            if ($reason) {
                $message .= ". Причина: {$reason}";
            }
            $message .= "\n\nОжидайте ответа.";
            
            $this->sendMessage($chat, $message);
        }
        
        return $result;
    }

    /**
     * Массовая передача чатов в отдел
     */
    public function bulkTransferToDepartment($chatIds, $newDepartmentId, $reason = null)
    {
        $results = [];
        $newDepartment = Department::find($newDepartmentId);
        
        foreach ($chatIds as $chatId) {
            $chat = Chat::find($chatId);
            if ($chat && $chat->is_messenger_chat) {
                $results[$chatId] = $this->transferToDepartmentWithNotification($chat, $newDepartmentId, $reason);
            }
        }
        
        return $results;
    }

    /**
     * Получение истории передач чата
     */
    public function getChatTransferHistory($chat)
    {
        return Message::where('chat_id', $chat->id)
            ->where('type', 'system')
            ->where('content', 'like', '%передан%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Автоматическое закрытие неактивных чатов
     */
    public function closeInactiveChats()
    {
        $inactiveDate = Carbon::now()->subDays(7);
        
        $inactiveChats = Chat::where('is_messenger_chat', true)
                            ->where('messenger_status', 'active')
                            ->where('last_activity_at', '<', $inactiveDate)
                            ->get();
        
        foreach ($inactiveChats as $chat) {
            $this->completeChat($chat, 'Автоматическое закрытие из-за неактивности');
        }
        
        return $inactiveChats->count();
    }
}
