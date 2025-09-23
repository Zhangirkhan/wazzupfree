<?php

namespace App\Contracts;

use App\Models\Chat;
use App\Models\Client;
use App\Models\Message;

interface MessageProcessorInterface
{
    /**
     * Сохранение текстового сообщения от клиента
     */
    public function saveClientMessage(Chat $chat, string $message, Client $client, ?string $wazzupMessageId = null): Message;

    /**
     * Сохранение изображения от клиента
     */
    public function saveClientImage(Chat $chat, string $imageUrl, string $caption, Client $client, ?string $wazzupMessageId = null): Message;

    /**
     * Сохранение видео от клиента
     */
    public function saveClientVideo(Chat $chat, string $videoUrl, string $caption, Client $client): Message;

    /**
     * Сохранение аудио от клиента
     */
    public function saveClientAudio(Chat $chat, string $audioUrl, string $caption, Client $client): Message;

    /**
     * Сохранение стикера от клиента
     */
    public function saveClientSticker(Chat $chat, string $stickerUrl, string $caption, Client $client): Message;

    /**
     * Сохранение документа от клиента
     */
    public function saveClientDocument(Chat $chat, string $documentUrl, string $documentName, string $caption, Client $client): Message;

    /**
     * Сохранение геолокации от клиента
     */
    public function saveClientLocation(Chat $chat, float $latitude, float $longitude, string $address, Client $client): Message;

    /**
     * Отправка сообщения в чат
     */
    public function sendMessage(Chat $chat, string $message): void;

    /**
     * Уведомление отдела о новом сообщении
     */
    public function notifyDepartment(Chat $chat, string $message): void;

    /**
     * Уведомление назначенного пользователя
     */
    public function notifyAssignedUser(Chat $chat, string $message): void;
}
