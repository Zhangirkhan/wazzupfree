<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'content' => $this->content,
            'type' => $this->type,
            'category' => $this->category,
            'variables' => $this->variables,
            'language' => $this->language,
            'is_active' => $this->is_active,
            'is_system' => $this->is_system,
            'usage_count' => $this->usage_count,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
        ];
    }
}
