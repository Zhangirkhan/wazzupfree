<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'category',
        'is_active',
        'created_by',
        'organization_id',
        'usage_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    // Категории шаблонов
    const CATEGORIES = [
        'greeting' => 'Приветствие',
        'farewell' => 'Прощание',
        'help' => 'Помощь',
        'support' => 'Поддержка',
        'information' => 'Информация',
        'general' => 'Общие'
    ];

    /**
     * Отношение к пользователю, который создал шаблон
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Отношение к организации
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Получить название категории
     */
    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Неизвестно';
    }

    /**
     * Увеличить счетчик использований
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Scope для активных шаблонов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для шаблонов по категории
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope для шаблонов организации
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
