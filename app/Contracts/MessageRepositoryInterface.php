<?php

namespace App\Contracts;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MessageRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Message;
    public function create(array $data): Message;
    public function update(Message $message, array $data): Message;
    public function delete(Message $message): bool;
    public function getByChat(int $chatId, int $perPage = 20): LengthAwarePaginator;
    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator;
    public function getByType(string $type, int $perPage = 20): LengthAwarePaginator;
    public function getUnread(int $userId, int $perPage = 20): LengthAwarePaginator;
    public function getRead(int $userId, int $perPage = 20): LengthAwarePaginator;
    public function getFromClient(int $chatId, int $perPage = 20): LengthAwarePaginator;
    public function getFromUser(int $chatId, int $perPage = 20): LengthAwarePaginator;
    public function getLastMessage(int $chatId): ?Message;
    public function markAsRead(Message $message): Message;
    public function markAsUnread(Message $message): Message;
    public function getByDateRange(int $chatId, string $startDate, string $endDate, int $perPage = 20): LengthAwarePaginator;
    public function getByMessengerId(string $messengerId): ?Message;
}
