<?php

namespace App\Listeners;

use App\Events\ChatCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChatNotification implements ShouldQueue
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle(ChatCreated $event): void
    {
        $this->notificationService->sendChatCreatedNotification($event->chat);
    }
}
