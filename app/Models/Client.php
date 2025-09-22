<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'uuid_wazzup',
        'comment',
        'is_active',
        'avatar',
        'contractor_id',
        'company_id'
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
     * Контрагент клиента
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /**
     * Компания клиента
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Чаты клиента
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'messenger_phone', 'phone');
    }

    /**
     * Клиенты без контрагента (физ.лица)
     */
    public function scopeWithoutContractor($query)
    {
        return $query->whereNull('contractor_id');
    }

    /**
     * Клиенты с контрагентом (юр.лица)
     */
    public function scopeWithContractor($query)
    {
        return $query->whereNotNull('contractor_id');
    }
}
