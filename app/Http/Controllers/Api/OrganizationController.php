<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrganizationResource;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationController extends ApiController
{
    public function __construct(
        private OrganizationService $organizationService
    ) {}

    /**
     * Получить список организаций
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $organizations = $this->organizationService->getOrganizations($perPage, $search);

            return response()->json([
                'data' => OrganizationResource::collection($organizations),
                'pagination' => [
                    'current_page' => $organizations->currentPage(),
                    'last_page' => $organizations->lastPage(),
                    'per_page' => $organizations->perPage(),
                    'total' => $organizations->total(),
                    'from' => $organizations->firstItem(),
                    'to' => $organizations->lastItem(),
                    'has_more_pages' => $organizations->hasMorePages(),
                    'links' => [
                        'first' => $organizations->url(1),
                        'last' => $organizations->url($organizations->lastPage()),
                        'prev' => $organizations->previousPageUrl(),
                        'next' => $organizations->nextPageUrl()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve organizations', $e->getMessage(), 500);
        }
    }

    /**
     * Получить организацию по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $organization = $this->organizationService->getOrganization($id);

            if (!$organization) {
                return $this->notFoundResponse('Organization not found');
            }

            return response()->json([
                'organization' => new OrganizationResource($organization)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve organization', $e->getMessage(), 500);
        }
    }

    /**
     * Создать организацию
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:organizations,slug'
            ],
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'webhook_url' => 'nullable|url|max:200',
            'wazzup24_enabled' => 'boolean',
            'wazzup24_api_key' => 'nullable|string',
            'wazzup24_channel_id' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $organization = $this->organizationService->createOrganization($request->all());

            return response()->json([
                'organization' => new OrganizationResource($organization)
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create organization', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить организацию
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:organizations,slug,' . $id
            ],
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'webhook_url' => 'nullable|url|max:200',
            'wazzup24_enabled' => 'boolean',
            'wazzup24_api_key' => 'nullable|string',
            'wazzup24_channel_id' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $organization = $this->organizationService->updateOrganization($id, $request->all());

            return response()->json([
                'organization' => new OrganizationResource($organization)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update organization', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить организацию
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->organizationService->deleteOrganization($id);

            return response()->json([
                'message' => 'Organization deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete organization', $e->getMessage(), 500);
        }
    }

    /**
     * Получить отделы организации
     */
    public function departments(int $id): JsonResponse
    {
        try {
            $departments = $this->organizationService->getOrganizationDepartments($id);

            return response()->json([
                'success' => true,
                'message' => 'Organization departments retrieved successfully',
                'data' => $departments,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve organization departments', $e->getMessage(), 500);
        }
    }

    /**
     * Получить пользователей организации
     */
    public function users(int $id): JsonResponse
    {
        try {
            $users = $this->organizationService->getOrganizationUsers($id);

            return response()->json([
                'success' => true,
                'message' => 'Organization users retrieved successfully',
                'data' => $users,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve organization users', $e->getMessage(), 500);
        }
    }

    /**
     * Получить полную структуру организации с отделами и сотрудниками
     */
    public function structure($id): JsonResponse
    {
        try {
            $organization = $this->organizationService->getOrganization($id);

            if (!$organization) {
                return $this->errorResponse('Organization not found', null, 404);
            }

            // Получаем отделы с пользователями и должностями
            $departments = $organization->departments()
                ->get()
                ->map(function($department) use ($organization) {
                    // Получаем пользователей для отдела напрямую по department_id
                    $departmentUsers = \App\Models\User::where('department_id', $department->id)
                        ->where('is_active', true)
                        ->get();

                    return [
                        'id' => $department->id,
                        'name' => $department->name,
                        'slug' => $department->slug,
                        'description' => $department->description,
                        'employees' => $departmentUsers->map(function($user) use ($organization, $department) {
                            // Получаем должность пользователя в этой организации и отделе
                            $position = $user->positions()
                                ->wherePivot('organization_id', $organization->id)
                                ->wherePivot('department_id', $department->id)
                                ->first();

                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'role' => $user->role,
                                'position' => $position ? [
                                    'id' => $position->id,
                                    'name' => $position->name,
                                    'slug' => $position->slug
                                ] : null,
                                'is_active' => $user->is_active,
                            ];
                        })->values()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Organization structure retrieved successfully',
                'data' => [
                    'organization' => [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'slug' => $organization->slug,
                        'description' => $organization->description,
                        'phone' => $organization->phone,
                        'is_active' => $organization->is_active,
                    ],
                    'departments' => $departments,
                    'total_employees' => $departments->sum(function($dept) {
                        return count($dept['employees']);
                    }),
                    'total_departments' => $departments->count(),
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve organization structure', $e->getMessage(), 500);
        }
    }
}



