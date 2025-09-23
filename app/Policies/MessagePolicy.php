<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Все пользователи могут видеть сообщения
    }

    public function view(User $user, Message $message): bool
    {
        // Пользователь может видеть сообщение если он имеет доступ к чату
        return $user->role === 'admin' || 
               $user->role === 'manager' ||
               $message->chat->assigned_to === $user->id ||
               $message->chat->created_by === $user->id ||
               $message->chat->department_id === $user->department_id;
    }

    public function create(User $user): bool
    {
        return true; // Все пользователи могут отправлять сообщения
    }

    public function update(User $user, Message $message): bool
    {
        return $user->role === 'admin' || 
               $message->user_id === $user->id;
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->role === 'admin' || 
               $message->user_id === $user->id;
    }

    public function markAsRead(User $user, Message $message): bool
    {
        return $user->role === 'admin' || 
               $user->role === 'manager' ||
               $message->chat->assigned_to === $user->id;
    }
}
