<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Contracts\UserRepositoryInterface;
use App\Services\CacheService;
use App\Services\LoggingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExampleOptimizedController extends ApiController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CacheService $cacheService,
        private LoggingService $loggingService,
        private NotificationService $notificationService
    ) {}

    /**
     * Получить список пользователей с кэшированием
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $role = $request->get('role');
            $departmentId = $request->get('department_id');
            $organizationId = $request->get('organization_id');

            // Используем кэширование для часто запрашиваемых данных
            $cacheKey = "users:index:" . md5(serialize($request->query()));
            
            $users = $this->cacheService->remember($cacheKey, 300, function() use ($perPage, $search, $role, $departmentId, $organizationId) {
                if ($search) {
                    return $this->userRepository->search($search, $perPage);
                }

                if ($role) {
                    return $this->userRepository->getByRole($role);
                }

                if ($departmentId) {
                    return $this->userRepository->getByDepartment($departmentId);
                }

                if ($organizationId) {
                    return $this->userRepository->getByOrganization($organizationId);
                }

                return $this->userRepository->getAll($perPage);
            });

            $this->loggingService->logUserAction('users_listed', [
                'per_page' => $perPage,
                'filters' => $request->query()
            ]);

            return $this->paginatedResponse(
                UserResource::collection($users),
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to retrieve users', [
                'error' => $e->getMessage(),
                'request' => $request->query()
            ]);
            return $this->errorResponse('Failed to retrieve users', $e->getMessage(), 500);
        }
    }

    /**
     * Создать пользователя с валидацией и событиями
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->create($request->validated());
            
            // Инвалидируем кэш
            $this->cacheService->forget("users:index:*");
            
            // Логируем действие
            $this->loggingService->logUserAction('user_created', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

            // Отправляем уведомления (если нужно)
            // $this->notificationService->sendUserCreatedNotification($user);

            return $this->successResponse(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to create user', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);
            return $this->errorResponse('Failed to create user', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить пользователя
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $user = $this->userRepository->update($user, $request->validated());
            
            // Инвалидируем кэш
            $this->cacheService->invalidateUser($user->id);
            $this->cacheService->forget("users:index:*");
            
            $this->loggingService->logUserAction('user_updated', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

            return $this->successResponse(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to update user', [
                'error' => $e->getMessage(),
                'user_id' => $id,
                'request_data' => $request->validated()
            ]);
            return $this->errorResponse('Failed to update user', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить пользователя
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $result = $this->userRepository->delete($user);
            
            if ($result) {
                // Инвалидируем кэш
                $this->cacheService->invalidateUser($user->id);
                $this->cacheService->forget("users:index:*");
                
                $this->loggingService->logUserAction('user_deleted', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]);
            }

            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);
            return $this->errorResponse('Failed to delete user', $e->getMessage(), 500);
        }
    }
}
