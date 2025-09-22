<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    public function getDepartments(int $perPage = 20, ?string $search = null, ?int $organizationId = null): LengthAwarePaginator
    {
        $query = Department::with('organization');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query->orderBy('name')
                    ->paginate($perPage);
    }

    public function getDepartment(int $id): ?Department
    {
        return Department::with(['organization', 'leader'])->find($id);
    }

    public function createDepartment(array $data): Department
    {
        return DB::transaction(function () use ($data) {
            // Проверяем существование организации
            $organization = Organization::findOrFail($data['organization_id']);

            $department = Department::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'organization_id' => $data['organization_id'],
                'is_active' => $data['is_active'] ?? true,
                'show_in_chatbot' => $data['show_in_chatbot'] ?? false,
                'chatbot_order' => $data['chatbot_order'] ?? 0,
            ]);

            Log::info('Department created', [
                'department_id' => $department->id,
                'name' => $department->name,
                'organization_id' => $department->organization_id
            ]);

            return $department->load(['organization', 'leader']);
        });
    }

    public function updateDepartment(int $id, array $data): Department
    {
        $department = Department::findOrFail($id);

        return DB::transaction(function () use ($department, $data) {
            if (isset($data['organization_id'])) {
                // Проверяем существование организации
                Organization::findOrFail($data['organization_id']);
            }

            $department->update([
                'name' => $data['name'] ?? $department->name,
                'slug' => $data['slug'] ?? $department->slug,
                'description' => $data['description'] ?? $department->description,
                'organization_id' => $data['organization_id'] ?? $department->organization_id,
                'is_active' => $data['is_active'] ?? $department->is_active,
                'show_in_chatbot' => $data['show_in_chatbot'] ?? $department->show_in_chatbot,
                'chatbot_order' => $data['chatbot_order'] ?? $department->chatbot_order,
            ]);

            Log::info('Department updated', [
                'department_id' => $department->id,
                'name' => $department->name
            ]);

            return $department->load(['organization', 'leader']);
        });
    }

    public function deleteDepartment(int $id): bool
    {
        $department = Department::findOrFail($id);

        // Проверяем, есть ли сотрудники в отделе
        if ($department->users()->count() > 0) {
            throw new \Exception('Cannot delete department with users');
        }

        $department->delete();

        Log::info('Department deleted', [
            'department_id' => $id
        ]);

        return true;
    }

    public function getDepartmentUsers(int $departmentId): array
    {
        $department = Department::find($departmentId);

        if (!$department) {
            return [];
        }

        return $department->users()->get()->toArray();
    }

    public function getDepartmentSupervisors(int $departmentId): array
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем пустой массив
        return [];
    }

    public function getDepartmentManagers(int $departmentId): array
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем пустой массив
        return [];
    }

    public function assignSupervisor(int $userId, int $departmentId): bool
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем true
        return true;
    }

    public function removeSupervisor(int $userId, int $departmentId): bool
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем true
        return true;
    }

    public function assignManager(int $userId, int $departmentId): bool
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем true
        return true;
    }

    public function removeManager(int $userId, int $departmentId): bool
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем true
        return true;
    }
}



