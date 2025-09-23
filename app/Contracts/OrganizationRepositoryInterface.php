<?php

namespace App\Contracts;

use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrganizationRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Organization;
    public function create(array $data): Organization;
    public function update(Organization $organization, array $data): Organization;
    public function delete(Organization $organization): bool;
    public function getActive(int $perPage = 20): LengthAwarePaginator;
    public function getInactive(int $perPage = 20): LengthAwarePaginator;
    public function getWithUsers(int $perPage = 20): LengthAwarePaginator;
    public function getWithDepartments(int $perPage = 20): LengthAwarePaginator;
    public function getWithChats(int $perPage = 20): LengthAwarePaginator;
}
