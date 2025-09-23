<?php

namespace App\Contracts;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Client;
    public function findByPhone(string $phone): ?Client;
    public function create(array $data): Client;
    public function update(Client $client, array $data): Client;
    public function delete(Client $client): bool;
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;
    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator;
    public function getActive(int $perPage = 20): LengthAwarePaginator;
    public function getInactive(int $perPage = 20): LengthAwarePaginator;
    public function getWithChats(int $perPage = 20): LengthAwarePaginator;
    public function getWithoutChats(int $perPage = 20): LengthAwarePaginator;
    public function getByCompany(int $companyId, int $perPage = 20): LengthAwarePaginator;
}
