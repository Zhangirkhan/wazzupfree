<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizationService
{
    public function getOrganizations(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        $query = Organization::query()
            ->withCount(['departments', 'users']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        return $query->orderBy('name')
                    ->paginate($perPage);
    }

    public function getOrganization(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function createOrganization(array $data): Organization
    {
        return DB::transaction(function () use ($data) {
            $organization = Organization::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'] ?? null,
                'wazzup24_enabled' => $data['wazzup24_enabled'] ?? false,
                'wazzup24_api_key' => $data['wazzup24_api_key'] ?? null,
                'wazzup24_channel_id' => $data['wazzup24_channel_id'] ?? null,
                'wazzup24_webhook_url' => $data['webhook_url'] ?? $data['wazzup24_webhook_url'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Organization created', [
                'organization_id' => $organization->id,
                'name' => $organization->name
            ]);

            return $organization;
        });
    }

    public function updateOrganization(int $id, array $data): Organization
    {
        $organization = Organization::findOrFail($id);

        return DB::transaction(function () use ($organization, $data) {

            // Безопасно вычисляем webhook URL (учитываем оба названия поля и пустые строки)
            $webhookUrl = $organization->wazzup24_webhook_url;
            if (array_key_exists('webhook_url', $data)) {
                $webhookUrl = ($data['webhook_url'] === '' || $data['webhook_url'] === null) ? null : $data['webhook_url'];
            } elseif (array_key_exists('wazzup24_webhook_url', $data)) {
                $webhookUrl = ($data['wazzup24_webhook_url'] === '' || $data['wazzup24_webhook_url'] === null) ? null : $data['wazzup24_webhook_url'];
            }

            $organization->update([
                'name' => $data['name'] ?? $organization->name,
                'slug' => array_key_exists('slug', $data) ? $data['slug'] : $organization->slug,
                'description' => $data['description'] ?? $organization->description,
                'phone' => $data['phone'] ?? $organization->phone,
                'wazzup24_enabled' => $data['wazzup24_enabled'] ?? $organization->wazzup24_enabled,
                'wazzup24_api_key' => $data['wazzup24_api_key'] ?? $organization->wazzup24_api_key,
                'wazzup24_channel_id' => $data['wazzup24_channel_id'] ?? $organization->wazzup24_channel_id,
                'wazzup24_webhook_url' => $webhookUrl,
                'is_active' => $data['is_active'] ?? $organization->is_active,
            ]);

            Log::info('Organization updated', [
                'organization_id' => $organization->id,
                'name' => $organization->name
            ]);

            return $organization;
        });
    }

    public function deleteOrganization(int $id): bool
    {
        $organization = Organization::findOrFail($id);

        // Проверяем, есть ли связанные отделы
        if ($organization->departments()->count() > 0) {
            throw new \Exception('Cannot delete organization with departments');
        }

        $organization->delete();

        Log::info('Organization deleted', [
            'organization_id' => $id
        ]);

        return true;
    }

    public function getOrganizationDepartments(int $organizationId): array
    {
        $organization = Organization::find($organizationId);

        if (!$organization) {
            return [];
        }

        return $organization->departments()->get()->toArray();
    }

    public function getOrganizationUsers(int $organizationId): array
    {
        $organization = Organization::find($organizationId);

        if (!$organization) {
            return [];
        }

        return $organization->users()->get()->toArray();
    }
}



