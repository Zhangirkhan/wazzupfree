<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'role_label' => $this->getRoleLabel($this->role),
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'active' : 'inactive',
            'status_label' => $this->getStatusLabel($this->is_active ? 'active' : 'inactive'),
            'status_severity' => $this->getStatusSeverity($this->is_active ? 'active' : 'inactive'),
            'department' => $this->whenLoaded('department', function() {
                return new DepartmentResource($this->department);
            }),
            'organizations' => $this->whenLoaded('organizations', function() {
                return OrganizationResource::collection($this->organizations);
            }),
            'positions' => $this->whenLoaded('positions', function() {
                return PositionResource::collection($this->positions);
            }),
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at),
            'last_login_at' => $this->formatDateTime($this->last_login_at)
        ];
    }

    private function getRoleLabel(string $role): string
    {
        $roleMap = [
            'admin' => 'Администратор',
            'manager' => 'Менеджер',
            'employee' => 'Сотрудник'
        ];

        return $roleMap[$role] ?? $role;
    }
}