<?php

namespace App\Providers;

use App\Events\ChatCreated;
use App\Events\MessageSent;
use App\Events\ChatAssigned;
use App\Events\UserCreated;
use App\Listeners\SendChatNotification;
use App\Listeners\UpdateChatActivity;
use App\Listeners\SendAssignmentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ChatCreated::class => [
            SendChatNotification::class,
        ],
        
        MessageSent::class => [
            UpdateChatActivity::class,
        ],
        
        ChatAssigned::class => [
            SendAssignmentNotification::class,
        ],
        
        UserCreated::class => [
            // Здесь можно добавить слушатели для создания пользователя
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
