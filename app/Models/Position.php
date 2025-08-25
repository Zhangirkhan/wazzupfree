<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Пользователи с этой должностью
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_position')
            ->withPivot(['organization_id', 'department_id', 'is_primary', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Назначения должностей
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(UserPosition::class);
    }

    /**
     * Активные должности
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные должности
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Сортировка по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Проверка права доступа
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Добавление права доступа
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Удаление права доступа
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);
        $this->update(['permissions' => array_values($permissions)]);
    }
}
