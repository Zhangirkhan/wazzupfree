<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $unreadCount = 0;

        // Считаем непрочитанные сообщения для текущего пользователя
        if ($user) {
            // Считаем сообщения от клиентов (is_from_client = true) которые не прочитаны
            // Если нет записей в message_reads, то сообщение считается непрочитанным
            $unreadCount = \App\Models\Message::where('chat_id', $this->id)
                ->where('is_from_client', true) // Только сообщения от клиентов
                ->whereDoesntHave('reads', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();
        }

        // Имя клиента: приоритет реальному имени из Client, затем title, затем телефон
        $clientName = $this->client && $this->client->name
            ? $this->client->name
            : ($this->title ?: ($this->messenger_phone ?? $this->phone));

        return [
            'id' => $this->id,
            'client_name' => $clientName,
            'client_phone' => $this->messenger_phone ?? $this->phone,
            'client_email' => $this->messenger_data['email'] ?? null, // Из messenger_data
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'user' => new UserResource($this->whenLoaded('creator')),
            'assigned_user' => new UserResource($this->whenLoaded('assignedTo')),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'messages_count' => $this->when(isset($this->messages_count), $this->messages_count),
            'unread_count' => $unreadCount,
            'last_message' => $this->when(true, function() {
                // Всегда получаем последнее сообщение отдельным запросом для актуальности
                $lastMessage = \App\Models\Message::where('chat_id', $this->id)
                    ->latest()
                    ->first();
                return $lastMessage ? new MessageResource($lastMessage) : null;
            }),
        ];
    }
}
