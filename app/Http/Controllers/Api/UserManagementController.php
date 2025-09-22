<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserManagementController extends ApiController
{
    public function __construct(
        private UserManagementService $userManagementService
    ) {}

    /**
     * Получить список пользователей
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $role = $request->get('role');
            $departmentId = $request->get('department_id');
            $organizationId = $request->get('organization_id');

            $users = $this->userManagementService->getUsers($perPage, $search, $role, $departmentId, $organizationId);

            return $this->paginatedResponse(
                UserResource::collection($users),
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve users', $e->getMessage(), 500);
        }
    }

    /**
     * Получить пользователя по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userManagementService->getUser($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse(
                new UserResource($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user', $e->getMessage(), 500);
        }
    }

    /**
     * Создать пользователя
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'role' => 'required|in:admin,manager,employee,user',
            'department_id' => 'nullable|exists:departments,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id'
        ]);

        try {
            $user = $this->userManagementService->createUser($request->all());

            return $this->successResponse(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create user', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить пользователя
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'role' => 'sometimes|in:admin,manager,employee,user',
            'department_id' => 'nullable|exists:departments,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id'
        ]);

        try {
            $user = $this->userManagementService->updateUser($id, $request->all());

            return $this->successResponse(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', $e->getMessage(), 500);
        }
    }

    /**
     * Изменить пароль пользователя
     */
    public function changePassword(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8'
        ]);

        try {
            $user = $this->userManagementService->changeUserPassword($id, $request->password);

            return $this->successResponse(
                new UserResource($user),
                'Password changed successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change password', $e->getMessage(), 500);
        }
    }

    /**
     * Деактивировать пользователя
     */
    public function deactivate(int $id): JsonResponse
    {
        try {
            $user = $this->userManagementService->deactivateUser($id);

            return $this->successResponse(
                new UserResource($user),
                'User deactivated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to deactivate user', $e->getMessage(), 500);
        }
    }

    /**
     * Активировать пользователя
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $user = $this->userManagementService->activateUser($id);

            return $this->successResponse(
                new UserResource($user),
                'User activated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to activate user', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить пользователя
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userManagementService->deleteUser($id);

            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete user', $e->getMessage(), 500);
        }
    }

    /**
     * Получить список ролей
     */
    public function roles(): JsonResponse
    {
        try {
            $roles = $this->userManagementService->getRoles();

            return $this->successResponse($roles, 'Roles retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve roles', $e->getMessage(), 500);
        }
    }

    /**
     * Получить список разрешений
     */
    public function permissions(): JsonResponse
    {
        try {
            $permissions = $this->userManagementService->getPermissions();

            return $this->successResponse($permissions, 'Permissions retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve permissions', $e->getMessage(), 500);
        }
    }

    /**
     * Получить роль по ID
     */
    public function showRole(string $id): JsonResponse
    {
        try {
            $role = $this->userManagementService->getRole($id);

            if (!$role) {
                return $this->errorResponse('Role not found', null, 404);
            }

            return $this->successResponse($role, 'Role retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve role', $e->getMessage(), 500);
        }
    }

    /**
     * Создать новую роль
     */
    public function createRole(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'permissions' => 'required|array',
                'permissions.*' => 'string'
            ]);

            $role = $this->userManagementService->createRole($validated);

            return $this->successResponse($role, 'Role created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create role', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить роль
     */
    public function updateRole(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'display_name' => 'sometimes|string|max:255',
                'permissions' => 'sometimes|array',
                'permissions.*' => 'string'
            ]);

            $role = $this->userManagementService->updateRole($id, $validated);

            return $this->successResponse($role, 'Role updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update role', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить роль
     */
    public function deleteRole(string $id): JsonResponse
    {
        try {
            $this->userManagementService->deleteRole($id);

            return $this->successResponse(null, 'Role deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete role', $e->getMessage(), 500);
        }
    }
}



