<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Department;
use App\Models\User;
use App\Models\Client;
use App\Services\ChatHistoryService;
use App\Contracts\ChatStateManagerInterface;
use App\Contracts\ClientManagerInterface;
use App\Contracts\MessageProcessorInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MessengerServiceNew
{
    public function __construct(
        private ChatStateManagerInterface $chatStateManager,
        private ClientManagerInterface $clientManager,
        private MessageProcessorInterface $messageProcessor
    ) {
        // Теперь используем внедренные зависимости
    }

    /**
     * Обработка входящего сообщения в мессенджере
     */
    public function handleIncomingMessage($phone, $message, $contactData = null, $organization = null, $wazzupMessageId = null)
    {
        try {
            Log::info('Processing message', [
                'phone' => $phone,
                'message_length' => strlen($message)
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client, $organization);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Обрабатываем сообщение в зависимости от статуса
            // (сохранение сообщения происходит внутри processMessage)
            $this->chatStateManager->processMessage($chat, $message, $client, $wazzupMessageId);

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
     * Обработка входящего изображения в мессенджере
     */
    public function handleIncomingImage($phone, $imageUrl, $caption = '', $contactData = null, $organization = null, $wazzupMessageId = null)
    {
        try {
            Log::info('Processing image', [
                'phone' => $phone,
                'image_url' => $imageUrl,
                'caption' => $caption
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client, $organization);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем изображение
            $this->messageProcessor->saveClientImage($chat, $imageUrl, $caption, $client, $wazzupMessageId);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingImage:', [
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
     * Обработка входящего видео
     */
    public function handleIncomingVideo($phone, $videoUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing video', [
                'phone' => $phone,
                'video_url' => $videoUrl,
                'caption' => $caption
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем видео
            $this->messageProcessor->saveClientVideo($chat, $videoUrl, $caption, $client);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingVideo:', [
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
     * Обработка входящего стикера
     */
    public function handleIncomingSticker($phone, $stickerUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing sticker', [
                'phone' => $phone,
                'sticker_url' => $stickerUrl,
                'caption' => $caption
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем стикер
            $this->messageProcessor->saveClientSticker($chat, $stickerUrl, $caption, $client);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingSticker:', [
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
     * Обработка входящего аудио
     */
    public function handleIncomingAudio($phone, $audioUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing audio', [
                'phone' => $phone,
                'audio_url' => $audioUrl,
                'caption' => $caption
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем аудио
            $this->messageProcessor->saveClientAudio($chat, $audioUrl, $caption, $client);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingAudio:', [
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
     * Обработка входящего документа
     */
    public function handleIncomingDocument($phone, $documentUrl, $documentName, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing document', [
                'phone' => $phone,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'caption' => $caption
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем документ
            $this->messageProcessor->saveClientDocument($chat, $documentUrl, $documentName, $caption, $client);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingDocument:', [
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
     * Обработка входящей локации
     */
    public function handleIncomingLocation($phone, $latitude, $longitude, $address = '', $contactData = null)
    {
        try {
            Log::info('Processing location', [
                'phone' => $phone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address
            ]);

            // Находим или создаем клиента с контактами
            $client = $this->clientManager->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);

            // Находим или создаем чат
            $chat = $this->clientManager->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Сохраняем локацию
            $this->messageProcessor->saveClientLocation($chat, $latitude, $longitude, $address, $client);

            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->chatStateManager->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }

            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error in handleIncomingLocation:', [
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
     * Отправка сообщения от менеджера клиенту
     */
    public function sendManagerMessage($chat, $message, $manager)
    {
        try {
            // Автоматически назначаем чат сотруднику, если он еще не назначен
            if (!$chat->assigned_to) {
                $chat->update([
                    'assigned_to' => $manager->id,
                    'last_activity_at' => now()
                ]);

                // Логируем назначение менеджера
                $historyService = app(ChatHistoryService::class);
                $historyService->logManagerAssignment($chat, $manager);

                Log::info("Chat auto-assigned to manager", [
                    'chat_id' => $chat->id,
                    'manager_id' => $manager->id,
                    'manager_name' => $manager->name
                ]);
            } else {
                // Обновляем только время активности
                $chat->update(['last_activity_at' => now()]);
            }

            // Формируем сообщение с именем сотрудника жирным шрифтом
            $formattedMessage = "**{$manager->name}**\n{$message}";

            // Сначала сохраняем сообщение в локальную базу данных
            $messageRecord = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $manager->id,
                'content' => $formattedMessage, // Сохраняем отформатированное сообщение
                'type' => 'text',
                'direction' => 'out',
                'metadata' => [
                    'direction' => 'outgoing',
                    'is_manager_message' => true,
                    'manager_name' => $manager->name,
                    'original_message' => $message // Сохраняем оригинальное сообщение
                ]
            ]);

            Log::info("Manager message saved", [
                'chat_id' => $chat->id,
                'message_id' => $messageRecord->id
            ]);

            // Отправляем сообщение через Wazzup24
            if (class_exists('\App\Services\Wazzup24Service')) {
                $wazzupService = app('\App\Services\Wazzup24Service');

                // Получаем данные для отправки
                $channelId = config('services.wazzup24.channel_id');
                $chatType = 'whatsapp';
                $chatId = $chat->messenger_phone;

                // Добавляем заголовок "Система" для сообщений менеджера
                $systemFormattedMessage = "*Система*\n\n" . $formattedMessage;

                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $systemFormattedMessage, // Отправляем сообщение с заголовком "Система"
                    $manager->id,
                    $messageRecord->id
                );

                if ($result['success']) {
                    // Обновляем сообщение с ID от Wazzup24
                    $messageRecord->update([
                        'wazzup_message_id' => $result['message_id'] ?? null,
                        'metadata' => array_merge($messageRecord->metadata ?? [], [
                            'wazzup_sent' => true,
                            'wazzup_message_id' => $result['message_id'] ?? null
                        ])
                    ]);

                    Log::info("Message sent via Wazzup24", [
                        'chat_id' => $chat->id,
                        'wazzup_id' => $result['message_id'] ?? null
                    ]);
                } else {
                    Log::error("Wazzup24 send error", [
                        'chat_id' => $chat->id,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::warning("Wazzup24Service не найден, сообщение сохранено только локально", [
                    'chat_id' => $chat->id,
                    'message_id' => $messageRecord->id
                ]);
            }

            return $messageRecord;

        } catch (\Exception $e) {
            Log::error("Ошибка в sendManagerMessage: " . $e->getMessage(), [
                'chat_id' => $chat->id,
                'manager' => $manager->name,
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
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

        $this->messageProcessor->sendMessage($chat, $message);

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
                'old_user' => $oldUser->name,
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

        // Сохраняем системное сообщение о завершении
        $message = "Чат завершен";
        if ($reason) {
            $message .= ". Причина: {$reason}";
        }

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => null,
            'content' => $message,
            'type' => 'system',
            'metadata' => [
                'completion_reason' => $reason
            ]
        ]);

        return true;
    }

    /**
     * Назначение менеджера
     */
    public function assignManager($chat, $manager)
    {
        $chat->update([
            'assigned_to' => $manager->id,
            'last_activity_at' => now()
        ]);

        // Логируем назначение менеджера
        $historyService = app(ChatHistoryService::class);
        $historyService->logManagerAssignment($chat, $manager);

        return true;
    }

    /**
     * Получение доступных отделов
     */
    public function getAvailableDepartments($currentDepartmentId = null)
    {
        $query = Department::where('is_active', true);
        
        if ($currentDepartmentId) {
            $query->where('id', '!=', $currentDepartmentId);
        }
        
        return $query->get();
    }

    /**
     * Получение доступных менеджеров
     */
    public function getAvailableManagers($currentUserId = null, $departmentId = null)
    {
        $query = User::where('is_active', true);
        
        if ($currentUserId) {
            $query->where('id', '!=', $currentUserId);
        }
        
        if ($departmentId) {
            $query->whereHas('departments', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }
        
        return $query->get();
    }

    /**
     * Передача чата в отдел с уведомлением
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
            $this->messageProcessor->sendMessage($chat, $message);
        }

        return $result;
    }

    /**
     * Передача чата пользователю с уведомлением
     */
    public function transferToUserWithNotification($chat, $newUserId, $reason = null, $notifyClient = true)
    {
        $result = $this->transferToUser($chat, $newUserId, $reason);

        if ($result && $notifyClient) {
            $newUser = User::find($newUserId);
            $message = "Ваш диалог был передан специалисту '{$newUser->name}'";
            if ($reason) {
                $message .= ". Причина: {$reason}";
            }
            $message .= "\n\nОжидайте ответа от специалиста.";
            $this->messageProcessor->sendMessage($chat, $message);
        }

        return $result;
    }

    /**
     * Массовая передача чатов в отдел
     */
    public function bulkTransferToDepartment($chatIds, $newDepartmentId, $reason = null)
    {
        $results = [];
        
        foreach ($chatIds as $chatId) {
            $chat = Chat::find($chatId);
            if ($chat) {
                $results[$chatId] = $this->transferToDepartment($chat, $newDepartmentId, $reason);
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
            ->where('metadata->transfer_reason', '!=', null)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Закрытие неактивных чатов
     */
    public function closeInactiveChats()
    {
        $inactiveThreshold = now()->subHours(24);
        
        $chats = Chat::where('is_messenger_chat', true)
            ->where('messenger_status', 'active')
            ->where('last_activity_at', '<', $inactiveThreshold)
            ->get();
        
        foreach ($chats as $chat) {
            $this->closeChat($chat->id, null, 'Автоматическое закрытие неактивного чата');
        }
        
        return $chats->count();
    }

    /**
     * Закрытие чата
     */
    public function closeChat($chatId, $managerId, $reason = 'Чат закрыт менеджером')
    {
        $chat = Chat::find($chatId);
        if (!$chat) {
            return ['success' => false, 'error' => 'Chat not found'];
        }

        // Обновляем статус чата
        $chat->update([
            'messenger_status' => 'closed',
            'status' => 'closed', // Для основного статуса чата
            'last_activity_at' => now()
        ]);

        // Отправляем сообщение клиенту о закрытии чата напрямую через Wazzup
        $clientMessage = "Ваш чат был закрыт.\nДля восстановления общение с менеджером нажмите 1\nдля возврата в главное меню нажмите 0";
        
        if (class_exists('\App\Services\Wazzup24Service') && $chat->messenger_phone) {
            try {
                $wazzupService = app('\App\Services\Wazzup24Service');
                
                // Получаем данные для отправки
                $channelId = config('services.wazzup24.channel_id');
                $chatType = 'whatsapp';
                $chatIdWazzup = $chat->messenger_phone;

                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatIdWazzup,
                    $clientMessage,
                    $managerId,
                    null
                );

                if ($result['success']) {
                    Log::info('Closure message sent to client via Wazzup24', [
                        'chat_id' => $chatId,
                        'wazzup_id' => $result['message_id'] ?? null
                    ]);
                } else {
                    Log::error('Failed to send closure message via Wazzup24', [
                        'chat_id' => $chatId,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Exception sending closure message via Wazzup24', [
                    'chat_id' => $chatId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Сохраняем системное сообщение о закрытии
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $managerId,
            'content' => "Чат закрыт. Причина: {$reason}",
            'type' => 'system',
            'metadata' => [
                'closure_reason' => $reason,
                'closed_by' => $managerId
            ]
        ]);

        Log::info('Chat closed by manager', [
            'chat_id' => $chatId,
            'manager_id' => $managerId,
            'reason' => $reason
        ]);

        return ['success' => true, 'message' => 'Chat closed successfully'];
    }

    /**
     * Публикация сообщения в Redis для SSE
     */
    public function publishMessageToRedis(int $chatId, Message $message): void
    {
        try {
            // Загружаем пользователя для сообщения
            $message->load('user');

            // Публикуем в канал чата
            $redis = app('redis');
            $channel = "chat:{$chatId}";
            $data = [
                'type' => 'message',
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'is_from_client' => $message->is_from_client,
                    'created_at' => $message->created_at->toISOString(),
                    'user' => $message->user ? [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar' => $message->user->avatar
                    ] : null,
                    'metadata' => $message->metadata
                ]
            ];
            
            $redis->publish($channel, json_encode($data));

            // Если это сообщение от клиента, отправляем глобальные события
            if ($message->is_from_client) {
                // Отправляем в глобальный канал чатов
                $globalData = [
                    'type' => 'new_message',
                    'chat_id' => $chatId,
                    'message_count' => 1
                ];
                
                Redis::lpush('sse_queue:chats.global', json_encode($globalData));
                Redis::expire('sse_queue:chats.global', 3600);

                // Отправляем в канал организации
                if ($message->chat->organization_id) {
                    Redis::lpush('sse_queue:organization.' . $message->chat->organization_id . '.chats', json_encode($globalData));
                    Redis::expire('sse_queue:organization.' . $message->chat->organization_id . '.chats', 3600);
                }
            }

            Log::info('📡 Messenger message published to Redis', [
                'chat_id' => $chatId,
                'message_id' => $message->id,
                'channel' => 'chat.' . $chatId,
                'is_from_client' => $message->is_from_client,
                'global_events_sent' => $message->is_from_client
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to publish messenger message to Redis', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'message_id' => $message->id
            ]);
        }
    }
}
