<?php

namespace App\Contracts;

interface WebhookMessageProcessorInterface
{
    /**
     * Обработка массива сообщений
     */
    public function handleMessages(array $messages): array;

    /**
     * Обработка одного сообщения
     */
    public function processMessage(array $message): bool;

    /**
     * Обработка текстового сообщения
     */
    public function processTextMessage(array $message): bool;

    /**
     * Обработка изображения
     */
    public function processImageMessage(array $message): bool;

    /**
     * Обработка видео
     */
    public function processVideoMessage(array $message): bool;

    /**
     * Обработка стикера
     */
    public function processStickerMessage(array $message): bool;

    /**
     * Обработка документа
     */
    public function processDocumentMessage(array $message): bool;

    /**
     * Обработка аудио
     */
    public function processAudioMessage(array $message): bool;

    /**
     * Обработка геолокации
     */
    public function processLocationMessage(array $message): bool;
}
