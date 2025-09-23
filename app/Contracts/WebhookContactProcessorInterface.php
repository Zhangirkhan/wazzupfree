<?php

namespace App\Contracts;

interface WebhookContactProcessorInterface
{
    /**
     * Обработка массива контактов
     */
    public function handleContacts(array $contacts): array;

    /**
     * Обработка одного контакта
     */
    public function processContact(array $contactData): bool;
}
