<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'type',
        'created_by',
        'assigned_to',
        'status',
        'closed_at',
        'phone',
        'wazzup_chat_id',
        'creator_id',
        // 'client_id', // Удалено - поля нет в БД
        'department_id',
        'messenger_status',
        'last_activity_at',
        'is_messenger_chat',
        'messenger_phone',
        'messenger_data',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'messenger_data' => 'array',
        'is_messenger_chat' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot(['role', 'is_active', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'pending');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'messenger_phone', 'phone');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeMessenger($query)
    {
        return $query->where('is_messenger_chat', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeActiveMessenger($query)
    {
        return $query->where('is_messenger_chat', true)
                    ->where('messenger_status', 'active');
    }

    /**
     * Проверяет, может ли пользователь видеть этот чат
     */
    public function canBeViewedBy(User $user): bool
    {
        // Администратор видит все чаты
        if ($user->role === 'admin') {
            return true;
        }

        // Для мессенджер чатов
        if ($this->is_messenger_chat) {
            return $this->canBeViewedByUserInMessenger($user);
        }

        // Для обычных чатов - проверяем участников
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Проверяет доступ к мессенджер чату
     */
    protected function canBeViewedByUserInMessenger(User $user): bool
    {
        // Если чат назначен конкретному пользователю
        if ($this->assigned_to && $this->assigned_to == $user->id) {
            return true;
        }

        // Если пользователь назначен на отдел чата
        if ($this->department_id && $user->department_id == $this->department_id) {
            return true;
        }

        // Если пользователь написал в чат (автоматически забирает на себя)
        if ($this->messages()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Автоматически назначает чат пользователю при его сообщении
     */
    public function assignToUser(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'department_id' => $user->department_id
        ]);

        \Log::info('Chat auto-assigned to user', [
            'chat_id' => $this->id,
            'user_id' => $user->id,
            'department_id' => $user->department_id
        ]);
    }
}
