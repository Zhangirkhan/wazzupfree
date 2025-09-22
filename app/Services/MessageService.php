<?php

namespace App\Services;

use App\Contracts\MessageServiceInterface;
use App\Events\MessageSent;
use App\Exceptions\ChatNotFoundException;
use App\Exceptions\FileUploadException;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class MessageService implements MessageServiceInterface
{
    public function sendMessage(string $chatId, string $message, User $user, string $type = 'text', ?UploadedFile $file = null): Message
    {
        Log::info('🔹 БЭК: MessageService::sendMessage вызван', [
            'chat_id' => $chatId,
            'user_id' => $user->id,
            'message' => $message,
            'type' => $type,
            'has_file' => $file !== null,
            'file_info' => $file ? [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ] : null
        ]);

        $chat = $this->validateChatAccess($chatId, $user);
        Log::info('🔹 БЭК: Доступ к чату проверен', ['chat_id' => $chat->id]);

        $messageData = [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $message ?: ($file ? $file->getClientOriginalName() : ''),
            'type' => $type,
            'direction' => 'out'
        ];

        Log::info('🔹 БЭК: Данные сообщения подготовлены', $messageData);

        // Обработка файлов
        if ($file) {
            Log::info('🔹 БЭК: Начинаем обработку файла');
            try {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chat_files', $filename, 'public');

                Log::info('🔹 БЭК: Файл сохранен', [
                    'filename' => $filename,
                    'path' => $path,
                    'full_url' => config('app.url') . '/storage/' . $path
                ]);

                $messageData['metadata'] = [
                    'file_path' => config('app.url') . '/storage/' . $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];

                Log::info('🔹 БЭК: Метаданные файла подготовлены', $messageData['metadata']);
            } catch (\Exception $e) {
                Log::error('🔸 БЭК: Ошибка загрузки файла', [
                    'error' => $e->getMessage(),
                    'file_name' => $file->getClientOriginalName()
                ]);
                throw new FileUploadException('Failed to upload file: ' . $e->getMessage());
            }
        }

        Log::info('🔹 БЭК: Создаем сообщение в БД', $messageData);
        $message = Message::create($messageData);
        Log::info('🔹 БЭК: Сообщение создано', [
            'message_id' => $message->id,
            'created_at' => $message->created_at
        ]);

        // Обновляем время последнего сообщения в чате
        $chat->update(['updated_at' => now()]);

        // Отправляем сообщение через Redis Pub/Sub
        Log::info('🔹 БЭК: Отправляем сообщение через Redis Pub/Sub');
        $this->publishToRedis($chatId, $message->load('user'));

        // Отправляем глобальные уведомления всем участникам чата
        Log::info('🔹 БЭК: Отправляем глобальные уведомления');
        $this->sendGlobalNotifications($chatId, $message->load('user'));

        // Отправляем сообщение через Wazzup24 (для мессенджер чатов)
        if ($chat->is_messenger_chat && $chat->messenger_phone) {
            Log::info('🔹 БЭК: Отправляем сообщение через Wazzup24', [
                'chat_id' => $chatId,
                'messenger_phone' => $chat->messenger_phone,
                'organization_id' => $chat->organization_id
            ]);
            $this->sendMessageViaWazzup24($chat, $message, $user);
        }

        Log::info('🔹 БЭК: Сообщение полностью обработано', [
            'message_id' => $message->id,
            'chat_id' => $chatId,
            'user_id' => $user->id,
            'type' => $type,
            'has_file' => $file !== null,
            'content' => $message->content,
            'metadata' => $message->metadata
        ]);

        return $message->load('user');
    }

    public function getChatMessages(string $chatId, User $user, int $perPage = 50): LengthAwarePaginator
    {
        $this->validateChatAccess($chatId, $user);

        return Message::where('chat_id', $chatId)
            ->with('user')
            ->orderBy('created_at', 'desc') // Сортируем по времени с поддержкой миллисекунд
            ->paginate($perPage);
    }

    public function sendSystemMessage(string $chatId, string $message, User $user): Message
    {
        $chat = $this->validateChatAccess($chatId, $user);

        return Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $message,
            'type' => 'system',
            'direction' => 'out'
        ]);
    }

    private function validateChatAccess(string $chatId, User $user): Chat
    {
        // Администратор может получить доступ к любому чату
        if ($user->role === 'admin') {
            $chat = Chat::where('id', $chatId)->first();
        } else {
            // Обычные пользователи могут получить доступ только к своим чатам
            $chat = Chat::where('id', $chatId)
                ->where(function($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhere('assigned_to', $user->id);
                })
                ->first();
        }

        if (!$chat) {
            Log::warning('Chat access denied', ['chat_id' => $chatId, 'user_id' => $user->id]);
            throw new ChatNotFoundException();
        }

        return $chat;
    }

    /**
     * Отправка сообщения через Redis Pub/Sub
     */
    private function publishToRedis(string $chatId, Message $message): void
    {
        try {
            $data = [
                'type' => 'new_message',
                'chatId' => $chatId,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->content, // Для совместимости с фронтендом
                    'content' => $message->content,
                    'type' => $message->type,
                    'is_from_client' => $message->direction === 'in',
                    'is_read' => false, // Новое сообщение всегда непрочитано
                    'read_at' => null,
                    'file_path' => $message->metadata['file_path'] ?? null,
                    'file_name' => $message->metadata['file_name'] ?? null,
                    'file_size' => $message->metadata['file_size'] ?? null,
                    'created_at' => $message->created_at->toISOString(),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'email' => $message->user->email,
                        'role' => $message->user->role,
                    ],
                ],
                'timestamp' => now()->toISOString()
            ];

            // Публикуем в Redis канал чата
            Redis::publish('chat.' . $chatId, json_encode($data));

            Log::info('Message published to Redis', [
                'chat_id' => $chatId,
                'message_id' => $message->id,
                'channel' => 'chat.' . $chatId
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to publish message to Redis', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'message_id' => $message->id
            ]);
        }
    }

    /**
     * Отправка глобальных уведомлений о новом сообщении
     */
    private function sendGlobalNotifications(string $chatId, Message $message): void
    {
        try {
            // Получаем всех участников чата (кроме отправителя)
            $chat = Chat::find($chatId);
            if (!$chat) {
                return;
            }

            // Определяем получателей уведомления
            $recipients = [];

            // Если есть назначенные пользователи, используем их
            if ($chat->assigned_user_id && $chat->assigned_user_id !== $message->user_id) {
                $recipients[] = $chat->assigned_user_id;
            }

            if ($chat->user_id &&
                $chat->user_id !== $message->user_id &&
                $chat->user_id !== $chat->assigned_user_id) {
                $recipients[] = $chat->user_id;
            }

            // Для многопользовательского чата отправляем уведомления всем активным пользователям
            // (кроме отправителя), независимо от назначения в чате
            $allUsers = \App\Models\User::where('id', '!=', $message->user_id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();

            // Объединяем назначенных пользователей и всех активных
            $recipients = array_unique(array_merge($recipients, $allUsers));

            // Отправляем уведомления каждому получателю
            foreach (array_unique($recipients) as $userId) {
                $notificationData = [
                    'type' => 'unread_message',
                    'chat_id' => $chatId,
                    'chat_name' => $chat->client_name,
                    'message' => [
                        'id' => $message->id,
                        'content' => $message->content,
                        'created_at' => $message->created_at->toISOString(),
                        'user' => [
                            'id' => $message->user->id,
                            'name' => $message->user->name,
                        ]
                    ],
                    'unread_count' => $this->getUnreadCount($chatId, $userId),
                    'timestamp' => now()->toISOString()
                ];

                // Публикуем в персональный канал пользователя
                Redis::publish('user.' . $userId . '.notifications', json_encode($notificationData));
            }

            Log::info('Global notifications sent', [
                'chat_id' => $chatId,
                'message_id' => $message->id,
                'recipients' => $recipients
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send global notifications', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'message_id' => $message->id
            ]);
        }
    }

    /**
     * Получить количество непрочитанных сообщений в чате для пользователя
     */
    private function getUnreadCount(string $chatId, int $userId): int
    {
        try {
            return Message::where('chat_id', $chatId)
                ->where('user_id', '!=', $userId)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread count', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'user_id' => $userId
            ]);
            return 0;
        }
    }

    /**
     * Отправка сообщения через Wazzup24
     */
    private function sendMessageViaWazzup24(Chat $chat, Message $message, User $user): void
    {
        try {
            // Проверяем, настроен ли Wazzup24 для организации
            if (!class_exists('\App\Services\Wazzup24Service') || !$chat->organization) {
                Log::warning('Wazzup24Service не найден или организация не найдена', [
                    'chat_id' => $chat->id,
                    'organization_id' => $chat->organization_id
                ]);
                return;
            }

            // Проверяем настройки организации
            if (!$chat->organization->isWazzup24Configured()) {
                Log::warning('Wazzup24 не настроен для организации', [
                    'chat_id' => $chat->id,
                    'organization_id' => $chat->organization_id,
                    'organization_name' => $chat->organization->name
                ]);
                return;
            }

            // Создаем сервис для организации
            $wazzupService = \App\Services\Wazzup24Service::forOrganization($chat->organization);

            // Получаем данные для отправки
            $channelId = $wazzupService->getChannelId();
            $chatType = 'whatsapp';
            $chatId = $chat->messenger_phone;

            // Проверяем тип сообщения и отправляем соответствующим методом
            if ($message->type === 'image' && isset($message->metadata['file_path'])) {
                // Отправляем изображение
                $imageUrl = $message->metadata['file_path'];

                Log::info('🔹 БЭК: Отправляем изображение через Wazzup24 API', [
                    'channel_id' => $channelId,
                    'chat_type' => $chatType,
                    'chat_id' => $chatId,
                    'image_url' => $imageUrl,
                    'user_name' => $user->name
                ]);

                $result = $wazzupService->sendImage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $imageUrl,
                    null, // Не отправляем подпись с изображением
                    $user->id,
                    $message->id
                );

                // Если изображение отправилось успешно и есть подпись, отправляем её отдельным сообщением
                if ($result['success'] && !empty($message->content)) {
                    $caption = "*{$user->name}*\n\n{$message->content}";
                    
                    Log::info('🔹 БЭК: Отправляем подпись к изображению', [
                        'caption' => $caption
                    ]);
                    
                    $captionResult = $wazzupService->sendMessage(
                        $channelId,
                        $chatType,
                        $chatId,
                        $caption,
                        $user->id,
                        $message->id
                    );
                    
                    if (!$captionResult['success']) {
                        Log::warning('Не удалось отправить подпись к изображению', [
                            'error' => $captionResult['error']
                        ]);
                    }
                }
            } elseif ($message->type === 'video' && isset($message->metadata['file_path'])) {
                // Отправляем видео
                $videoUrl = $message->metadata['file_path'];

                Log::info('🔹 БЭК: Отправляем видео через Wazzup24 API', [
                    'channel_id' => $channelId,
                    'chat_type' => $chatType,
                    'chat_id' => $chatId,
                    'video_url' => $videoUrl,
                    'user_name' => $user->name
                ]);

                $result = $wazzupService->sendVideo(
                    $channelId,
                    $chatType,
                    $chatId,
                    $videoUrl,
                    null, // Не отправляем подпись с видео
                    $user->id,
                    $message->id
                );

                // Если видео отправилось успешно и есть подпись, отправляем её отдельным сообщением
                if ($result['success'] && !empty($message->content)) {
                    $caption = "*{$user->name}*\n\n{$message->content}";
                    
                    Log::info('🔹 БЭК: Отправляем подпись к видео', [
                        'caption' => $caption
                    ]);
                    
                    $captionResult = $wazzupService->sendMessage(
                        $channelId,
                        $chatType,
                        $chatId,
                        $caption,
                        $user->id,
                        $message->id
                    );
                    
                    if (!$captionResult['success']) {
                        Log::warning('Не удалось отправить подпись к видео', [
                            'error' => $captionResult['error']
                        ]);
                    }
                }
            } else {
                // Отправляем текстовое сообщение
                $formattedMessage = "*{$user->name}*\n\n{$message->content}";

                Log::info('🔹 БЭК: Отправляем сообщение через Wazzup24 API', [
                    'channel_id' => $channelId,
                    'chat_type' => $chatType,
                    'chat_id' => $chatId,
                    'message_length' => strlen($formattedMessage),
                    'user_name' => $user->name
                ]);

                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $formattedMessage,
                    $user->id,
                    $message->id
                );
            }

            if ($result['success']) {
                // Обновляем сообщение с ID от Wazzup24
                $message->update([
                    'wazzup_message_id' => $result['message_id'] ?? null,
                    'metadata' => array_merge($message->metadata ?? [], [
                        'wazzup_sent' => true,
                        'wazzup_message_id' => $result['message_id'] ?? null,
                        'sent_via' => 'wazzup24'
                    ])
                ]);

                Log::info('✅ Сообщение успешно отправлено через Wazzup24', [
                    'chat_id' => $chat->id,
                    'message_id' => $message->id,
                    'wazzup_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('❌ Ошибка отправки сообщения через Wazzup24', [
                    'chat_id' => $chat->id,
                    'message_id' => $message->id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);

                // Обновляем сообщение с информацией об ошибке
                $message->update([
                    'metadata' => array_merge($message->metadata ?? [], [
                        'wazzup_sent' => false,
                        'wazzup_error' => $result['error'] ?? 'Unknown error',
                        'sent_via' => 'failed'
                    ])
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Исключение при отправке сообщения через Wazzup24', [
                'chat_id' => $chat->id,
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Обновляем сообщение с информацией об исключении
            try {
                $message->update([
                    'metadata' => array_merge($message->metadata ?? [], [
                        'wazzup_sent' => false,
                        'wazzup_error' => $e->getMessage(),
                        'sent_via' => 'exception'
                    ])
                ]);
            } catch (\Exception $updateException) {
                Log::error('Не удалось обновить сообщение с ошибкой Wazzup24', [
                    'message_id' => $message->id,
                    'update_error' => $updateException->getMessage()
                ]);
            }
        }
    }
}
