<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Wazzup24Service
{
    private $apiKey;
    private $channelId;
    private $baseUrl = 'https://api.wazzup24.com';

    public function __construct($apiKey = null, $channelId = null)
    {
        $this->apiKey = $apiKey ?: config('services.wazzup24.api_key');
        $this->channelId = $channelId ?: config('services.wazzup24.channel_id');
    }

    /**
     * Создание сервиса для организации
     */
    public static function forOrganization($organization)
    {
        if (!$organization->isWazzup24Configured()) {
            throw new \Exception("Wazzup24 не настроен для организации {$organization->name}");
        }

        return new self($organization->wazzup24_api_key, $organization->wazzup24_channel_id);
    }

    /**
     * Получить Channel ID
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Проверка подключения к API
     */
    public function testConnection()
    {
        try {
            // Согласно документации, используем /v3/channels для проверки подключения
            $response = $this->makeRequest('GET', '/v3/channels');

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API connection test failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение списка каналов
     */
    public function getChannels()
    {
        try {
            $response = $this->makeRequest('GET', '/v3/channels');

            return [
                'success' => true,
                'channels' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get channels failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }



    /**
     * Отправка сообщения
     */
    public function sendMessage($channelId, $chatType, $chatId, $text, $crmUserId = null, $crmMessageId = null)
    {
        try {
            // Очищаем текст от некорректных UTF-8 символов
            $cleanText = $this->cleanText($text);

            $data = [
                'channelId' => $channelId,
                'chatType' => $chatType,
                'chatId' => $chatId,
                'text' => $cleanText
            ];

            // Убираем crmUserId и crmMessageId, так как они вызывают ошибку INVALID_MESSAGE_DATA
            // if ($crmUserId) {
            //     $data['crmUserId'] = $crmUserId;
            // }
            //
            // if ($crmMessageId) {
            //     $data['crmMessageId'] = $crmMessageId;
            // }

            $response = $this->makeRequest('POST', '/v3/message', $data);

            return [
                'success' => true,
                'data' => $response,
                'message_id' => $response['messageId'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API send message failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение истории сообщений
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
                'messages' => $response['messages'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get messages failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Выполнение HTTP запроса к API
     */
    private function makeRequest($method, $endpoint, $data = null, $params = [])
    {
        if (!$this->apiKey) {
            throw new \Exception('API ключ не настроен');
        }

        $url = $this->baseUrl . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        // Попробуем разные форматы авторизации
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // Добавляем авторизацию
        if (strpos($this->apiKey, 'Bearer ') === 0) {
            $headers['Authorization'] = $this->apiKey;
        } else {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        Log::info('Wazzup24 API request', [
            'method' => $method,
            'endpoint' => $endpoint
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
                    Log::info('Wazzup24 API response', [
            'status' => $response->status()
        ]);
            return $responseData;
        } else {
            $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
            Log::error('Wazzup24 API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
            throw new \Exception($errorMessage);
        }
    }

    /**
     * Очистка текста от некорректных UTF-8 символов
     */
    private function cleanText($text)
    {
        // Удаляем управляющие символы, но сохраняем переносы строк (\n, \r)
        $text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Удаляем символы замены UTF-8
        $text = str_replace("\u{FFFD}", '', $text);

        // Конвертируем в UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Проверяем корректность
        if (!mb_check_encoding($text, 'UTF-8')) {
            // Если все еще некорректно, используем fallback
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }

        return $text;
    }

    /**
     * Проверка подключения (алиас для testConnection)
     */
    public function checkConnection()
    {
        return $this->testConnection();
    }

    /**
     * Настройка webhooks
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
                'data' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API setup webhooks failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение настроек webhooks
     */
    public function getWebhooks()
    {
        try {
            $response = $this->makeRequest('GET', '/v3/webhooks');

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get webhooks failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение списка пользователей
     */
    public function getUsers()
    {
        try {
            $response = $this->makeRequest('GET', '/users');

            return [
                'success' => true,
                'users' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get users failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение списка клиентов из Wazzup24
     */
    public function getClients($limit = 100)
    {
        try {
            // Получаем историю сообщений для извлечения клиентов
            $params = [
                'limit' => $limit
            ];

            $response = $this->makeRequest('GET', '/messages', null, $params);

            // Извлекаем уникальных клиентов из сообщений
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
                        ];
                    }
                }
            }

            return [
                'success' => true,
                'clients' => $clients,
                'total' => count($clients)
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get clients failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение контактов из Wazzup24
     */
    public function getContacts()
    {
        try {
            $response = $this->makeRequest('GET', '/contacts');

            return [
                'success' => true,
                'contacts' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get contacts failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Добавление/обновление пользователей
     */
    public function updateUsers($users)
    {
        try {
            $response = $this->makeRequest('POST', '/users', $users);

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API update users failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Проверка статуса канала
     */
    public function getChannelStatus($channelId = null)
    {
        try {
            // Если channelId не указан, получаем первый доступный канал
            if (!$channelId) {
                $channels = $this->getChannels();
                if ($channels['success'] && !empty($channels['channels'])) {
                    $channelId = $channels['channels'][0]['channelId'] ?? null;
                }
            }

            if (!$channelId) {
                return [
                    'success' => false,
                    'error' => 'Канал не найден'
                ];
            }

            // В API v3 статус канала возвращается в списке каналов
            $channels = $this->getChannels();
            if ($channels['success']) {
                foreach ($channels['channels'] as $channel) {
                    if ($channel['channelId'] === $channelId) {
                        return [
                            'success' => true,
                            'status' => $channel['state'] ?? 'unknown',
                            'channel' => $channel
                        ];
                    }
                }
            }

            return [
                'success' => false,
                'error' => 'Канал не найден'
            ];
        } catch (\Exception $e) {
            Log::error('Wazzup24 API get channel status failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
