<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'organization_id' => $this->organization_id,
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'is_active' => $this->is_active,
            'show_in_chatbot' => $this->show_in_chatbot,
            'chatbot_order' => $this->chatbot_order,
            'leader' => $this->when($this->relationLoaded('leader'), function() {
                return $this->leader ? [
                    'id' => $this->leader->id,
                    'name' => $this->leader->name,
                    'email' => $this->leader->email,
                ] : null;
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'users_count' => $this->when(isset($this->users_count), $this->users_count),
        ];
    }
}





