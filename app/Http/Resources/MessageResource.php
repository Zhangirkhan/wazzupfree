<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class MessageResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $metadata = $this->metadata ?? [];
        
        // Загружаем цитируемое сообщение если есть
        if (isset($metadata['reply_to_message_id'])) {
            $replyToMessage = \App\Models\Message::find($metadata['reply_to_message_id']);
            if ($replyToMessage) {
                $metadata['reply_to_message'] = [
                    'id' => $replyToMessage->id,
                    'content' => $replyToMessage->content,
                    'message' => $replyToMessage->content,
                    'type' => $replyToMessage->type,
                    'is_from_client' => $replyToMessage->is_from_client,
                    'file_name' => $replyToMessage->metadata['file_name'] ?? null,
                    'user' => $replyToMessage->user ? [
                        'id' => $replyToMessage->user->id,
                        'name' => $replyToMessage->user->name
                    ] : null
                ];
            }
        }
        
        return [
            'id' => $this->id,
            'content' => $this->content,
            'message' => $this->content, // Добавляем для совместимости с фронтендом
            'type' => $this->type,
            'is_from_client' => $this->is_from_client,
            'is_read' => $this->is_read,
            'read_at' => $this->formatDateTime($this->read_at),
            'file_path' => $this->getFilePath(),
            'file_name' => $this->getFileName(),
            'file_size' => $this->getFileSize(),
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            'metadata' => $metadata,
            'messenger_id' => $this->messenger_id,
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }

    private function getFilePath(): ?string
    {
        $metadata = $this->metadata ?? [];
        return $metadata['image_url'] ?? 
               $metadata['video_url'] ?? 
               $metadata['audio_url'] ?? 
               $metadata['document_url'] ?? 
               $metadata['file_path'] ?? null;
    }

    private function getFileName(): ?string
    {
        $metadata = $this->metadata ?? [];
        return $metadata['image_filename'] ?? 
               $metadata['video_filename'] ?? 
               $metadata['audio_filename'] ?? 
               $metadata['document_filename'] ?? 
               $metadata['file_name'] ?? null;
    }

    private function getFileSize(): ?int
    {
        $metadata = $this->metadata ?? [];
        return $metadata['image_size'] ?? 
               $metadata['video_size'] ?? 
               $metadata['audio_size'] ?? 
               $metadata['document_size'] ?? 
               $metadata['file_size'] ?? null;
    }
}