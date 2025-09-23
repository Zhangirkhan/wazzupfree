<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Contracts\WebhookHandlerInterface;

class WebhookController extends Controller
{
    public function __construct(
        private WebhookHandlerInterface $webhookHandler
    ) {}

    /**
     * Обработка webhook'ов от Wazzup24
     */
    public function wazzup24(Request $request): JsonResponse
    {
        return $this->webhookHandler->handle($request);
    }

    /**
     * Обработка webhook'ов для конкретной организации
     */
    public function organizationWebhook(Request $request, string $organization): JsonResponse
    {
        return $this->webhookHandler->handleForOrganization($request, $organization);
    }
}
