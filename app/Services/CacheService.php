<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public function flush(): bool
    {
        return Cache::flush();
    }

    // Специальные методы для чатов
    public function getChatsCacheKey(int $userId, int $organizationId): string
    {
        return "user_chats:{$userId}:org:{$organizationId}";
    }

    public function getChatCacheKey(int $chatId): string
    {
        return "chat:{$chatId}";
    }

    public function getUserCacheKey(int $userId): string
    {
        return "user:{$userId}";
    }

    public function getMessagesCacheKey(int $chatId): string
    {
        return "chat_messages:{$chatId}";
    }

    public function getClientsCacheKey(int $organizationId): string
    {
        return "organization_clients:{$organizationId}";
    }

    public function getDepartmentsCacheKey(int $organizationId): string
    {
        return "organization_departments:{$organizationId}";
    }

    public function invalidateUserChats(int $userId, int $organizationId): void
    {
        $this->forget($this->getChatsCacheKey($userId, $organizationId));
    }

    public function invalidateChat(int $chatId): void
    {
        $this->forget($this->getChatCacheKey($chatId));
        $this->forget($this->getMessagesCacheKey($chatId));
    }

    public function invalidateUser(int $userId): void
    {
        $this->forget($this->getUserCacheKey($userId));
    }

    public function invalidateOrganizationClients(int $organizationId): void
    {
        $this->forget($this->getClientsCacheKey($organizationId));
    }

    public function invalidateOrganizationDepartments(int $organizationId): void
    {
        $this->forget($this->getDepartmentsCacheKey($organizationId));
    }

    // Методы для работы с Redis
    public function setRedis(string $key, $value, int $ttl = 3600): void
    {
        Redis::setex($key, $ttl, json_encode($value));
    }

    public function getRedis(string $key)
    {
        $value = Redis::get($key);
        return $value ? json_decode($value, true) : null;
    }

    public function deleteRedis(string $key): void
    {
        Redis::del($key);
    }

    public function existsRedis(string $key): bool
    {
        return Redis::exists($key) > 0;
    }
}
