<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'domain' => $this->domain,
            'phone' => $this->phone,
            'webhook_url' => $this->getWebhookUrl(),
            'webhook_token' => $this->generateWebhookToken(),
            'wazzup24_enabled' => $this->wazzup24_enabled,
            'wazzup24_api_key' => $this->when($this->wazzup24_api_key, '***masked***'),
            'wazzup24_channel_id' => $this->wazzup24_channel_id,
            'wazzup24_webhook_url' => $this->wazzup24_webhook_url,
            'wazzup24_settings' => $this->wazzup24_settings,
            'is_wazzup24_configured' => $this->isWazzup24Configured(),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'departments_count' => $this->when(isset($this->departments_count), $this->departments_count),
            'users_count' => $this->when(isset($this->users_count), $this->users_count),
        ];
    }
}
