<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use App\Services\CacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CachedUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CacheService $cacheService
    ) {}

    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = "users:all:per_page:{$perPage}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($perPage) {
            return $this->userRepository->getAll($perPage);
        });
    }

    public function findById(int $id): ?User
    {
        $cacheKey = $this->cacheService->getUserCacheKey($id);
        
        return $this->cacheService->remember($cacheKey, 600, function() use ($id) {
            return $this->userRepository->findById($id);
        });
    }

    public function findByEmail(string $email): ?User
    {
        $cacheKey = "user:email:{$email}";
        
        return $this->cacheService->remember($cacheKey, 600, function() use ($email) {
            return $this->userRepository->findByEmail($email);
        });
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = "users:search:{$query}:per_page:{$perPage}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($query, $perPage) {
            return $this->userRepository->search($query, $perPage);
        });
    }

    public function create(array $data): User
    {
        $user = $this->userRepository->create($data);
        
        // Инвалидируем кэш
        $this->cacheService->forget("users:all:per_page:20");
        
        return $user;
    }

    public function update(User $user, array $data): User
    {
        $updatedUser = $this->userRepository->update($user, $data);
        
        // Инвалидируем кэш
        $this->cacheService->invalidateUser($user->id);
        $this->cacheService->forget("users:all:per_page:20");
        
        return $updatedUser;
    }

    public function delete(User $user): bool
    {
        $result = $this->userRepository->delete($user);
        
        if ($result) {
            // Инвалидируем кэш
            $this->cacheService->invalidateUser($user->id);
            $this->cacheService->forget("users:all:per_page:20");
        }
        
        return $result;
    }

    public function getByRole(string $role): LengthAwarePaginator
    {
        $cacheKey = "users:role:{$role}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($role) {
            return $this->userRepository->getByRole($role);
        });
    }

    public function getByDepartment(int $departmentId): LengthAwarePaginator
    {
        $cacheKey = "users:department:{$departmentId}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($departmentId) {
            return $this->userRepository->getByDepartment($departmentId);
        });
    }

    public function getByOrganization(int $organizationId): LengthAwarePaginator
    {
        $cacheKey = "users:organization:{$organizationId}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($organizationId) {
            return $this->userRepository->getByOrganization($organizationId);
        });
    }

    public function getActiveUsers(): LengthAwarePaginator
    {
        $cacheKey = "users:active";
        
        return $this->cacheService->remember($cacheKey, 300, function() {
            return $this->userRepository->getActiveUsers();
        });
    }

    public function getInactiveUsers(): LengthAwarePaginator
    {
        $cacheKey = "users:inactive";
        
        return $this->cacheService->remember($cacheKey, 300, function() {
            return $this->userRepository->getInactiveUsers();
        });
    }

    public function getByPosition(int $positionId): LengthAwarePaginator
    {
        $cacheKey = "users:position:{$positionId}";
        
        return $this->cacheService->remember($cacheKey, 300, function() use ($positionId) {
            return $this->userRepository->getByPosition($positionId);
        });
    }
}
