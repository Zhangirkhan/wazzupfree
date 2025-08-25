<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'avatar',
        'role',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get user's organizations
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'user_organization')
            ->withPivot(['department_id', 'role_id', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get user's departments
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_organization')
            ->withPivot(['role_id', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get user's primary department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get user's roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_organization')
            ->withPivot(['department_id', 'is_active', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get user's positions
     */
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'user_position')
            ->withPivot(['organization_id', 'department_id', 'is_primary', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Get user's position assignments
     */
    public function positionAssignments()
    {
        return $this->hasMany(UserPosition::class);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Check if user has permission to access section
     */
    public function hasPermission($permission): bool
    {
        $permissions = [
            'admin' => [
                'dashboard', 'users', 'departments', 'chats', 'organizations', 
                'positions', 'clients', 'settings'
            ],
            'manager' => [
                'dashboard', 'clients', 'messenger'
            ],
            'employee' => [
                'dashboard', 'clients', 'messenger'
            ]
        ];

        return in_array($permission, $permissions[$this->role] ?? []);
    }

    /**
     * Get user's primary position
     */
    public function primaryPosition()
    {
        return $this->positions()
            ->wherePivot('is_primary', true)
            ->wherePivot('expires_at', '>', now())
            ->orWherePivot('expires_at', null)
            ->first();
    }

    /**
     * Check if user has specific position
     */
    public function hasPosition($positionSlug, $organizationId = null)
    {
        $query = $this->positions()->where('slug', $positionSlug);
        
        if ($organizationId) {
            $query->wherePivot('organization_id', $organizationId);
        }
        
        return $query->exists();
    }

    /**
     * Check if user has permission through position
     */
    public function hasPositionPermission($permission, $organizationId = null)
    {
        $positions = $this->positions();
        
        if ($organizationId) {
            $positions->wherePivot('organization_id', $organizationId);
        }
        
        return $positions->get()->some(function ($position) use ($permission) {
            return $position->hasPermission($permission);
        });
    }

    /**
     * Check if user has specific role in organization
     */
    public function hasRoleInOrganization($roleSlug, $organizationId)
    {
        return $this->organizations()
            ->where('organization_id', $organizationId)
            ->whereHas('roles', function ($query) use ($roleSlug) {
                $query->where('slug', $roleSlug);
            })
            ->exists();
    }

    /**
     * Check if user belongs to department in organization
     */
    public function belongsToDepartmentInOrganization($departmentSlug, $organizationId)
    {
        return $this->organizations()
            ->where('organization_id', $organizationId)
            ->whereHas('departments', function ($query) use ($departmentSlug) {
                $query->where('slug', $departmentSlug);
            })
            ->exists();
    }

    /**
     * Get user's chats
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_participants')
            ->withPivot(['role', 'is_active', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Get user's messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get chats created by user
     */
    public function createdChats()
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    /**
     * Get chats assigned to user
     */
    public function assignedChats()
    {
        return $this->hasMany(Chat::class, 'assigned_to');
    }
}
