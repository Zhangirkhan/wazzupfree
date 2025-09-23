<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Все пользователи могут видеть клиентов
    }

    public function view(User $user, Client $client): bool
    {
        // Пользователь может видеть клиента если он в той же организации
        return $user->role === 'admin' || 
               $user->organizations->contains($client->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'manager';
    }

    public function update(User $user, Client $client): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'manager' && $user->organizations->contains($client->organization_id));
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }
}
