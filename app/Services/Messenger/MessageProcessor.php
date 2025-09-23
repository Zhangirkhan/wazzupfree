<?php

namespace App\Services\Messenger;

use App\Contracts\MessageProcessorInterface;
use App\Models\Chat;
use App\Models\Client;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MessageProcessor implements MessageProcessorInterface
{
    /**
     * Сохранение текстового сообщения от клиента
     */
    public function saveClientMessage(Chat $chat, string $message, Client $client, ?string $wazzupMessageId = null): Message
    {
        Log::info('💬 Saving client message', [
            'chat_id' => $chat->id,
            'client_id' => $client->id,
            'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
            'phone' => $chat->messenger_phone
        ]);

        $metadata = [
            'original_message' => $message,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'direction' => 'incoming'
        ];

        // Добавляем wazzup_message_id если есть
        if ($wazzupMessageId) {
            $metadata['wazzup_message_id'] = $wazzupMessageId;
        }

        $messageRecord = Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Используем системного пользователя для всех сообщений
            'content' => $message,
            'type' => 'text',
            'is_from_client' => true, // Это сообщение от клиента
            'messenger_message_id' => 'client_' . time() . '_' . rand(1000, 9999),
            'metadata' => $metadata
        ]);

        Log::info('✅ Client message saved', [
            'message_id' => $messageRecord->id,
            'chat_id' => $chat->id
        ]);

        // Отправляем событие в Redis для SSE
        $this->publishMessageToRedis($chat->id, $messageRecord);

        return $messageRecord;
    }

    /**
     * Сохранение изображения от клиента
     */
    public function saveClientImage(Chat $chat, string $imageUrl, string $caption, Client $client, ?string $wazzupMessageId = null): Message
    {
        try {
            // Используем ImageService для сохранения изображения
            $imageService = app(\App\Services\ImageService::class);
            $imageData = $imageService->saveImageFromUrl($imageUrl, $chat->id);

            if (!$imageData) {
                Log::error('Failed to save image', [
                    'chat_id' => $chat->id,
                    'image_url' => $imageUrl
                ]);
                throw new \Exception('Failed to save image');
            }

            // Создаем сообщение с изображением
            $messageContent = !empty($caption) ? $caption : 'Изображение';

            $metadata = [
                'image_url' => $imageData['url'],
                'image_path' => $imageData['path'],
                'image_filename' => $imageData['filename'],
                'image_size' => $imageData['size'],
                'image_extension' => $imageData['extension'],
                'original_url' => $imageUrl,
                'caption' => $caption,
                'client_id' => $client->id,
                'client_name' => $client->name,
                'direction' => 'incoming'
            ];

            // Добавляем wazzup_message_id если есть
            if ($wazzupMessageId) {
                $metadata['wazzup_message_id'] = $wazzupMessageId;
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'image',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => $metadata
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client image saved successfully', [
                'chat_id' => $chat->id,
                'image_url' => $imageData['url'],
                'caption' => $caption
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client image', [
                'chat_id' => $chat->id,
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Сохранение видео от клиента
     */
    public function saveClientVideo(Chat $chat, string $videoUrl, string $caption, Client $client): Message
    {
        try {
            // Используем VideoService для сохранения видео
            $videoService = app(\App\Services\VideoService::class);
            $videoData = $videoService->saveVideoFromUrl($videoUrl, $chat->id);

            if (!$videoData) {
                Log::error('Failed to save video', [
                    'chat_id' => $chat->id,
                    'video_url' => $videoUrl
                ]);
                throw new \Exception('Failed to save video');
            }

            // Создаем сообщение с видео
            $messageContent = !empty($caption) ? $caption : 'Видео';

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'video',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => [
                    'video_url' => $videoData['url'],
                    'video_path' => $videoData['path'],
                    'video_filename' => $videoData['filename'],
                    'video_size' => $videoData['size'],
                    'video_extension' => $videoData['extension'],
                    'original_url' => $videoUrl,
                    'caption' => $caption,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client video saved successfully', [
                'chat_id' => $chat->id,
                'video_url' => $videoData['url'],
                'caption' => $caption
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client video', [
                'chat_id' => $chat->id,
                'video_url' => $videoUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Сохранение аудио от клиента
     */
    public function saveClientAudio(Chat $chat, string $audioUrl, string $caption, Client $client): Message
    {
        try {
            // Создаем сообщение с аудио (пока что без специального сервиса)
            $messageContent = !empty($caption) ? $caption : 'Аудио сообщение';

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'audio',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => [
                    'audio_url' => $audioUrl,
                    'original_url' => $audioUrl,
                    'caption' => $caption,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client audio saved successfully', [
                'chat_id' => $chat->id,
                'audio_url' => $audioUrl,
                'caption' => $caption
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client audio', [
                'chat_id' => $chat->id,
                'audio_url' => $audioUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Сохранение стикера от клиента
     */
    public function saveClientSticker(Chat $chat, string $stickerUrl, string $caption, Client $client): Message
    {
        try {
            // Создаем сообщение со стикером
            $messageContent = !empty($caption) ? $caption : 'Стикер';

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'sticker',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => [
                    'sticker_url' => $stickerUrl,
                    'original_url' => $stickerUrl,
                    'caption' => $caption,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client sticker saved successfully', [
                'chat_id' => $chat->id,
                'sticker_url' => $stickerUrl,
                'caption' => $caption
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client sticker', [
                'chat_id' => $chat->id,
                'sticker_url' => $stickerUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Сохранение документа от клиента
     */
    public function saveClientDocument(Chat $chat, string $documentUrl, string $documentName, string $caption, Client $client): Message
    {
        try {
            // Создаем сообщение с документом
            $messageContent = !empty($caption) ? $caption : 'Документ: ' . $documentName;

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'document',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => [
                    'document_url' => $documentUrl,
                    'document_name' => $documentName,
                    'original_url' => $documentUrl,
                    'caption' => $caption,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client document saved successfully', [
                'chat_id' => $chat->id,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'caption' => $caption
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client document', [
                'chat_id' => $chat->id,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Сохранение геолокации от клиента
     */
    public function saveClientLocation(Chat $chat, float $latitude, float $longitude, string $address, Client $client): Message
    {
        try {
            // Создаем сообщение с геолокацией
            $messageContent = !empty($address) ? $address : "Геолокация: {$latitude}, {$longitude}";

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'location',
                'is_from_client' => true, // Это сообщение от клиента
                'metadata' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $message);

            Log::info('Client location saved successfully', [
                'chat_id' => $chat->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error saving client location', [
                'chat_id' => $chat->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Отправка сообщения в чат
     */
    public function sendMessage(Chat $chat, string $message): void
    {
        try {
            // Сохраняем в базу как системное сообщение с временной меткой на 100 миллисекунд позже
            // Получаем время последнего сообщения и добавляем 100ms для правильного порядка
            $lastMessage = Message::where('chat_id', $chat->id)->orderBy('created_at', 'desc')->first();
            $systemMessageTime = $lastMessage ?
                $lastMessage->created_at->addMilliseconds(200) :
                now()->addMilliseconds(200);

            // Создаем сообщение с точным временем через DB::table для обхода автоматических timestamps
            $messageId = DB::table('messages')->insertGetId([
                'chat_id' => $chat->id,
                'user_id' => 1, // Системный пользователь
                'content' => $message,
                'type' => 'system', // Системное сообщение от бота
                'is_from_client' => false, // Это сообщение от бота
                'messenger_message_id' => 'bot_' . time() . '_' . rand(1000, 9999),
                'created_at' => $systemMessageTime->format('Y-m-d H:i:s.v'),
                'updated_at' => $systemMessageTime->format('Y-m-d H:i:s.v'),
                'metadata' => json_encode([
                    'direction' => 'outgoing',
                    'is_bot_message' => true,
                    'sender' => 'Система'
                ])
            ]);

            // Получаем созданное сообщение
            $messageRecord = Message::find($messageId);

            Log::info("System message saved", ['chat_id' => $chat->id]);

            // Отправляем системное сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $messageRecord);

            // Отправляем системное сообщение через Wazzup24
            if (class_exists('\App\Services\Wazzup24Service') && $chat->organization) {
                try {
                    $wazzupService = \App\Services\Wazzup24Service::forOrganization($chat->organization);

                    // Получаем данные для отправки
                    $channelId = $wazzupService->getChannelId();
                    $chatType = 'whatsapp';
                    $chatId = $chat->messenger_phone;

                    // Форматируем сообщение для WhatsApp с жирной надписью "Система"
                    $formattedMessage = "*Система*\n\n" . $message;

                    $result = $wazzupService->sendMessage(
                        $channelId,
                        $chatType,
                        $chatId,
                        $formattedMessage,
                        1, // Системный пользователь
                        $messageRecord->id
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to create Wazzup24Service for organization', [
                        'chat_id' => $chat->id,
                        'organization_id' => $chat->organization_id,
                        'error' => $e->getMessage()
                    ]);
                    $result = ['success' => false, 'error' => $e->getMessage()];
                }

                if ($result['success']) {
                    // Обновляем сообщение с ID от Wazzup24
                    $messageRecord->update([
                        'wazzup_message_id' => $result['message_id'] ?? null,
                        'metadata' => array_merge($messageRecord->metadata ?? [], [
                            'wazzup_sent' => true,
                            'wazzup_message_id' => $result['message_id'] ?? null
                        ])
                    ]);

                    Log::info("System message sent via Wazzup24", [
                        'chat_id' => $chat->id,
                        'wazzup_id' => $result['message_id'] ?? null
                    ]);
                } else {
                    Log::error("Ошибка отправки системного сообщения через Wazzup24", [
                        'chat_id' => $chat->id,
                        'message_id' => $messageRecord->id,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::warning("Wazzup24Service не найден, системное сообщение сохранено только локально", [
                    'chat_id' => $chat->id,
                    'message_id' => $messageRecord->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Ошибка в sendMessage: " . $e->getMessage(), [
                'chat_id' => $chat->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Уведомление отдела о новом сообщении
     */
    public function notifyDepartment(Chat $chat, string $message): void
    {
        $department = $chat->department;

        if (!$department) {
            Log::warning("Отдел не найден для чата {$chat->id}");
            return;
        }

        $users = $department->users;

        if ($users->isEmpty()) {
            Log::warning("В отделе {$department->name} нет пользователей для уведомления");
            return;
        }

        foreach ($users as $user) {
            // Здесь можно добавить уведомления (email, push, etc.)
            Log::info("Уведомление пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Уведомление назначенного пользователя
     */
    public function notifyAssignedUser(Chat $chat, string $message): void
    {
        $user = $chat->assignedTo;
        if ($user) {
            Log::info("Уведомление назначенному пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Публикация сообщения в Redis для SSE
     */
    private function publishMessageToRedis(int $chatId, Message $message): void
    {
        try {
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
                    'metadata' => $message->metadata
                ]
            ];
            
            $redis->publish($channel, json_encode($data));
        } catch (\Exception $e) {
            Log::error('Failed to publish message to Redis', [
                'chat_id' => $chatId,
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
