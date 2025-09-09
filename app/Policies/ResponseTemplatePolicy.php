<?php

namespace App\Policies;

use App\Models\ResponseTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResponseTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ResponseTemplate $responseTemplate): bool
    {
        return $user->role === 'admin' && $user->organization_id === $responseTemplate->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResponseTemplate $responseTemplate): bool
    {
        return $user->role === 'admin' && $user->organization_id === $responseTemplate->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResponseTemplate $responseTemplate): bool
    {
        return $user->role === 'admin' && $user->organization_id === $responseTemplate->organization_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ResponseTemplate $responseTemplate): bool
    {
        return $user->role === 'admin' && $user->organization_id === $responseTemplate->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ResponseTemplate $responseTemplate): bool
    {
        return $user->role === 'admin' && $user->organization_id === $responseTemplate->organization_id;
    }
}
