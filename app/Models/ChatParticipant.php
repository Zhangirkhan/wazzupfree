<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
        'is_active',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Активные участники чата
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные участники чата
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
