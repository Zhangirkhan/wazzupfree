<?php

namespace App\Services\Media;

use App\Services\ImageService;
use App\Services\VideoService;
use App\Services\AudioService;
use App\Services\DocumentService;
use App\Services\StickerService;

class MediaFactory
{
    /**
     * Создание MediaProcessor с внедренными зависимостями
     */
    public static function createMediaProcessor(): MediaProcessor
    {
        return new MediaProcessor(
            app(ImageService::class),
            app(VideoService::class),
            app(AudioService::class),
            app(DocumentService::class),
            app(StickerService::class)
        );
    }

    /**
     * Создание MediaManager
     */
    public static function createMediaManager(): MediaManager
    {
        return new MediaManager(
            self::createMediaProcessor()
        );
    }

    /**
     * Создание медиа-сервиса по типу
     */
    public static function createMediaService(string $type): mixed
    {
        return match ($type) {
            'image' => app(ImageService::class),
            'video' => app(VideoService::class),
            'audio' => app(AudioService::class),
            'document' => app(DocumentService::class),
            'sticker' => app(StickerService::class),
            default => throw new \InvalidArgumentException("Unknown media type: {$type}")
        };
    }

    /**
     * Получение всех доступных медиа-сервисов
     */
    public static function getAllMediaServices(): array
    {
        return [
            'image' => app(ImageService::class),
            'video' => app(VideoService::class),
            'audio' => app(AudioService::class),
            'document' => app(DocumentService::class),
            'sticker' => app(StickerService::class)
        ];
    }
}
