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
        'phone',
        'is_active',
        'wazzup24_api_key',
        'wazzup24_channel_id',
        'wazzup24_webhook_url',
        'wazzup24_webhook_secret',
        'wazzup24_settings',
        'wazzup24_enabled',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'wazzup24_settings' => 'array',
        'wazzup24_enabled' => 'boolean',
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

    /**
     * Организации с включенным Wazzup24
     */
    public function scopeWithWazzup24($query)
    {
        return $query->where('wazzup24_enabled', true)
                    ->whereNotNull('wazzup24_api_key')
                    ->whereNotNull('wazzup24_channel_id');
    }

    /**
     * Проверка настроенности Wazzup24
     */
    public function isWazzup24Configured(): bool
    {
        return $this->wazzup24_enabled &&
               !empty($this->wazzup24_api_key) &&
               !empty($this->wazzup24_channel_id);
    }

    /**
     * Получение webhook URL для организации
     */
    public function getWebhookUrl(): string
    {
        if ($this->wazzup24_webhook_url) {
            return $this->wazzup24_webhook_url;
        }

        return route('webhooks.organization', ['organization' => $this->slug]);
    }

    /**
     * Генерация webhook токена для организации
     */
    public function generateWebhookToken(): string
    {
        return 'webhook_' . $this->id . '_' . substr(md5($this->slug . $this->created_at), 0, 16);
    }
}
