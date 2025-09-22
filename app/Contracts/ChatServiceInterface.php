<?php

namespace App\Contracts;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ChatServiceInterface
{
    public function getUserChats(User $user, int $perPage = 20): LengthAwarePaginator;

    public function createChat(array $data, User $user): Chat;

    public function getChat(string $id, User $user): ?Chat;

    public function searchChats(string $query, User $user, ?string $status = null, int $perPage = 20): LengthAwarePaginator;

    public function endChat(string $chatId, User $user): Chat;

    public function transferChat(string $chatId, int $assignedTo, User $user, ?string $note = null): Chat;
}
