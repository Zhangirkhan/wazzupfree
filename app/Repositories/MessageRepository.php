<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\Chat;
use App\Contracts\MessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MessageRepository implements MessageRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user', 'chat'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Message
    {
        return Message::with(['user', 'chat'])->find($id);
    }

    public function create(array $data): Message
    {
        return Message::create($data);
    }

    public function update(Message $message, array $data): Message
    {
        $message->update($data);
        return $message->fresh(['user', 'chat']);
    }

    public function delete(Message $message): bool
    {
        return $message->delete();
    }

    public function getByChat(int $chatId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user'])
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user', 'chat'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByType(string $type, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user', 'chat'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnread(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user', 'chat'])
            ->where('is_read', false)
            ->where('user_id', '!=', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRead(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user', 'chat'])
            ->where('is_read', true)
            ->where('user_id', '!=', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getFromClient(int $chatId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user'])
            ->where('chat_id', $chatId)
            ->where('is_from_client', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getFromUser(int $chatId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user'])
            ->where('chat_id', $chatId)
            ->where('is_from_client', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getLastMessage(int $chatId): ?Message
    {
        return Message::with(['user'])
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function markAsRead(Message $message): Message
    {
        $message->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        return $message->fresh(['user', 'chat']);
    }

    public function markAsUnread(Message $message): Message
    {
        $message->update([
            'is_read' => false,
            'read_at' => null
        ]);
        
        return $message->fresh(['user', 'chat']);
    }

    public function getByDateRange(int $chatId, string $startDate, string $endDate, int $perPage = 20): LengthAwarePaginator
    {
        return Message::with(['user'])
            ->where('chat_id', $chatId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByMessengerId(string $messengerId): ?Message
    {
        return Message::where('messenger_id', $messengerId)->first();
    }
}
