<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Contracts\OrganizationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Organization
    {
        return Organization::with(['users', 'departments', 'clients'])->find($id);
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);
        return $organization->fresh(['users', 'departments', 'clients']);
    }

    public function delete(Organization $organization): bool
    {
        return $organization->delete();
    }

    public function getActive(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getInactive(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->where('is_active', false)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithUsers(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->whereHas('users')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithDepartments(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->whereHas('departments')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithChats(int $perPage = 20): LengthAwarePaginator
    {
        return Organization::with(['users', 'departments', 'clients'])
            ->whereHas('clients.chats')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
