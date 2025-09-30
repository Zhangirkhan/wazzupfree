<?php

namespace App\Providers;

use App\Contracts\ChatStateManagerInterface;
use App\Contracts\ClientManagerInterface;
use App\Contracts\MessageProcessorInterface;
use App\Contracts\WebhookHandlerInterface;
use App\Contracts\WebhookMessageProcessorInterface;
use App\Contracts\WebhookStatusProcessorInterface;
use App\Contracts\WebhookContactProcessorInterface;
use App\Contracts\MediaProcessorInterface;
use App\Services\Messenger\ChatStateManager;
use App\Services\Messenger\ClientManager;
use App\Services\Messenger\MessageProcessor;
use App\Services\Webhook\WebhookHandler;
use App\Services\Webhook\WebhookMessageProcessor;
use App\Services\Webhook\WebhookStatusProcessor;
use App\Services\Webhook\WebhookContactProcessor;
use App\Services\Media\MediaProcessor;
use App\Services\Media\MediaManager;
use Illuminate\Support\ServiceProvider;

class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем SystemMessageService как singleton
        $this->app->singleton(\App\Services\SystemMessageService::class);
        
        // Регистрируем MessageProcessor как singleton
        $this->app->singleton(MessageProcessorInterface::class, MessageProcessor::class);
        
        // Регистрируем ClientManager как singleton
        $this->app->singleton(ClientManagerInterface::class, ClientManager::class);
        
        // Регистрируем ChatStateManager с зависимостями
        $this->app->singleton(ChatStateManagerInterface::class, function ($app) {
            return new ChatStateManager(
                $app->make(MessageProcessorInterface::class),
                $app->make(\App\Services\SystemMessageService::class)
            );
        });

        // Регистрируем Webhook сервисы
        $this->app->singleton(WebhookMessageProcessorInterface::class, function ($app) {
            return new WebhookMessageProcessor($app->make(\App\Services\MessengerService::class));
        });

        $this->app->singleton(WebhookStatusProcessorInterface::class, WebhookStatusProcessor::class);
        $this->app->singleton(WebhookContactProcessorInterface::class, WebhookContactProcessor::class);

        $this->app->singleton(WebhookHandlerInterface::class, function ($app) {
            return new WebhookHandler(
                $app->make(WebhookMessageProcessorInterface::class),
                $app->make(WebhookStatusProcessorInterface::class),
                $app->make(WebhookContactProcessorInterface::class)
            );
        });

        // Регистрируем Media сервисы
        $this->app->singleton(MediaProcessorInterface::class, function ($app) {
            return new MediaProcessor(
                $app->make(\App\Services\ImageService::class),
                $app->make(\App\Services\VideoService::class),
                $app->make(\App\Services\AudioService::class),
                $app->make(\App\Services\DocumentService::class),
                $app->make(\App\Services\StickerService::class)
            );
        });

        $this->app->singleton(MediaManager::class, function ($app) {
            return new MediaManager($app->make(MediaProcessorInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
