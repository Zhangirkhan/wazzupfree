<?php

namespace App\Contracts;

use App\Models\Client;
use App\Models\Chat;
use App\Models\Organization;

interface ClientManagerInterface
{
    /**
     * Поиск или создание клиента
     */
    public function findOrCreateClient(string $phone, ?array $contactData = null): Client;

    /**
     * Поиск или создание мессенджер чата
     */
    public function findOrCreateMessengerChat(string $phone, Client $client, ?Organization $organization = null): Chat;

    /**
     * Проверка является ли номер тестовым
     */
    public function isTestNumber(string $phone): bool;
}
