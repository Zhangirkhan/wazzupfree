<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Contracts\ChatRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateChatActivity implements ShouldQueue
{
    public function __construct(
        private ChatRepositoryInterface $chatRepository
    ) {}

    public function handle(MessageSent $event): void
    {
        $chat = $event->message->chat;
        
        $this->chatRepository->update($chat, [
            'last_activity_at' => now(),
            'unread_count' => $event->message->is_from_client 
                ? $chat->unread_count + 1 
                : $chat->unread_count
        ]);
    }
}
