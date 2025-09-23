<?php

namespace App\Services;

use App\Contracts\ChatServiceInterface;
use App\Exceptions\ChatNotFoundException;
use App\Exceptions\UnauthorizedChatAccessException;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Contracts\ChatRepositoryInterface;
use App\Events\ChatCreated;
use App\Events\ChatAssigned;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ChatService implements ChatServiceInterface
{
    public function __construct(
        private ChatRepositoryInterface $chatRepository
    ) {}

    public function getUserChats(User $user, int $perPage = 20): LengthAwarePaginator
    {
        // Администратор видит все чаты
        if ($user->role === 'admin') {
            return $this->chatRepository->getAll($perPage);
        }

        // Обычные пользователи видят только свои чаты
        return $this->chatRepository->getByUser($user->id, $perPage);
    }

    public function createChat(array $data, User $user): Chat
    {
        return DB::transaction(function () use ($data, $user) {
            $chatData = [
                'organization_id' => 1, // Используем первую организацию
                'title' => $data['client_name'],
                'phone' => $data['client_phone'],
                'department_id' => $data['department_id'] ?? null,
                'status' => 'active',
                'assigned_to' => $user->id
            ];

            $chat = $this->chatRepository->create($chatData, $user);

            // Создаем первое сообщение
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $data['message'],
                'type' => 'text',
                'direction' => 'out'
            ]);

            // Отправляем событие
            event(new ChatCreated($chat));

            Log::info('Chat created successfully', [
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'client_name' => $data['client_name']
            ]);

            // Отправляем событие о создании чата через Redis
            $this->broadcastChatEvent('chat_created', $chat->load(['creator', 'assignedTo', 'messages']));

            return $chat->load(['creator', 'assignedTo', 'messages']);
        });
    }

    public function getChat(string $id, User $user): ?Chat
    {
        $chat = $this->chatRepository->findById((int)$id);

        if (!$chat) {
            Log::warning('Chat not found', ['chat_id' => $id, 'user_id' => $user->id]);
            throw new ChatNotFoundException();
        }

        // Проверяем права доступа
        if ($user->role !== 'admin' && 
            $chat->created_by !== $user->id && 
            $chat->assigned_to !== $user->id) {
            throw new UnauthorizedChatAccessException();
        }

        Log::info('Chat accessed', ['chat_id' => $id, 'user_id' => $user->id]);
        return $chat;
    }

    public function searchChats(string $query, User $user, ?string $status = null, int $perPage = 20): LengthAwarePaginator
    {
        // Администратор может искать по всем чатам
        if ($user->role === 'admin') {
            return $this->chatRepository->search($query, $perPage);
        } else {
            // Обычные пользователи ищут только по своим чатам
            return $this->chatRepository->getByUser($user->id, $perPage);
        }
    }

    public function endChat(string $chatId, User $user): Chat
    {
        $chat = $this->getChat($chatId, $user);
        $chat->update(['status' => 'closed']);

        Log::info('Chat ended', ['chat_id' => $chatId, 'user_id' => $user->id]);

        return $chat;
    }

    public function transferChat(string $chatId, int $assignedTo, User $user, ?string $note = null): Chat
    {
        return DB::transaction(function () use ($chatId, $assignedTo, $user, $note) {
            $chat = $this->getChat($chatId, $user);
            $assignedUser = User::findOrFail($assignedTo);

            $chat = $this->chatRepository->update($chat, [
                'assigned_to' => $assignedTo,
                'status' => 'transferred'
            ]);

            // Создаем системное сообщение о передаче
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => "Чат передан пользователю. " . ($note ?? ''),
                'type' => 'system',
                'direction' => 'out'
            ]);

            // Отправляем событие о назначении чата
            event(new ChatAssigned($chat, $assignedUser, $user));

            Log::info('Chat transferred', [
                'chat_id' => $chatId,
                'from_user_id' => $user->id,
                'to_user_id' => $assignedTo,
                'note' => $note
            ]);

            return $chat->load(['creator', 'assignedTo']);
        });
    }

    public function deleteChat(string $chatId, User $user): bool
    {
        $chat = $this->getChat($chatId, $user);
        $chat->delete();

        Log::info('Chat soft deleted', ['chat_id' => $chatId, 'user_id' => $user->id]);

        return true;
    }

    public function restoreChat(string $chatId, User $user): Chat
    {
        $chat = Chat::withTrashed()->find($chatId);

        if (!$chat) {
            throw new ChatNotFoundException();
        }

        $chat->restore();

        Log::info('Chat restored', ['chat_id' => $chatId, 'user_id' => $user->id]);

        return $chat->load(['creator', 'assignedTo']);
    }

    /**
     * Отправляем событие о чате через Redis для SSE
     */
    private function broadcastChatEvent(string $eventType, Chat $chat): void
    {
        try {
            $eventData = [
                'type' => $eventType,
                'chat' => [
                    'id' => $chat->id,
                    'title' => $chat->title,
                    'client_name' => $chat->title, // Для совместимости
                    'client_phone' => $chat->phone,
                    'client_email' => null,
                    'status' => $chat->status,
                    'created_at' => $chat->created_at?->toISOString(),
                    'updated_at' => $chat->updated_at?->toISOString(),
                    'unread_count' => 0,
                    'last_message' => $chat->messages->first() ? [
                        'id' => $chat->messages->first()->id,
                        'message' => $chat->messages->first()->content,
                        'type' => $chat->messages->first()->type,
                        'created_at' => $chat->messages->first()->created_at?->toISOString(),
                        'user' => [
                            'id' => $chat->messages->first()->user_id,
                            'name' => $chat->creator?->name ?? 'Система',
                            'email' => $chat->creator?->email ?? '',
                            'role' => 'user',
                            'permissions' => [],
                            'roles' => [],
                            'organization_id' => $chat->organization_id,
                            'phone' => null,
                            'position' => null,
                            'created_at' => '',
                            'updated_at' => '',
                            'status' => 'active'
                        ],
                        'is_from_client' => false,
                        'is_read' => false
                    ] : null,
                    'user' => [
                        'id' => $chat->creator?->id,
                        'name' => $chat->creator?->name ?? 'Система',
                        'email' => $chat->creator?->email ?? ''
                    ],
                    'assigned_user' => $chat->assignedTo ? [
                        'id' => $chat->assignedTo->id,
                        'name' => $chat->assignedTo->name,
                        'email' => $chat->assignedTo->email
                    ] : null
                ],
                'timestamp' => now()->toISOString()
            ];

            // Отправляем в глобальный канал чатов
            Redis::publish('chats.global', json_encode($eventData));

            // Отправляем в канал организации если есть
            if ($chat->organization_id) {
                Redis::publish('organization.' . $chat->organization_id . '.chats', json_encode($eventData));
            }

            // Отправляем создателю чата
            if ($chat->created_by) {
                Redis::publish('user.' . $chat->created_by . '.chats', json_encode($eventData));
            }

            // Отправляем назначенному пользователю если отличается от создателя
            if ($chat->assigned_to && $chat->assigned_to !== $chat->created_by) {
                Redis::publish('user.' . $chat->assigned_to . '.chats', json_encode($eventData));
            }

            Log::info('Chat event broadcasted', [
                'event_type' => $eventType,
                'chat_id' => $chat->id,
                'organization_id' => $chat->organization_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast chat event', [
                'event_type' => $eventType,
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
