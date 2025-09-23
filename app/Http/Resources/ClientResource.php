<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ClientResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'status' => $this->is_active ? 'active' : 'inactive',
            'status_label' => $this->getStatusLabel($this->is_active ? 'active' : 'inactive'),
            'status_severity' => $this->getStatusSeverity($this->is_active ? 'active' : 'inactive'),
            'organization' => $this->whenLoaded('organization', function() {
                return new OrganizationResource($this->organization);
            }),
            'company' => $this->whenLoaded('company', function() {
                return new CompanyResource($this->company);
            }),
            'chats_count' => $this->whenLoaded('chats', function() {
                return $this->chats->count();
            }),
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }
}