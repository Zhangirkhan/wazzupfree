<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrganizationWazzupService
{
    private $organization;
    private $apiKey;
    private $baseUrl = 'https://api.wazzup24.com';

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        $this->apiKey = $organization->wazzup24_api_key;
    }

    /**
     * Проверка подключения к API для организации
     */
    public function testConnection()
    {
        try {
            $response = $this->makeRequest('GET', '/v3/channels');

            return [
                'success' => true,
                'data' => $response,
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API connection test failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Получение списка каналов для организации
     */
    public function getChannels()
    {
        try {
            $response = $this->makeRequest('GET', '/v3/channels');

            return [
                'success' => true,
                'channels' => $response,
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get channels failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Отправка сообщения от имени организации
     */
    public function sendMessage($channelId, $chatType, $chatId, $text, $crmUserId = null, $crmMessageId = null)
    {
        try {
            $cleanText = $this->cleanText($text);

            $data = [
                'channelId' => $channelId,
                'chatType' => $chatType,
                'chatId' => $chatId,
                'text' => $cleanText
            ];

            $response = $this->makeRequest('POST', '/v3/message', $data);

            return [
                'success' => true,
                'data' => $response,
                'message_id' => $response['messageId'] ?? null,
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API send message failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Получение истории сообщений для организации
     */
    public function getMessages($channelId, $chatId = null, $limit = 50)
    {
        try {
            $params = [
                'channelId' => $channelId,
                'limit' => $limit
            ];

            if ($chatId) {
                $params['chatId'] = $chatId;
            }

            $response = $this->makeRequest('GET', '/v3/messages', null, $params);

            return [
                'success' => true,
                'messages' => $response['messages'] ?? [],
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get messages failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Настройка webhooks для организации
     */
    public function setupWebhooks($webhooksUri, $subscriptions = [])
    {
        try {
            $data = [
                'webhooksUri' => $webhooksUri,
                'subscriptions' => array_merge([
                    'messagesAndStatuses' => true,
                    'contactsAndDealsCreation' => false,
                    'channelsUpdates' => false,
                    'templateStatus' => false
                ], $subscriptions)
            ];

            $response = $this->makeRequest('PATCH', '/v3/webhooks', $data);

            return [
                'success' => true,
                'data' => $response,
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API setup webhooks failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Получение настроек webhooks для организации
     */
    public function getWebhooks()
    {
        try {
            $response = $this->makeRequest('GET', '/v3/webhooks');

            return [
                'success' => true,
                'data' => $response,
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get webhooks failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Получение клиентов для организации
     */
    public function getClients($limit = 100)
    {
        try {
            $params = ['limit' => $limit];
            $response = $this->makeRequest('GET', '/messages', null, $params);

            $clients = [];
            $seenPhones = [];

            if (isset($response['messages']) && is_array($response['messages'])) {
                foreach ($response['messages'] as $message) {
                    $phone = $message['senderData']['chatId'] ?? null;

                    if ($phone && !in_array($phone, $seenPhones)) {
                        $seenPhones[] = $phone;

                        $clients[] = [
                            'phone' => $phone,
                            'name' => $message['senderData']['name'] ?? 'Клиент ' . $phone,
                            'uuid_wazzup' => $message['senderData']['chatId'] ?? null,
                            'last_message' => $message['textMessageData']['textMessage'] ?? '',
                            'last_message_date' => $message['timestamp'] ?? null,
                            'organization_id' => $this->organization->id,
                        ];
                    }
                }
            }

            return [
                'success' => true,
                'clients' => $clients,
                'total' => count($clients),
                'organization_id' => $this->organization->id
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get clients failed for organization ' . $this->organization->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id
            ];
        }
    }

    /**
     * Выполнение HTTP запроса к API
     */
    private function makeRequest($method, $endpoint, $data = null, $params = [])
    {
        if (!$this->apiKey) {
            throw new \Exception('API ключ не настроен для организации ' . $this->organization->name);
        }

        $url = $this->baseUrl . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        if (strpos($this->apiKey, 'Bearer ') === 0) {
            $headers['Authorization'] = $this->apiKey;
        } else {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        Log::info('Wazzup24 API request for organization ' . $this->organization->id, [
            'method' => $method,
            'endpoint' => $endpoint,
            'organization' => $this->organization->name
        ]);

        $request = Http::withHeaders($headers);

        switch ($method) {
            case 'GET':
                $response = $request->get($url);
                break;
            case 'POST':
                $response = $request->post($url, $data);
                break;
            case 'PUT':
                $response = $request->put($url, $data);
                break;
            case 'PATCH':
                $response = $request->patch($url, $data);
                break;
            case 'DELETE':
                $response = $request->delete($url);
                break;
            default:
                throw new \Exception('Неподдерживаемый HTTP метод: ' . $method);
        }

        if ($response->successful()) {
            $responseData = $response->json();
            Log::info('Wazzup24 API response for organization ' . $this->organization->id, [
                'status' => $response->status(),
                'organization' => $this->organization->name
            ]);
            return $responseData;
        } else {
            $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
            Log::error('Wazzup24 API error for organization ' . $this->organization->id, [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'organization' => $this->organization->name
            ]);
            throw new \Exception($errorMessage);
        }
    }

    /**
     * Очистка текста от некорректных UTF-8 символов
     */
    private function cleanText($text)
    {
        $text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = str_replace("\u{FFFD}", '', $text);
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }

        return $text;
    }
}
