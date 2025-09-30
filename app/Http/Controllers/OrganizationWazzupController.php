<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Services\OrganizationWazzupService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class OrganizationWazzupController extends Controller
{
    /**
     * Получение настроек Wazzup24 для организации
     */
    public function getSettings(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        return response()->json([
            'organization' => $organization->name,
            'wazzup24_enabled' => $organization->wazzup24_enabled,
            'wazzup24_api_key' => $organization->wazzup24_api_key,
            'wazzup24_channel_id' => $organization->wazzup24_channel_id,
            'wazzup24_webhook_url' => $organization->wazzup24_webhook_url,
            'is_configured' => $organization->isWazzup24Configured(),
            'webhook_url' => $organization->getWebhookUrl(),
        ]);
    }

    /**
     * Обновление настроек Wazzup24 для организации
     */
    public function updateSettings(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $request->validate([
            'wazzup24_api_key' => 'nullable|string',
            'wazzup24_channel_id' => 'nullable|string',
            'wazzup24_webhook_url' => 'nullable|url',
            'wazzup24_enabled' => 'boolean',
        ]);

        $organization->update($request->only([
            'wazzup24_api_key',
            'wazzup24_channel_id',
            'wazzup24_webhook_url',
            'wazzup24_enabled',
        ]));

        Log::info('Wazzup24 settings updated for organization', [
            'organization_id' => $organization->id,
            'user_id' => $request->user()->id,
            'enabled' => $organization->wazzup24_enabled,
        ]);

        return response()->json([
            'message' => 'Настройки Wazzup24 обновлены',
            'organization' => $organization->fresh(),
        ]);
    }

    /**
     * Тестирование подключения к Wazzup24
     */
    public function testConnection(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->testConnection();

        return response()->json($result);
    }

    /**
     * Получение каналов Wazzup24
     */
    public function getChannels(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->getChannels();

        return response()->json($result);
    }

    /**
     * Настройка webhook'ов для организации
     */
    public function setupWebhooks(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        // Позволяем переопределять URL и подписки из запроса
        $webhookUrl = $request->input('webhook_url') ?: $organization->getWebhookUrl();
        $subscriptions = (array) $request->input('subscriptions', [
            'messagesAndStatuses' => true,
            'contactsAndDealsCreation' => false,
            'channelsUpdates' => false,
            'templateStatus' => false,
        ]);

        // Предварительная проверка доступности webhook URL
        try {
            $probe = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'Accept' => 'application/json'
            ])->get($webhookUrl);
            if ($probe->status() !== 200) {
                return response()->json([
                    'success' => false,
                    'error' => 'Webhook URL check failed',
                    'status' => $probe->status(),
                    'body' => substr($probe->body(), 0, 500)
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Webhook URL request exception: ' . $e->getMessage()
            ], 400);
        }

        // Если пришёл новый URL — сохраняем его в организации
        if ($request->filled('webhook_url')) {
            $organization->update(['wazzup24_webhook_url' => $webhookUrl]);
        }

        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->setupWebhooks($webhookUrl, $subscriptions);

        if ($result['success']) {
            Log::info('Webhooks configured for organization', [
                'organization_id' => $organization->id,
                'webhook_url' => $webhookUrl,
            ]);
        }

        return response()->json($result);
    }

    /**
     * Получить текущие настройки webhooks из Wazzup24 (webhooksUri и subscriptions)
     */
    public function webhooksStatus(Request $request, Organization $organization): JsonResponse
    {
        if (!Gate::allows('manageWazzup24', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        $wazzupService = new OrganizationWazzupService($organization);
        $status = $wazzupService->getWebhooks();

        return response()->json($status);
    }

    /**
     * Получение клиентов для организации
     */
    public function getClients(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('viewData', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        $limit = $request->get('limit', 100);
        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->getClients($limit);

        return response()->json($result);
    }

    /**
     * Отправка сообщения от имени организации
     */
    public function sendMessage(Request $request, Organization $organization): JsonResponse
    {
        // Проверяем права доступа
        if (!Gate::allows('viewData', $organization)) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        if (!$organization->isWazzup24Configured()) {
            return response()->json(['error' => 'Wazzup24 не настроен для организации'], 400);
        }

        $request->validate([
            'channel_id' => 'required|string',
            'chat_type' => 'required|string',
            'chat_id' => 'required|string',
            'text' => 'required|string',
        ]);

        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->sendMessage(
            $request->channel_id,
            $request->chat_type,
            $request->chat_id,
            $request->text
        );

        return response()->json($result);
    }

}
