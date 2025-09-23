<?php

namespace App\Services\Messenger;

use App\Contracts\ClientManagerInterface;
use App\Models\Client;
use App\Models\Chat;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientManager implements ClientManagerInterface
{
    /**
     * Поиск или создание клиента
     */
    public function findOrCreateClient(string $phone, ?array $contactData = null): Client
    {
        $client = Client::where('phone', $phone)->first();

        if (!$client) {
            $client = Client::create([
                'name' => $contactData['name'] ?? 'Клиент ' . $phone,
                'phone' => $phone,
                'is_active' => true,
                'avatar' => $contactData['avatarUri'] ?? $contactData['avatar'] ?? null
            ]);

            Log::info('Client created', [
                'client_id' => $client->id,
                'name' => $client->name
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

                // Синхронизируем title чатов по этому номеру, если в title был телефон
                $this->syncChatTitles($client);
            }
        }

        return $client;
    }

    /**
     * Поиск или создание мессенджер чата
     */
    public function findOrCreateMessengerChat(string $phone, Client $client, ?Organization $organization = null): Chat
    {
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();

        $isNewChat = false;

        if (!$chat) {
            // Используем переданную организацию или ID 1 по умолчанию
            $organizationId = $organization ? $organization->id : 1;

            // Формируем название чата: имя клиента или номер телефона
            $chatTitle = $client->name && $client->name !== 'Клиент ' . $phone
                ? $client->name
                : $phone;

            $chat = Chat::create([
                'organization_id' => $organizationId,
                'title' => $chatTitle,
                'type' => 'private', // Используем разрешенный тип
                'status' => 'active',
                'created_by' => 1, // Системный пользователь
                'is_messenger_chat' => true,
                'messenger_phone' => $phone,
                'messenger_status' => 'menu',
                'last_activity_at' => now()
            ]);
            $isNewChat = true;

            Log::info('Messenger chat created', [
                'chat_id' => $chat->id,
                'organization_id' => $organizationId,
                'phone' => $phone
            ]);

            // Отправляем уведомление о создании нового чата
            $this->notifyNewChatCreated($chat);
        } else {
            // Обновляем время последней активности для существующего чата
            $chat->update(['last_activity_at' => now()]);
        }

        return $chat;
    }

    /**
     * Проверка является ли номер тестовым
     */
    public function isTestNumber(string $phone): bool
    {
        $testNumbers = [
            '77476644108',  // Оригинальный тестовый номер
            '77079500929',  // +7 707 950 0929
            '77028200002',  // +7 702 820 0002
            '77777895444'   // +7 777 789 5444
        ];

        return in_array($phone, $testNumbers);
    }

    /**
     * Синхронизация заголовков чатов с именем клиента
     */
    private function syncChatTitles(Client $client): void
    {
        try {
            $chatsToSync = Chat::where('messenger_phone', $client->phone)
                ->where('is_messenger_chat', true)
                ->where(function($q) use ($client) {
                    $q->whereNull('title')
                      ->orWhere('title', $client->phone)
                      ->orWhere('title', 'Клиент ' . $client->phone);
                })
                ->get();
                
            foreach ($chatsToSync as $chat) {
                $chat->update(['title' => $client->name]);
            }
        } catch (\Exception $e) {
            Log::warning('Не удалось синхронизировать title чатов с именем клиента', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Уведомление о создании нового чата
     */
    private function notifyNewChatCreated(Chat $chat): void
    {
        try {
            $chatData = [
                'type' => 'chat_created',
                'chat' => [
                    'id' => $chat->id,
                    'title' => $chat->title,
                    'organization_id' => $chat->organization_id,
                    'status' => $chat->status,
                    'created_at' => $chat->created_at->toISOString(),
                    'last_activity_at' => $chat->last_activity_at->toISOString(),
                    'is_messenger_chat' => $chat->is_messenger_chat,
                    'messenger_phone' => $chat->messenger_phone,
                    'unread_count' => 0
                ]
            ];

            // Отправляем в глобальный канал (используем списки для SSE)
            Redis::lpush('sse_queue:chats.global', json_encode($chatData));
            Redis::expire('sse_queue:chats.global', 3600); // TTL 1 час

            // Также отправляем в канал организации
            if ($chat->organization_id) {
                Redis::lpush('sse_queue:organization.' . $chat->organization_id . '.chats', json_encode($chatData));
                Redis::expire('sse_queue:organization.' . $chat->organization_id . '.chats', 3600);
            } else {
                // Для чатов без организации отправляем в специальный канал
                Redis::lpush('sse_queue:chats.no_organization', json_encode($chatData));
                Redis::expire('sse_queue:chats.no_organization', 3600);
            }

            // Отправляем всем активным пользователям (fallback)
            $activeUsers = \App\Models\User::whereNotNull('id')->pluck('id');
            foreach ($activeUsers as $userId) {
                Redis::lpush('sse_queue:user.' . $userId . '.chats', json_encode($chatData));
                Redis::expire('sse_queue:user.' . $userId . '.chats', 3600);
            }

            Log::info('📡 New chat notification sent via Redis', [
                'chat_id' => $chat->id,
                'organization_id' => $chat->organization_id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send new chat notification', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
