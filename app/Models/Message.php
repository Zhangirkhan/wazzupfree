<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_hidden' => 'boolean',
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
}
