<?php

namespace App\Services\Webhook;

use App\Contracts\WebhookHandlerInterface;
use App\Contracts\WebhookMessageProcessorInterface;
use App\Contracts\WebhookStatusProcessorInterface;
use App\Contracts\WebhookContactProcessorInterface;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookHandler implements WebhookHandlerInterface
{
    public function __construct(
        private WebhookMessageProcessorInterface $messageProcessor,
        private WebhookStatusProcessorInterface $statusProcessor,
        private WebhookContactProcessorInterface $contactProcessor
    ) {}

    /**
     * Обработка webhook'а от Wazzup24
     */
    public function handle(Request $request): JsonResponse
    {
        // Логируем КАЖДЫЙ входящий запрос с максимальной детализацией
        Log::info('=== WEBHOOK RECEIVED ===', [
            'timestamp' => now()->toDateTimeString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'accept' => $request->header('Accept'),
            'authorization' => $request->header('Authorization') ? 'Present' : 'Not present',
            'all_headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'body_params' => $request->all(),
            'raw_body' => $request->getContent(),
            'body_size' => strlen($request->getContent()),
            'has_files' => $request->hasFile('file'),
            'files_count' => count($request->allFiles())
        ]);

        // Обрабатываем GET запросы (для тестирования и проверки доступности)
        if ($request->method() === 'GET') {
            Log::info('=== GET REQUEST HANDLED ===');
            $response = response()->json([
                'status' => 'success',
                'message' => 'Webhook endpoint is accessible',
                'timestamp' => now()->toDateTimeString(),
                'method' => 'GET'
            ], 200);

            Log::info('=== GET RESPONSE ===', [
                'status' => $response->getStatusCode(),
                'response' => $response->getContent()
            ]);

            return $response;
        }

        try {
            // Проверяем, что webhook включен
            if (!config('wazzup24.webhook.enabled', true)) {
                Log::warning('Webhook disabled in config');
                return response()->json(['error' => 'Webhook disabled'], 400);
            }

            // Валидируем webhook
            $this->validate($request);

            $data = $request->all();
            Log::info('Webhook data parsed:', ['data' => $data]);

            // Проверяем тестовый запрос от Wazzup24
            if (isset($data['test']) && $data['test'] === true) {
                Log::info('=== WAZZUP24 TEST WEBHOOK RECEIVED ===');
                $response = response()->json(['status' => 'success'], 200);

                Log::info('=== TEST WEBHOOK RESPONSE ===', [
                    'status' => $response->getStatusCode(),
                    'response' => $response->getContent()
                ]);

                return $response;
            }

            // Логируем входящий webhook
            if (config('wazzup24.logging.enabled')) {
                Log::channel(config('wazzup24.logging.channel'))->info('Wazzup24: Incoming webhook', $data);
            }

            // Обрабатываем новый формат webhook'а Wazzup24
            Log::info('Processing webhook data structure:', array_keys($data));

            // Проверяем наличие сообщений
            if (isset($data['messages']) && is_array($data['messages'])) {
                Log::info('Found messages array with ' . count($data['messages']) . ' messages');
                return $this->handleMessages($data['messages']);
            }

            // Проверяем наличие статусов
            if (isset($data['statuses']) && is_array($data['statuses'])) {
                Log::info('Found statuses array with ' . count($data['statuses']) . ' statuses');
                return $this->handleStatuses($data['statuses']);
            }

            // Проверяем наличие контактов
            if (isset($data['contacts']) && is_array($data['contacts'])) {
                Log::info('Found contacts array with ' . count($data['contacts']) . ' contacts');
                return $this->handleContacts($data['contacts']);
            }

            // Проверяем старый формат для совместимости
            if (isset($data['type'])) {
                Log::info('Processing legacy webhook type: ' . $data['type']);
                return $this->handleLegacyWebhook($data);
            }

            Log::warning('Unknown webhook format:', $data);
            return response()->json(['error' => 'Unknown webhook format'], 400);

        } catch (\Exception $e) {
            Log::error('Webhook processing error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обработка webhook'а для организации
     */
    public function handleForOrganization(Request $request, string $organization): JsonResponse
    {
        try {
            Log::info('=== ORGANIZATION WEBHOOK RECEIVED ===', [
                'organization' => $organization,
                'timestamp' => now()->toDateTimeString(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            // Находим организацию
            $org = Organization::where('slug', $organization)->first();
            if (!$org) {
                Log::warning('Organization not found:', ['organization' => $organization]);
                return response()->json(['error' => 'Organization not found'], 404);
            }

            // Обрабатываем GET запросы
            if ($request->method() === 'GET') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Organization webhook endpoint is accessible',
                    'organization' => $organization,
                    'timestamp' => now()->toDateTimeString()
                ], 200);
            }

            $data = $request->all();
            Log::info('Organization webhook data:', ['data' => $data]);

            // Обрабатываем сообщения для организации
            if (isset($data['messages']) && is_array($data['messages'])) {
                return $this->handleMessagesForOrganization($data['messages'], $org);
            }

            // Обрабатываем статусы для организации
            if (isset($data['statuses']) && is_array($data['statuses'])) {
                return $this->handleStatusesForOrganization($data['statuses'], $org);
            }

            // Обрабатываем контакты для организации
            if (isset($data['contacts']) && is_array($data['contacts'])) {
                return $this->handleContactsForOrganization($data['contacts'], $org);
            }

            return response()->json(['error' => 'Unknown webhook format'], 400);

        } catch (\Exception $e) {
            Log::error('Organization webhook processing error:', [
                'organization' => $organization,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Валидация webhook'а
     */
    public function validate(Request $request): void
    {
        // Здесь можно добавить проверку подписи webhook'а
        // Пока что просто проверяем, что это POST запрос
        if ($request->method() !== 'POST' && $request->method() !== 'GET') {
            throw new \InvalidArgumentException('Invalid HTTP method');
        }
    }

    /**
     * Обработка сообщений
     */
    private function handleMessages(array $messages): JsonResponse
    {
        try {
            $results = $this->messageProcessor->handleMessages($messages);
            $successCount = count(array_filter($results));
            
            Log::info('Messages processed:', [
                'total' => count($messages),
                'successful' => $successCount,
                'failed' => count($messages) - $successCount
            ]);

            return response()->json([
                'status' => 'success',
                'processed' => count($messages),
                'successful' => $successCount,
                'failed' => count($messages) - $successCount
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error handling messages:', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);

            return response()->json([
                'error' => 'Failed to process messages',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обработка статусов
     */
    private function handleStatuses(array $statuses): JsonResponse
    {
        try {
            $results = $this->statusProcessor->handleStatuses($statuses);
            $successCount = count(array_filter($results));
            
            Log::info('Statuses processed:', [
                'total' => count($statuses),
                'successful' => $successCount,
                'failed' => count($statuses) - $successCount
            ]);

            return response()->json([
                'status' => 'success',
                'processed' => count($statuses),
                'successful' => $successCount,
                'failed' => count($statuses) - $successCount
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error handling statuses:', [
                'error' => $e->getMessage(),
                'statuses_count' => count($statuses)
            ]);

            return response()->json([
                'error' => 'Failed to process statuses',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обработка контактов
     */
    private function handleContacts(array $contacts): JsonResponse
    {
        try {
            $results = $this->contactProcessor->handleContacts($contacts);
            $successCount = count(array_filter($results));
            
            Log::info('Contacts processed:', [
                'total' => count($contacts),
                'successful' => $successCount,
                'failed' => count($contacts) - $successCount
            ]);

            return response()->json([
                'status' => 'success',
                'processed' => count($contacts),
                'successful' => $successCount,
                'failed' => count($contacts) - $successCount
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error handling contacts:', [
                'error' => $e->getMessage(),
                'contacts_count' => count($contacts)
            ]);

            return response()->json([
                'error' => 'Failed to process contacts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обработка старого формата webhook'а
     */
    private function handleLegacyWebhook(array $data): JsonResponse
    {
        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'incomingMessageReceived':
                return $this->handleLegacyIncomingMessage($data);
            case 'outgoingMessageReceived':
                return $this->handleLegacyOutgoingMessage($data);
            case 'outgoingAPIMessageReceived':
                return $this->handleLegacyOutgoingAPIMessage($data);
            case 'messageStatus':
                return $this->handleLegacyMessageStatus($data);
            case 'stateChange':
                return $this->handleLegacyStateChange($data);
            default:
                Log::warning('Unknown legacy webhook type:', ['type' => $type]);
                return response()->json(['error' => 'Unknown webhook type'], 400);
        }
    }

    /**
     * Обработка старого формата входящего сообщения
     */
    private function handleLegacyIncomingMessage(array $data): JsonResponse
    {
        // Конвертируем старый формат в новый
        $message = [
            'id' => $data['id'] ?? uniqid(),
            'type' => $data['messageType'] ?? 'text',
            'from' => $data['senderData']['sender'] ?? '',
            'text' => ['body' => $data['messageData']['textMessageData']['textMessage'] ?? ''],
            'contacts' => $data['senderData']['contacts'] ?? []
        ];

        return $this->handleMessages([$message]);
    }

    /**
     * Обработка старого формата исходящего сообщения
     */
    private function handleLegacyOutgoingMessage(array $data): JsonResponse
    {
        Log::info('Legacy outgoing message received:', $data);
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Обработка старого формата исходящего API сообщения
     */
    private function handleLegacyOutgoingAPIMessage(array $data): JsonResponse
    {
        Log::info('Legacy outgoing API message received:', $data);
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Обработка старого формата статуса сообщения
     */
    private function handleLegacyMessageStatus(array $data): JsonResponse
    {
        // Конвертируем старый формат в новый
        $status = [
            'id' => $data['id'] ?? uniqid(),
            'status' => $data['statusData']['status'] ?? '',
            'timestamp' => $data['timestamp'] ?? now()->toISOString()
        ];

        return $this->handleStatuses([$status]);
    }

    /**
     * Обработка старого формата изменения состояния
     */
    private function handleLegacyStateChange(array $data): JsonResponse
    {
        Log::info('Legacy state change received:', $data);
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Обработка сообщений для организации
     */
    private function handleMessagesForOrganization(array $messages, Organization $organization): JsonResponse
    {
        // Здесь можно добавить специфичную логику для организации
        return $this->handleMessages($messages);
    }

    /**
     * Обработка статусов для организации
     */
    private function handleStatusesForOrganization(array $statuses, Organization $organization): JsonResponse
    {
        // Здесь можно добавить специфичную логику для организации
        return $this->handleStatuses($statuses);
    }

    /**
     * Обработка контактов для организации
     */
    private function handleContactsForOrganization(array $contacts, Organization $organization): JsonResponse
    {
        // Здесь можно добавить специфичную логику для организации
        return $this->handleContacts($contacts);
    }
}
