<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\ChatServiceInterface;
use App\Contracts\MessageServiceInterface;
use App\Services\AuthService;
use App\Services\ChatService;
use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(ChatServiceInterface::class, ChatService::class);
        $this->app->bind(MessageServiceInterface::class, MessageService::class);
        
            // Регистрируем новые сервисы
            $this->app->singleton(\App\Services\CacheService::class);
            $this->app->singleton(\App\Services\NotificationService::class);
            $this->app->singleton(\App\Services\LoggingService::class);
            $this->app->singleton(\App\Contracts\UnitOfWorkInterface::class, \App\Services\UnitOfWorkService::class);
            $this->app->singleton(\App\Contracts\SagaInterface::class, \App\Services\SagaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
