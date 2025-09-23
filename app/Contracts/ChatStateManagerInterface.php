<?php

namespace App\Contracts;

use App\Models\Chat;
use App\Models\Client;

interface ChatStateManagerInterface
{
    /**
     * Обработка сообщения в зависимости от текущего состояния чата
     */
    public function processMessage(Chat $chat, string $message, Client $client, ?string $wazzupMessageId = null): void;

    /**
     * Обработка выбора из меню
     */
    public function handleMenuSelection(Chat $chat, string $message, Client $client): void;

    /**
     * Обработка выбора тестового номера
     */
    public function handleTestNumberSelection(Chat $chat, string $message, Client $client): void;

    /**
     * Обработка выбора отдела
     */
    public function handleDepartmentSelection(Chat $chat, string $message, Client $client): void;

    /**
     * Обработка активного чата
     */
    public function handleActiveChat(Chat $chat, string $message, Client $client): void;

    /**
     * Обработка завершенного чата
     */
    public function handleCompletedChat(Chat $chat, string $message, Client $client): void;

    /**
     * Обработка закрытого чата
     */
    public function handleClosedChat(Chat $chat, string $message, Client $client): void;

    /**
     * Сброс чата к меню
     */
    public function resetToMenu(Chat $chat, Client $client): void;

    /**
     * Отправка начального меню
     */
    public function sendInitialMenu(Chat $chat, Client $client): void;
}
