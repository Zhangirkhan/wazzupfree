<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'uuid_wazzup',
        'comment',
        'is_active',
        'avatar'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Активные клиенты
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные клиенты
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Поиск по имени или телефону
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('uuid_wazzup', 'like', "%{$search}%");
        });
    }

    /**
     * Чаты клиента
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'messenger_phone', 'phone');
    }
}
