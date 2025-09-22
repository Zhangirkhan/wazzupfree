<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DepartmentResource;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentController extends ApiController
{
    public function __construct(
        private DepartmentService $departmentService
    ) {}

    /**
     * Получить список отделов
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $organizationId = $request->get('organization_id');

            $departments = $this->departmentService->getDepartments($perPage, $search, $organizationId);

            return $this->paginatedResponse(
                DepartmentResource::collection($departments),
                'Departments retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve departments', $e->getMessage(), 500);
        }
    }

    /**
     * Получить отдел по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $department = $this->departmentService->getDepartment($id);

            if (!$department) {
                return $this->notFoundResponse('Department not found');
            }

            return $this->successResponse(
                new DepartmentResource($department),
                'Department retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve department', $e->getMessage(), 500);
        }
    }

    /**
     * Создать отдел
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:departments,slug',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean',
            'show_in_chatbot' => 'boolean',
            'chatbot_order' => 'integer|min:0'
        ]);

        try {
            $department = $this->departmentService->createDepartment($request->all());

            return $this->successResponse(
                new DepartmentResource($department),
                'Department created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create department', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить отдел
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:departments,slug,' . $id,
            'description' => 'nullable|string',
            'organization_id' => 'sometimes|exists:organizations,id',
            'is_active' => 'boolean',
            'show_in_chatbot' => 'boolean',
            'chatbot_order' => 'integer|min:0'
        ]);

        try {
            $department = $this->departmentService->updateDepartment($id, $request->all());

            return $this->successResponse(
                new DepartmentResource($department),
                'Department updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update department', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить отдел
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->departmentService->deleteDepartment($id);

            return $this->successResponse(null, 'Department deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete department', $e->getMessage(), 500);
        }
    }

    /**
     * Получить пользователей отдела
     */
    public function users(int $id): JsonResponse
    {
        try {
            $users = $this->departmentService->getDepartmentUsers($id);

            return $this->successResponse($users, 'Department users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve department users', $e->getMessage(), 500);
        }
    }

    /**
     * Получить руководителей отдела
     */
    public function supervisors(int $id): JsonResponse
    {
        try {
            $supervisors = $this->departmentService->getDepartmentSupervisors($id);

            return $this->successResponse($supervisors, 'Department supervisors retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve department supervisors', $e->getMessage(), 500);
        }
    }

    /**
     * Получить менеджеров отдела
     */
    public function managers(int $id): JsonResponse
    {
        try {
            $managers = $this->departmentService->getDepartmentManagers($id);

            return $this->successResponse($managers, 'Department managers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve department managers', $e->getMessage(), 500);
        }
    }

    /**
     * Назначить руководителя отдела
     */
    public function assignSupervisor(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'department_id' => 'required|integer|exists:departments,id'
            ]);

            $result = $this->departmentService->assignSupervisor($validated['user_id'], $validated['department_id']);

            return $this->successResponse($result, 'Supervisor assigned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to assign supervisor', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить руководителя отдела
     */
    public function removeSupervisor(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'department_id' => 'required|integer|exists:departments,id'
            ]);

            $result = $this->departmentService->removeSupervisor($validated['user_id'], $validated['department_id']);

            return $this->successResponse($result, 'Supervisor removed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove supervisor', $e->getMessage(), 500);
        }
    }

    /**
     * Назначить менеджера отдела
     */
    public function assignManager(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'department_id' => 'required|integer|exists:departments,id'
            ]);

            $result = $this->departmentService->assignManager($validated['user_id'], $validated['department_id']);

            return $this->successResponse($result, 'Manager assigned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to assign manager', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить менеджера отдела
     */
    public function removeManager(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'department_id' => 'required|integer|exists:departments,id'
            ]);

            $result = $this->departmentService->removeManager($validated['user_id'], $validated['department_id']);

            return $this->successResponse($result, 'Manager removed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove manager', $e->getMessage(), 500);
        }
    }
}



