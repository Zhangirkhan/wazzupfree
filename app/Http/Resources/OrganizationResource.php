<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrganizationResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'active' : 'inactive',
            'status_label' => $this->getStatusLabel($this->is_active ? 'active' : 'inactive'),
            'status_severity' => $this->getStatusSeverity($this->is_active ? 'active' : 'inactive'),
            // Счётчики: берем атрибуты *_count если есть (из withCount), иначе считаем загруженные отношения
            'users_count' => $this->users_count ?? ($this->relationLoaded('users') ? $this->users->count() : 0),
            'departments_count' => $this->departments_count ?? ($this->relationLoaded('departments') ? $this->departments->count() : 0),
            // Параметры интеграции Wazzup24
            'wazzup24_enabled' => (bool) $this->wazzup24_enabled,
            'wazzup24_api_key' => $this->wazzup24_api_key,
            'wazzup24_channel_id' => $this->wazzup24_channel_id,
            'webhook_url' => method_exists($this, 'getWebhookUrl') ? $this->getWebhookUrl() : null,
            'clients_count' => $this->whenLoaded('clients', function() {
                return $this->clients->count();
            }),
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }
}