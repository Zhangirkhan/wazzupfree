<?php

namespace App\Contracts;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ChatRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Chat;
    public function create(array $data, User $user): Chat;
    public function update(Chat $chat, array $data): Chat;
    public function delete(Chat $chat): bool;
    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator;
    public function getByDepartment(int $departmentId, int $perPage = 20): LengthAwarePaginator;
    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator;
    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator;
    public function getByClient(int $clientId, int $perPage = 20): LengthAwarePaginator;
    public function getUnassigned(int $perPage = 20): LengthAwarePaginator;
    public function getActive(int $perPage = 20): LengthAwarePaginator;
    public function getClosed(int $perPage = 20): LengthAwarePaginator;
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;
    public function getWithUnreadMessages(int $userId, int $perPage = 20): LengthAwarePaginator;
    public function getByMessengerPhone(string $phone): ?Chat;
}
