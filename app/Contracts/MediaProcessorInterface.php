<?php

namespace App\Contracts;

interface MediaProcessorInterface
{
    /**
     * Обработка изображения
     */
    public function processImage(string $url, int $chatId, ?string $caption = null): ?array;

    /**
     * Обработка видео
     */
    public function processVideo(string $url, int $chatId, ?string $caption = null): ?array;

    /**
     * Обработка аудио
     */
    public function processAudio(string $url, int $chatId, ?string $caption = null): ?array;

    /**
     * Обработка документа
     */
    public function processDocument(string $url, int $chatId, string $filename, ?string $caption = null): ?array;

    /**
     * Обработка стикера
     */
    public function processSticker(string $url, int $chatId, ?string $caption = null): ?array;

    /**
     * Валидация медиа-файла
     */
    public function validateMediaFile(string $url, string $type): bool;

    /**
     * Получение информации о медиа-файле
     */
    public function getMediaInfo(string $url): ?array;
}
