<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use App\Services\QueryBuilderService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OptimizedUserRepository implements UserRepositoryInterface
{
    private const DEFAULT_EAGER_LOADS = [
        'department',
        'organizations',
        'positions'
    ];

    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('id', $id)
            ->first();
    }

    public function findByEmail(string $email): ?User
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('email', $email)
            ->first();
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        $queryBuilder = new QueryBuilderService(new User());
        $queryBuilder->with(self::DEFAULT_EAGER_LOADS);
        
        // Добавляем условия поиска
        $queryBuilder->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
        
        return $queryBuilder->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(self::DEFAULT_EAGER_LOADS);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getByRole(string $role): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('role', $role)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByDepartment(int $departmentId): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByOrganization(int $organizationId): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->whereHas('organizations', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->orderBy('name')
            ->paginate(20);
    }

    public function getActiveUsers(): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getInactiveUsers(): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->where('is_active', false)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByPosition(int $positionId): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with(self::DEFAULT_EAGER_LOADS)
            ->whereHas('positions', function($q) use ($positionId) {
                $q->where('position_id', $positionId);
            })
            ->orderBy('name')
            ->paginate(20);
    }

    /**
     * Получить пользователей с их чатами (оптимизированный запрос)
     */
    public function getUsersWithChats(int $perPage = 20): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with([
                'department',
                'organizations',
                'positions',
                'chats' => function($query) {
                    $query->latest()->limit(5);
                },
                'assignedChats' => function($query) {
                    $query->latest()->limit(5);
                }
            ])
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Получить пользователей с их сообщениями (оптимизированный запрос)
     */
    public function getUsersWithMessages(int $perPage = 20): LengthAwarePaginator
    {
        return (new QueryBuilderService(new User()))
            ->with([
                'department',
                'organizations',
                'positions',
                'messages' => function($query) {
                    $query->latest()->limit(10);
                }
            ])
            ->orderBy('name')
            ->paginate($perPage);
    }
}
