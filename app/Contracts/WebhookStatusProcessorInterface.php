<?php

namespace App\Contracts;

interface WebhookStatusProcessorInterface
{
    /**
     * Обработка массива статусов
     */
    public function handleStatuses(array $statuses): array;

    /**
     * Обработка одного статуса
     */
    public function processStatus(array $statusData): bool;
}
