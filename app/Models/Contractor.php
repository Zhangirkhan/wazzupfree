<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    protected $fillable = [
        'name',
        'type',
        'inn',
        'kpp',
        'ogrn',
        'legal_address',
        'actual_address',
        'passport_series',
        'passport_number',
        'passport_issued_by',
        'passport_issued_date',
        'address',
        'phone',
        'email',
        'website',
        'contact_person',
        'contact_phone',
        'contact_email',
        'bank_name',
        'bank_account',
        'bik',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'passport_issued_date' => 'date',
    ];

    /**
     * Клиенты контрагента
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Активные контрагенты
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные контрагенты
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Юридические лица
     */
    public function scopeLegal($query)
    {
        return $query->where('type', 'legal');
    }

    /**
     * Физические лица
     */
    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    /**
     * Поиск по названию или ИНН
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('inn', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%");
        });
    }

    /**
     * Проверка, является ли контрагент юридическим лицом
     */
    public function isLegal(): bool
    {
        return $this->type === 'legal';
    }

    /**
     * Проверка, является ли контрагент физическим лицом
     */
    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    /**
     * Получение полного названия с типом
     */
    public function getFullNameAttribute(): string
    {
        $typePrefix = $this->isLegal() ? 'ООО' : '';
        return $typePrefix ? "{$typePrefix} {$this->name}" : $this->name;
    }
}
