<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionService
{
    public function getPositions(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        $query = Position::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        return $query->orderBy('name')
                    ->paginate($perPage);
    }

    public function getPosition(int $id): ?Position
    {
        return Position::find($id);
    }

    public function createPosition(array $data): Position
    {
        return DB::transaction(function () use ($data) {
            $position = Position::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'permissions' => $data['permissions'] ?? [],
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Position created', [
                'position_id' => $position->id,
                'name' => $position->name
            ]);

            return $position;
        });
    }

    public function updatePosition(int $id, array $data): Position
    {
        $position = Position::findOrFail($id);

        return DB::transaction(function () use ($position, $data) {
            $position->update([
                'name' => $data['name'] ?? $position->name,
                'slug' => $data['slug'] ?? $position->slug,
                'description' => $data['description'] ?? $position->description,
                'permissions' => $data['permissions'] ?? $position->permissions,
                'is_active' => $data['is_active'] ?? $position->is_active,
            ]);

            Log::info('Position updated', [
                'position_id' => $position->id,
                'name' => $position->name
            ]);

            return $position;
        });
    }

    public function deletePosition(int $id): bool
    {
        $position = Position::findOrFail($id);

        // Проверяем, есть ли пользователи с этой должностью
        if ($position->users()->count() > 0) {
            throw new \Exception('Cannot delete position with assigned users');
        }

        $position->delete();

        Log::info('Position deleted', [
            'position_id' => $id
        ]);

        return true;
    }
}





