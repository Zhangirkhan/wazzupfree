<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PositionResource extends BaseResource
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
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }
}