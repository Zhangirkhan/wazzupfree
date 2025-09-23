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
            'description' => $this->description,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'active' : 'inactive',
            'status_label' => $this->getStatusLabel($this->is_active ? 'active' : 'inactive'),
            'status_severity' => $this->getStatusSeverity($this->is_active ? 'active' : 'inactive'),
            'users_count' => $this->whenLoaded('users', function() {
                return $this->users->count();
            }),
            'departments_count' => $this->whenLoaded('departments', function() {
                return $this->departments->count();
            }),
            'clients_count' => $this->whenLoaded('clients', function() {
                return $this->clients->count();
            }),
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }
}