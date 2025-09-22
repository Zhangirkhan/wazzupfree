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

        // Определяем путь к файлу в зависимости от типа сообщения
        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($this->type === 'image') {
            $filePath = $this->metadata['image_url'] ?? $this->metadata['file_path'] ?? null;
            $fileName = $this->metadata['image_filename'] ?? $this->metadata['file_name'] ?? null;
            $fileSize = $this->metadata['image_size'] ?? $this->metadata['file_size'] ?? null;
        } elseif ($this->type === 'video') {
            $filePath = $this->metadata['video_url'] ?? $this->metadata['file_path'] ?? null;
            $fileName = $this->metadata['video_filename'] ?? $this->metadata['file_name'] ?? null;
            $fileSize = $this->metadata['video_size'] ?? $this->metadata['file_size'] ?? null;
        } elseif ($this->type === 'document') {
            $filePath = $this->metadata['document_url'] ?? $this->metadata['file_path'] ?? null;
            $fileName = $this->metadata['document_filename'] ?? $this->metadata['file_name'] ?? null;
            $fileSize = $this->metadata['document_size'] ?? $this->metadata['file_size'] ?? null;
        } else {
            $filePath = $this->metadata['file_path'] ?? null;
            $fileName = $this->metadata['file_name'] ?? null;
            $fileSize = $this->metadata['file_size'] ?? null;
        }

        return [
            'id' => $this->id,
            'message' => $this->content,
            'type' => $this->type,
            'is_from_client' => $this->is_from_client ?? ($this->direction === 'in'),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'created_at' => $this->created_at->toISOString(),
            'is_read' => $isRead,
            'read_at' => $readAt,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
