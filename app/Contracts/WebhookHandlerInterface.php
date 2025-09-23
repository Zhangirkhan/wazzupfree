<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface WebhookHandlerInterface
{
    /**
     * Обработка webhook'а от Wazzup24
     */
    public function handle(Request $request): JsonResponse;

    /**
     * Обработка webhook'а для организации
     */
    public function handleForOrganization(Request $request, string $organization): JsonResponse;

    /**
     * Валидация webhook'а
     */
    public function validate(Request $request): void;
}
