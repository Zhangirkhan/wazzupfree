<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'parent_id',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_organization')
            ->withPivot(['role_id', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Активные отделы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Неактивные отделы
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
