<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with(['department', 'organizations', 'positions'])->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(['department', 'organizations', 'positions']);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getByRole(string $role): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->where('role', $role)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByDepartment(int $departmentId): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByOrganization(int $organizationId): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->whereHas('organizations', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->orderBy('name')
            ->paginate(20);
    }

    public function getActiveUsers(): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getInactiveUsers(): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->where('is_active', false)
            ->orderBy('name')
            ->paginate(20);
    }

    public function getByPosition(int $positionId): LengthAwarePaginator
    {
        return User::with(['department', 'organizations', 'positions'])
            ->whereHas('positions', function($q) use ($positionId) {
                $q->where('position_id', $positionId);
            })
            ->orderBy('name')
            ->paginate(20);
    }
}
