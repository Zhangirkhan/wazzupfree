<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Суперадмин может видеть все организации
        if ($user->isAdmin()) {
            return true;
        }

        // Обычные пользователи видят только свои организации
        return $user->organizations()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        // Суперадмин может видеть все организации
        if ($user->isAdmin()) {
            return true;
        }

        // Пользователь должен быть привязан к организации
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Только суперадмин может создавать организации
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        // Суперадмин может обновлять все организации
        if ($user->isAdmin()) {
            return true;
        }

        // Админ организации может обновлять свою организацию
        $userOrganization = $user->organizations()
            ->where('organizations.id', $organization->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$userOrganization) {
            return false;
        }

        // Проверяем роль пользователя в организации
        $role = $userOrganization->pivot->role_id;
        if ($role) {
            $roleModel = \App\Models\Role::find($role);
            return $roleModel && $roleModel->hasPermission('organizations');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        // Только суперадмин может удалять организации
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): bool
    {
        // Только суперадмин может восстанавливать организации
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        // Только суперадмин может окончательно удалять организации
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage Wazzup24 settings.
     */
    public function manageWazzup24(User $user, Organization $organization): bool
    {
        // Суперадмин может управлять настройками всех организаций
        if ($user->isAdmin()) {
            return true;
        }

        // Пользователь должен быть привязан к организации
        $userOrganization = $user->organizations()
            ->where('organizations.id', $organization->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$userOrganization) {
            return false;
        }

        // Проверяем роль пользователя в организации
        $role = $userOrganization->pivot->role_id;
        if ($role) {
            $roleModel = \App\Models\Role::find($role);
            return $roleModel && $roleModel->hasPermission('settings');
        }

        return false;
    }

    /**
     * Determine whether the user can view organization data.
     */
    public function viewData(User $user, Organization $organization): bool
    {
        // Суперадмин может видеть данные всех организаций
        if ($user->isAdmin()) {
            return true;
        }

        // Пользователь должен быть привязан к организации
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }
}
