<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'domain',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_organization')
            ->withPivot(['department_id', 'role_id', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Активные организации
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные организации
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
