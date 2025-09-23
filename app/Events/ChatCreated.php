<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Chat $chat
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.' . $this->chat->client->organization_id . '.chats'),
            new PrivateChannel('user.' . $this->chat->created_by . '.chats')
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'chat_created',
            'chat' => [
                'id' => $this->chat->id,
                'title' => $this->chat->title,
                'client_name' => $this->chat->client->name ?? 'Неизвестный клиент',
                'last_message' => $this->chat->messages->last()?->content,
                'unread_count' => $this->chat->unread_count,
                'created_at' => $this->chat->created_at->toISOString()
            ]
        ];
    }
}
