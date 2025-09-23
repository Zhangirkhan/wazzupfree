<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Chat $chat,
        public User $assignedUser,
        public User $assignedBy
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->assignedUser->id . '.chats'),
            new PrivateChannel('organization.' . $this->chat->client->organization_id . '.chats')
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'chat_assigned',
            'chat' => [
                'id' => $this->chat->id,
                'title' => $this->chat->title,
                'assigned_to' => $this->assignedUser->name,
                'assigned_by' => $this->assignedBy->name,
                'assigned_at' => now()->toISOString()
            ]
        ];
    }
}
