<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'manager';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin' || 
               $user->role === 'manager' || 
               $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
