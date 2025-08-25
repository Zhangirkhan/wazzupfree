<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'organization_id',
        'slug'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function hasPermission($permission): bool
    {
        $permissions = [
            'admin' => [
                'dashboard', 'users', 'departments', 'chats', 'organizations', 
                'positions', 'clients', 'settings', 'messenger'
            ],
            'manager' => [
                'messenger', 'clients'
            ],
            'employee' => [
                'messenger', 'clients'
            ]
        ];

        return in_array($permission, $permissions[$this->name] ?? []);
    }
}
