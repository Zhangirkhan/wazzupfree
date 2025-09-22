<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'type',
        'metadata',
        'is_hidden',
        'hidden_by',
        'hidden_at',
        'wazzup_message_id',
        'direction',
        'status',
        'is_from_client',
        'messenger_message_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_hidden' => 'boolean',
        'is_from_client' => 'boolean',
        'hidden_at' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Проверить, прочитано ли сообщение пользователем
     */
    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Получить время прочтения сообщения пользователем
     */
    public function getReadTimeBy(int $userId): ?string
    {
        $read = $this->reads()->where('user_id', $userId)->first();
        return $read ? $read->read_at->toISOString() : null;
    }

    /**
     * Отметить сообщение как прочитанное
     */
    public function markAsReadBy(int $userId): MessageRead
    {
        return MessageRead::markAsRead($this->id, $userId);
    }
}
