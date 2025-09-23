<?php

namespace App\Listeners;

use App\Events\ChatAssigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAssignmentNotification implements ShouldQueue
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handle(ChatAssigned $event): void
    {
        $this->notificationService->sendChatAssignmentNotification(
            $event->chat,
            $event->assignedUser,
            $event->assignedBy
        );
    }
}
