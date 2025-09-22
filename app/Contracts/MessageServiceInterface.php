<?php

namespace App\Contracts;

use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface MessageServiceInterface
{
    public function sendMessage(string $chatId, string $message, User $user, string $type = 'text', ?UploadedFile $file = null): Message;

    public function getChatMessages(string $chatId, User $user, int $perPage = 50): LengthAwarePaginator;

    public function sendSystemMessage(string $chatId, string $message, User $user): Message;
}
