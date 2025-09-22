<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $isRead = false;
        $readAt = null;

        // Проверяем, прочитано ли сообщение текущим пользователем
        if ($user) {
            $isRead = $this->isReadBy($user->id);
            $readAt = $this->getReadTimeBy($user->id);
        }

        return [
            'id' => $this->id,
            'message' => $this->content,
            'type' => $this->type,
            'is_from_client' => $this->is_from_client ?? ($this->direction === 'in'),
            'file_path' => $this->metadata['file_path'] ?? null,
            'file_name' => $this->metadata['file_name'] ?? null,
            'file_size' => $this->metadata['file_size'] ?? null,
            'created_at' => $this->created_at->toISOString(),
            'is_read' => $isRead,
            'read_at' => $readAt,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
