<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Template extends Model
{
    protected $fillable = [
        'name',
        'content',
        'type',
        'category',
        'variables',
        'language',
        'is_active',
        'is_system',
        'created_by',
        'organization_id',
        'usage_count',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Связь с пользователем-создателем
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Связь с организацией
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope для активных шаблонов
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для неактивных шаблонов
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope для системных шаблонов
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope для пользовательских шаблонов
     */
    public function scopeUser(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope для фильтрации по типу
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для фильтрации по категории
     */
    public function scopeOfCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope для поиска по названию и содержимому
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для фильтрации по языку
     */
    public function scopeOfLanguage(Builder $query, string $language): Builder
    {
        return $query->where('language', $language);
    }

    /**
     * Scope для шаблонов организации
     */
    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->where(function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId)
              ->orWhere('is_system', true);
        });
    }

    /**
     * Увеличить счетчик использования
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Получить обработанный контент с подстановкой переменных
     */
    public function getProcessedContent(array $variables = []): string
    {
        $content = $this->content;

        // Объединяем переменные шаблона с переданными
        $allVariables = array_merge($this->variables ?? [], $variables);

        foreach ($allVariables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        return $content;
    }

    /**
     * Проверить, является ли шаблон системным
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Проверить, активен ли шаблон
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Получить доступные типы шаблонов
     */
    public static function getTypes(): array
    {
        return ['message', 'email', 'sms', 'notification'];
    }

    /**
     * Получить доступные категории шаблонов
     */
    public static function getCategories(): array
    {
        return ['greeting', 'farewell', 'support', 'sales', 'technical', 'general'];
    }

    /**
     * Получить доступные языки
     */
    public static function getLanguages(): array
    {
        return ['ru', 'en', 'kk'];
    }
}
