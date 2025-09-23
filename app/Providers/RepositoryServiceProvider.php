<?php

namespace App\Providers;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\ChatRepositoryInterface;
use App\Contracts\MessageRepositoryInterface;
use App\Contracts\ClientRepositoryInterface;
use App\Contracts\DepartmentRepositoryInterface;
use App\Contracts\OrganizationRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\OptimizedUserRepository;
use App\Repositories\ChatRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\CachedUserRepository;
use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Регистрируем интерфейсы и их реализации
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            $baseRepository = new OptimizedUserRepository();
            $cacheService = $app->make(CacheService::class);
            return new CachedUserRepository($baseRepository, $cacheService);
        });

        $this->app->bind(ChatRepositoryInterface::class, ChatRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
