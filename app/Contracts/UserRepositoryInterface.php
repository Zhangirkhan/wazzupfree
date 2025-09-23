<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): bool;
    public function getByRole(string $role): LengthAwarePaginator;
    public function getByDepartment(int $departmentId): LengthAwarePaginator;
    public function getByOrganization(int $organizationId): LengthAwarePaginator;
    public function getActiveUsers(): LengthAwarePaginator;
    public function getInactiveUsers(): LengthAwarePaginator;
    public function getByPosition(int $positionId): LengthAwarePaginator;
}
