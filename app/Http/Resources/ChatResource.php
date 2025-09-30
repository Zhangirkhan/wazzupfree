<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ChatResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel($this->status),
            'status_severity' => $this->getStatusSeverity($this->status),
            'is_messenger_chat' => $this->is_messenger_chat,
            'messenger_phone' => $this->messenger_phone,
            'messenger_status' => $this->messenger_status,
            'unread_count' => $this->unread_count,
            'last_activity_at' => $this->formatDateTime($this->last_activity_at),
            // Добавляем поля для совместимости с фронтендом
            'client_name' => $this->client ? $this->client->name : $this->title,
            'client_phone' => $this->client ? $this->client->phone : $this->phone,
            'client_email' => $this->client ? $this->client->email : null,
            'client' => $this->whenLoaded('client', function() {
                return new ClientResource($this->client);
            }),
            'department' => $this->whenLoaded('department', function() {
                return new DepartmentResource($this->department);
            }),
            'assigned_to' => $this->whenLoaded('assignedTo', function() {
                return new UserResource($this->assignedTo);
            }),
            'last_message' => $this->whenLoaded('lastMessage', function() {
                return new MessageResource($this->lastMessage);
            }),
            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->formatDateTime($this->updated_at)
        ];
    }
}