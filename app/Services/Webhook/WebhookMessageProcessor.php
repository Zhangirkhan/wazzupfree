<?php

namespace App\Services\Webhook;

use App\Contracts\WebhookMessageProcessorInterface;
use App\Services\MessengerService;
use Illuminate\Support\Facades\Log;

class WebhookMessageProcessor implements WebhookMessageProcessorInterface
{
    public function __construct(
        private MessengerService $messengerService
    ) {}

    /**
     * Обработка массива сообщений
     */
    public function handleMessages(array $messages): array
    {
        $results = [];
        
        foreach ($messages as $message) {
            $results[] = $this->processMessage($message);
        }
        
        return $results;
    }

    /**
     * Обработка одного сообщения
     */
    public function processMessage(array $message): bool
    {
        try {
            Log::info('Processing webhook message:', [
                'message_id' => $message['id'] ?? 'unknown',
                'type' => $message['type'] ?? 'unknown',
                'from' => $message['from'] ?? 'unknown'
            ]);

            // Фильтруем эхо и не-входящие сообщения от Wazzup24, чтобы не зациклиться
            // isEcho=true означает, что это наше же исходящее сообщение, отданное обратно вебхуком
            if (isset($message['isEcho']) && $message['isEcho'] === true) {
                Log::info('Skipping echo message from Wazzup24', [
                    'message_id' => $message['id'] ?? null
                ]);
                return true;
            }

            // Вебхук может присылать разные статусы; обрабатываем только входящие сообщения клиента
            if (isset($message['status']) && $message['status'] !== 'inbound') {
                Log::info('Skipping non-inbound message', [
                    'message_id' => $message['id'] ?? null,
                    'status' => $message['status']
                ]);
                return true;
            }

            $messageType = $message['type'] ?? 'text';
            
            return match ($messageType) {
                'text' => $this->processTextMessage($message),
                'image' => $this->processImageMessage($message),
                'video' => $this->processVideoMessage($message),
                'sticker' => $this->processStickerMessage($message),
                'document' => $this->processDocumentMessage($message),
                'audio' => $this->processAudioMessage($message),
                'location' => $this->processLocationMessage($message),
                default => $this->processTextMessage($message)
            };

        } catch (\Exception $e) {
            Log::error('Error processing webhook message:', [
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка текстового сообщения
     */
    public function processTextMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            $text = $message['text']['body'] ?? '';
            $contactData = $this->extractContactData($message);
            $wazzupMessageId = $message['id'] ?? null;
            $organization = null;
            if (isset($message['organization_id'])) {
                // Пробрасываем организацию далее, если она передана из вебхука org
                $organization = \App\Models\Organization::find($message['organization_id']);
            }

            if (empty($phone) || empty($text)) {
                Log::warning('Invalid text message data:', $message);
                return false;
            }

            $result = $this->messengerService->handleIncomingMessage(
                $phone,
                $text,
                $contactData,
                $organization,
                $wazzupMessageId
            );

            Log::info('Text message processed:', [
                'phone' => $phone,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing text message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка изображения
     */
    public function processImageMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            
            // Wazzup24 присылает URL изображения в contentUri, а не в image.link
            $imageUrl = $message['contentUri'] ?? '';
            
            // Fallback для старого формата, если есть
            if (empty($imageUrl) && isset($message['image'])) {
                $imageData = $message['image'];
                $imageUrl = $imageData['link'] ?? '';
                $caption = $imageData['caption'] ?? '';
            } else {
                $caption = $message['text'] ?? '';
            }
            
            $contactData = $this->extractContactData($message);
            $wazzupMessageId = $message['id'] ?? null;
            
            // Извлекаем организацию, если передана
            $organization = null;
            if (isset($message['organization_id'])) {
                $organization = \App\Models\Organization::find($message['organization_id']);
            }

            if (empty($phone) || empty($imageUrl)) {
                Log::warning('Invalid image message data:', [
                    'phone' => $phone,
                    'imageUrl' => $imageUrl,
                    'message_keys' => array_keys($message)
                ]);
                return false;
            }

            $result = $this->messengerService->handleIncomingImage(
                $phone,
                $imageUrl,
                $caption,
                $contactData,
                $organization,
                $wazzupMessageId
            );

            Log::info('Image message processed:', [
                'phone' => $phone,
                'image_url' => $imageUrl,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing image message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка видео
     */
    public function processVideoMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            
            // Wazzup24 присылает URL видео в contentUri, а не в video.link
            $videoUrl = $message['contentUri'] ?? '';
            
            // Fallback для старого формата, если есть
            if (empty($videoUrl) && isset($message['video'])) {
                $videoData = $message['video'];
                $videoUrl = $videoData['link'] ?? '';
                $caption = $videoData['caption'] ?? '';
            } else {
                $caption = $message['text'] ?? '';
            }
            
            $contactData = $this->extractContactData($message);

            if (empty($phone) || empty($videoUrl)) {
                Log::warning('Invalid video message data:', [
                    'phone' => $phone,
                    'videoUrl' => $videoUrl,
                    'message_keys' => array_keys($message)
                ]);
                return false;
            }

            $result = $this->messengerService->handleIncomingVideo(
                $phone,
                $videoUrl,
                $caption,
                $contactData
            );

            Log::info('Video message processed:', [
                'phone' => $phone,
                'video_url' => $videoUrl,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing video message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка стикера
     */
    public function processStickerMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            
            // Wazzup24 присылает URL стикера в contentUri
            $stickerUrl = $message['contentUri'] ?? '';
            
            // Fallback для старого формата
            if (empty($stickerUrl) && isset($message['sticker'])) {
                $stickerData = $message['sticker'];
                $stickerUrl = $stickerData['link'] ?? '';
            }
            
            $contactData = $this->extractContactData($message);

            if (empty($phone) || empty($stickerUrl)) {
                Log::warning('Invalid sticker message data:', [
                    'phone' => $phone,
                    'stickerUrl' => $stickerUrl,
                    'message_keys' => array_keys($message)
                ]);
                return false;
            }

            $result = $this->messengerService->handleIncomingSticker(
                $phone,
                $stickerUrl,
                '',
                $contactData
            );

            Log::info('Sticker message processed:', [
                'phone' => $phone,
                'sticker_url' => $stickerUrl,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing sticker message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка документа
     */
    public function processDocumentMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            
            // Wazzup присылает данные напрямую в message, а не в document
            $documentUrl = $message['contentUri'] ?? '';
            $documentName = $message['text'] ?? 'Документ';
            $caption = $message['text'] ?? '';
            $contactData = $this->extractContactData($message);

            // Извлекаем filename из URL если есть
            if (strpos($documentUrl, 'filename=') !== false) {
                parse_str(parse_url($documentUrl, PHP_URL_QUERY), $params);
                if (!empty($params['filename'])) {
                    $documentName = $params['filename'];
                }
            }

            if (empty($phone) || empty($documentUrl)) {
                Log::warning('Invalid document message data:', $message);
                return false;
            }

            $result = $this->messengerService->handleIncomingDocument(
                $phone,
                $documentUrl,
                $documentName,
                $caption,
                $contactData
            );

            Log::info('Document message processed:', [
                'phone' => $phone,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing document message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка аудио
     */
    public function processAudioMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            
            // Wazzup присылает данные напрямую в message, а не в audio
            $audioUrl = $message['contentUri'] ?? '';
            $contactData = $this->extractContactData($message);

            if (empty($phone) || empty($audioUrl)) {
                Log::warning('Invalid audio message data:', $message);
                return false;
            }

            $result = $this->messengerService->handleIncomingAudio(
                $phone,
                $audioUrl,
                '',
                $contactData
            );

            Log::info('Audio message processed:', [
                'phone' => $phone,
                'audio_url' => $audioUrl,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing audio message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Обработка геолокации
     */
    public function processLocationMessage(array $message): bool
    {
        try {
            $phone = $this->cleanPhoneNumber($message['from'] ?? '');
            $locationData = $message['location'] ?? [];
            $latitude = $locationData['latitude'] ?? 0;
            $longitude = $locationData['longitude'] ?? 0;
            $address = $locationData['address'] ?? '';
            $contactData = $this->extractContactData($message);

            if (empty($phone) || ($latitude === 0 && $longitude === 0)) {
                Log::warning('Invalid location message data:', $message);
                return false;
            }

            $result = $this->messengerService->handleIncomingLocation(
                $phone,
                $latitude,
                $longitude,
                $address,
                $contactData
            );

            Log::info('Location message processed:', [
                'phone' => $phone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'success' => $result['success'] ?? false
            ]);

            return $result['success'] ?? false;

        } catch (\Exception $e) {
            Log::error('Error processing location message:', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Очистка номера телефона
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Убираем все символы кроме цифр
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Если номер начинается с 8, заменяем на 7
        if (str_starts_with($phone, '8')) {
            $phone = '7' . substr($phone, 1);
        }
        
        // Если номер начинается с +7, убираем +
        if (str_starts_with($phone, '+7')) {
            $phone = '7' . substr($phone, 2);
        }
        
        return $phone;
    }

    /**
     * Извлечение данных контакта из сообщения
     */
    private function extractContactData(array $message): ?array
    {
        $contactData = null;
        
        if (isset($message['contacts']) && is_array($message['contacts'])) {
            $contact = $message['contacts'][0] ?? null;
            if ($contact) {
                $contactData = [
                    'name' => $contact['name']['formatted_name'] ?? null,
                    'avatar' => $contact['avatar'] ?? null,
                    'avatarUri' => $contact['avatar'] ?? null
                ];
            }
        }
        // Fallback для формата Wazzup v3: поле contact { name, avatarUri, phone }
        if (!$contactData && isset($message['contact']) && is_array($message['contact'])) {
            $c = $message['contact'];
            $contactData = [
                'name' => $c['name'] ?? null,
                'avatar' => $c['avatarUri'] ?? null,
                'avatarUri' => $c['avatarUri'] ?? null,
                'phone' => $c['phone'] ?? null
            ];
        }
        
        return $contactData;
    }
}
