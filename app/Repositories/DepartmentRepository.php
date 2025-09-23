<?php

namespace App\Repositories;

use App\Models\Department;
use App\Contracts\DepartmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Department
    {
        return Department::with(['organization', 'users'])->find($id);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department->fresh(['organization', 'users']);
    }

    public function delete(Department $department): bool
    {
        return $department->delete();
    }

    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->where('organization_id', $organizationId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getActive(int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getInactive(int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->where('is_active', false)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithUsers(int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->whereHas('users')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithoutUsers(int $perPage = 20): LengthAwarePaginator
    {
        return Department::with(['organization', 'users'])
            ->whereDoesntHave('users')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
