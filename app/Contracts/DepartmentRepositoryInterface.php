<?php

namespace App\Contracts;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DepartmentRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator;
    public function getActive(int $perPage = 20): LengthAwarePaginator;
    public function getInactive(int $perPage = 20): LengthAwarePaginator;
    public function getWithUsers(int $perPage = 20): LengthAwarePaginator;
    public function getWithoutUsers(int $perPage = 20): LengthAwarePaginator;
}
