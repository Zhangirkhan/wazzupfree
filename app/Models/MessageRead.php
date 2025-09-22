<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отметить сообщение как прочитанное
     */
    public static function markAsRead(int $messageId, int $userId): self
    {
        return self::firstOrCreate([
            'message_id' => $messageId,
            'user_id' => $userId,
        ], [
            'read_at' => now(),
        ]);
    }

    /**
     * Отметить множество сообщений как прочитанные
     */
    public static function markMultipleAsRead(array $messageIds, int $userId): void
    {
        $existingReads = self::where('user_id', $userId)
            ->whereIn('message_id', $messageIds)
            ->pluck('message_id')
            ->toArray();

        $newMessageIds = array_diff($messageIds, $existingReads);

        if (!empty($newMessageIds)) {
            $data = array_map(function($messageId) use ($userId) {
                return [
                    'message_id' => $messageId,
                    'user_id' => $userId,
                    'read_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $newMessageIds);

            self::insert($data);
        }
    }
}
