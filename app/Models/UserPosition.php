<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPosition extends Model
{
    protected $table = 'user_position';

    protected $fillable = [
        'user_id',
        'position_id',
        'organization_id',
        'department_id',
        'is_primary',
        'assigned_at',
        'expires_at'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'assigned_at' => 'date',
        'expires_at' => 'date',
    ];

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Должность
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Организация
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Отдел
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Активные назначения
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Основные должности
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
