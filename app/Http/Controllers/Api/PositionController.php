<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PositionResource;
use App\Services\PositionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PositionController extends ApiController
{
    public function __construct(
        private PositionService $positionService
    ) {}

    /**
     * Получить список должностей
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $positions = $this->positionService->getPositions($perPage, $search);

            return $this->paginatedResponse(
                PositionResource::collection($positions),
                'Positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve positions', $e->getMessage(), 500);
        }
    }

    /**
     * Получить должность по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $position = $this->positionService->getPosition($id);

            if (!$position) {
                return $this->notFoundResponse('Position not found');
            }

            return $this->successResponse(
                new PositionResource($position),
                'Position retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve position', $e->getMessage(), 500);
        }
    }

    /**
     * Создать должность
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:positions,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean'
        ]);

        try {
            $position = $this->positionService->createPosition($request->all());

            return $this->successResponse(
                new PositionResource($position),
                'Position created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create position', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить должность
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:positions,slug,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean'
        ]);

        try {
            $position = $this->positionService->updatePosition($id, $request->all());

            return $this->successResponse(
                new PositionResource($position),
                'Position updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update position', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить должность
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->positionService->deletePosition($id);

            return $this->successResponse(null, 'Position deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete position', $e->getMessage(), 500);
        }
    }
}





