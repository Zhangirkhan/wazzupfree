<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Все пользователи могут видеть чаты
    }

    public function view(User $user, Chat $chat): bool
    {
        // Пользователь может видеть чат если:
        // 1. Он назначен на чат
        // 2. Он создал чат
        // 3. Он админ или менеджер
        // 4. Он в том же отделе
        return $user->role === 'admin' || 
               $user->role === 'manager' ||
               $chat->assigned_to === $user->id ||
               $chat->created_by === $user->id ||
               $chat->department_id === $user->department_id;
    }

    public function create(User $user): bool
    {
        return true; // Все пользователи могут создавать чаты
    }

    public function update(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || 
               $user->role === 'manager' ||
               $chat->assigned_to === $user->id ||
               $chat->created_by === $user->id;
    }

    public function delete(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || 
               $chat->created_by === $user->id;
    }

    public function assign(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || $user->role === 'manager';
    }

    public function close(User $user, Chat $chat): bool
    {
        return $user->role === 'admin' || 
               $user->role === 'manager' ||
               $chat->assigned_to === $user->id;
    }
}
