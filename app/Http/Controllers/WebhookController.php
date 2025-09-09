<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\ChatParticipant;
use App\Services\Wazzup24Service;
use App\Services\MessengerService;

class WebhookController extends Controller
{
    protected $wazzupService;
    protected $messengerService;

    public function __construct(Wazzup24Service $wazzupService, MessengerService $messengerService)
    {
        $this->wazzupService = $wazzupService;
        $this->messengerService = $messengerService;
    }

    /**
     * Обработка webhook'ов от Wazzup24
     */
    public function wazzup24(Request $request): JsonResponse
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

            // Валидируем webhook (можно добавить проверку подписи)
            $this->validateWebhook($request);

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
                
                switch ($data['type']) {
                    case 'incomingMessageReceived':
                        return $this->handleIncomingMessage($data);
                    case 'outgoingMessageReceived':
                        return $this->handleOutgoingMessage($data);
                    case 'outgoingAPIMessageReceived':
                        return $this->handleOutgoingAPIMessage($data);
                    case 'outgoingMessageStatus':
                        return $this->handleMessageStatus($data);
                    case 'stateInstanceChanged':
                        return $this->handleStateChange($data);
                    default:
                        return response()->json(['error' => 'Unknown event type'], 400);
                }
            }
            
            Log::warning('Unknown webhook structure', ['data' => $data]);
            $response = response()->json(['error' => 'Unknown webhook structure'], 400);
            
            Log::info('=== WEBHOOK RESPONSE ===', [
                'status' => $response->getStatusCode(),
                'response' => $response->getContent()
            ]);
            
            return $response;

        } catch (\Exception $e) {
            Log::error('=== WEBHOOK ERROR ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if (config('wazzup24.logging.enabled')) {
                Log::channel(config('wazzup24.logging.channel'))->error('Wazzup24: Webhook error', [
                    'error' => $e->getMessage(),
                    'data' => $request->all()
                ]);
            }

            $response = response()->json(['error' => 'Internal server error'], 500);
            
            Log::info('=== WEBHOOK ERROR RESPONSE ===', [
                'status' => $response->getStatusCode(),
                'response' => $response->getContent()
            ]);
            
            return $response;
        }
    }

    /**
     * Обработка массива сообщений (новый формат)
     */
    protected function handleMessages(array $messages): JsonResponse
    {
        Log::info('=== HANDLING MESSAGES ARRAY ===', ['count' => count($messages)]);
        
        $processed = 0;
        $errors = 0;
        
        foreach ($messages as $message) {
            try {
                // Проверяем, что это входящее сообщение (не echo)
                if (isset($message['isEcho']) && $message['isEcho'] === true) {
                    Log::info('Skipping echo message', ['messageId' => $message['messageId'] ?? 'unknown']);
                    continue;
                }
                
                // Обрабатываем входящее сообщение
                $result = $this->processMessage($message);
                if ($result) {
                    $processed++;
                } else {
                    $errors++;
                }
                
            } catch (\Exception $e) {
                Log::error('Error processing message', [
                    'message' => $message,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }
        
        Log::info('Messages processing completed', [
            'processed' => $processed,
            'errors' => $errors
        ]);
        
        $response = response()->json([
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ]);
        
        Log::info('=== MESSAGES PROCESSING COMPLETED ===', [
            'processed' => $processed,
            'errors' => $errors,
            'response_status' => $response->getStatusCode(),
            'response_content' => $response->getContent()
        ]);
        
        return $response;
    }
    
    /**
     * Обработка статусов сообщений
     */
    protected function handleStatuses(array $statuses): JsonResponse
    {
        Log::info('=== HANDLING STATUSES ARRAY ===', ['count' => count($statuses)]);
        
        // Пока просто логируем статусы
        foreach ($statuses as $status) {
            Log::info('Message status update', [
                'messageId' => $status['messageId'] ?? 'unknown',
                'status' => $status['status'] ?? 'unknown'
            ]);
        }
        
        $response = response()->json(['success' => true]);
        
        Log::info('=== STATUSES PROCESSING COMPLETED ===', [
            'statuses_count' => count($statuses),
            'response_status' => $response->getStatusCode(),
            'response_content' => $response->getContent()
        ]);
        
        return $response;
    }
    
    /**
     * Обработка контактов
     */
    protected function handleContacts(array $contacts): JsonResponse
    {
        Log::info('=== HANDLING CONTACTS ARRAY ===', ['count' => count($contacts)]);
        
        $processed = 0;
        $errors = 0;
        
        foreach ($contacts as $contact) {
            try {
                Log::info('Processing contact', [
                    'contactId' => $contact['contactId'] ?? 'unknown',
                    'name' => $contact['name'] ?? 'unknown',
                    'phone' => $contact['phone'] ?? 'unknown'
                ]);
                
                // Здесь можно добавить логику обработки контактов
                // Например, создание или обновление клиента в базе данных
                
                $processed++;
                
            } catch (\Exception $e) {
                Log::error('Error processing contact', [
                    'contact' => $contact,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }
        
        Log::info('Contacts processing completed', [
            'processed' => $processed,
            'errors' => $errors
        ]);
        
        $response = response()->json([
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ]);
        
        Log::info('=== CONTACTS PROCESSING COMPLETED ===', [
            'contacts_count' => count($contacts),
            'processed' => $processed,
            'errors' => $errors,
            'response_status' => $response->getStatusCode(),
            'response_content' => $response->getContent()
        ]);
        
        return $response;
    }
    
    /**
     * Обработка отдельного сообщения
     */
    protected function processMessage(array $message): bool
    {
        Log::info('=== PROCESSING MESSAGE ===', [
            'messageId' => $message['messageId'] ?? 'unknown',
            'chatId' => $message['chatId'] ?? 'unknown',
            'type' => $message['type'] ?? 'unknown'
        ]);
        
        $phone = $message['chatId'] ?? null;
        $text = $message['text'] ?? '';
        $messageId = $message['messageId'] ?? null;
        $contactName = $message['contact']['name'] ?? null;
        $messageType = $message['type'] ?? 'text';
        
        Log::info('Message details:', [
            'phone' => $phone,
            'text' => $text,
            'message_id' => $messageId,
            'contact_name' => $contactName,
            'type' => $messageType
        ]);
        
        if (!$phone) {
            Log::error('Invalid message data - missing phone', [
                'phone' => $phone
            ]);
            return false;
        }
        
        // Обрабатываем изображения
        if ($messageType === 'imageMessage' || $messageType === 'image') {
            return $this->processImageMessage($message);
        }
        
        // Обрабатываем видео
        if ($messageType === 'videoMessage' || $messageType === 'video') {
            return $this->processVideoMessage($message);
        }
        
        // Обрабатываем стикеры
        if ($messageType === 'stickerMessage' || $messageType === 'sticker') {
            return $this->processStickerMessage($message);
        }
        
        // Обрабатываем документы
        if ($messageType === 'documentMessage' || $messageType === 'document') {
            return $this->processDocumentMessage($message);
        }
        
        // Обрабатываем аудио
        if ($messageType === 'audioMessage' || $messageType === 'audio') {
            return $this->processAudioMessage($message);
        }
        
        // Обрабатываем локацию
        if ($messageType === 'locationMessage' || $messageType === 'location') {
            return $this->processLocationMessage($message);
        }
        
        // Обрабатываем текстовые сообщения
        if ($text === null || $text === '') {
            Log::error('Invalid message data - missing text', [
                'phone' => $phone,
                'text' => $text
            ]);
            return false;
        }
        
        // Используем MessengerService для обработки
        try {
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingMessage($phone, $text, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка изображения из Wazzup24
     */
    protected function processImageMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные изображения - проверяем разные форматы
            $imageUrl = null;
            $caption = '';
            
            // Формат 1: contentUri (новый формат Wazzup24)
            if (isset($message['contentUri'])) {
                $imageUrl = $message['contentUri'];
                // В новом формате подпись может быть в поле text
                $caption = $message['text'] ?? '';
                Log::info('Found image in contentUri format', [
                    'contentUri' => $imageUrl,
                    'caption' => $caption
                ]);
            }
            // Формат 2: imageMessage (старый формат)
            elseif (isset($message['imageMessage'])) {
                $imageData = $message['imageMessage'];
                $imageUrl = $imageData['link'] ?? $imageData['url'] ?? null;
                $caption = $imageData['caption'] ?? '';
                Log::info('Found image in imageMessage format', ['imageData' => $imageData]);
            }
            // Формат 3: image (альтернативный формат)
            elseif (isset($message['image'])) {
                $imageData = $message['image'];
                $imageUrl = $imageData['link'] ?? $imageData['url'] ?? null;
                $caption = $imageData['caption'] ?? '';
                Log::info('Found image in image format', ['imageData' => $imageData]);
            }
            
            if (!$imageUrl) {
                Log::error('No image URL found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing image message', [
                'phone' => $phone,
                'image_url' => $imageUrl,
                'caption' => $caption,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки изображения
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingImage($phone, $imageUrl, $caption, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Image message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process image message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing image message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка видео из Wazzup24
     */
    protected function processVideoMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные видео - проверяем разные форматы
            $videoUrl = null;
            $caption = '';
            
            // Формат 1: contentUri (новый формат Wazzup24)
            if (isset($message['contentUri'])) {
                $videoUrl = $message['contentUri'];
                // В новом формате подпись может быть в поле text
                $caption = $message['text'] ?? '';
                Log::info('Found video in contentUri format', [
                    'contentUri' => $videoUrl,
                    'caption' => $caption
                ]);
            }
            // Формат 2: videoMessage (старый формат)
            elseif (isset($message['videoMessage'])) {
                $videoData = $message['videoMessage'];
                $videoUrl = $videoData['link'] ?? $videoData['url'] ?? null;
                $caption = $videoData['caption'] ?? '';
                Log::info('Found video in videoMessage format', ['videoData' => $videoData]);
            }
            // Формат 3: video (альтернативный формат)
            elseif (isset($message['video'])) {
                $videoData = $message['video'];
                $videoUrl = $videoData['link'] ?? $videoData['url'] ?? null;
                $caption = $videoData['caption'] ?? '';
                Log::info('Found video in video format', ['videoData' => $videoData]);
            }
            
            if (!$videoUrl) {
                Log::error('No video URL found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing video message', [
                'phone' => $phone,
                'video_url' => $videoUrl,
                'caption' => $caption,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки видео
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingVideo($phone, $videoUrl, $caption, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Video message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process video message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing video message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка стикера из Wazzup24
     */
    protected function processStickerMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные стикера
            $stickerUrl = null;
            $caption = '';
            
            // Формат 1: contentUri (новый формат Wazzup24)
            if (isset($message['contentUri'])) {
                $stickerUrl = $message['contentUri'];
                $caption = $message['text'] ?? '';
                Log::info('Found sticker in contentUri format', [
                    'contentUri' => $stickerUrl,
                    'caption' => $caption
                ]);
            }
            // Формат 2: stickerMessage (старый формат)
            elseif (isset($message['stickerMessage'])) {
                $stickerData = $message['stickerMessage'];
                $stickerUrl = $stickerData['link'] ?? $stickerData['url'] ?? null;
                $caption = $stickerData['caption'] ?? '';
                Log::info('Found sticker in stickerMessage format', ['stickerData' => $stickerData]);
            }
            
            if (!$stickerUrl) {
                Log::error('No sticker URL found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing sticker message', [
                'phone' => $phone,
                'sticker_url' => $stickerUrl,
                'caption' => $caption,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки стикера
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingSticker($phone, $stickerUrl, $caption, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Sticker message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process sticker message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing sticker message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка документа из Wazzup24
     */
    protected function processDocumentMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные документа
            $documentUrl = null;
            $documentName = '';
            $caption = '';
            
            // Формат 1: contentUri (новый формат Wazzup24)
            if (isset($message['contentUri'])) {
                $documentUrl = $message['contentUri'];
                $documentName = $message['documentName'] ?? 'Документ';
                $caption = $message['text'] ?? '';
                Log::info('Found document in contentUri format', [
                    'contentUri' => $documentUrl,
                    'documentName' => $documentName,
                    'caption' => $caption
                ]);
            }
            // Формат 2: documentMessage (старый формат)
            elseif (isset($message['documentMessage'])) {
                $documentData = $message['documentMessage'];
                $documentUrl = $documentData['link'] ?? $documentData['url'] ?? null;
                $documentName = $documentData['filename'] ?? $documentData['name'] ?? 'Документ';
                $caption = $documentData['caption'] ?? '';
                Log::info('Found document in documentMessage format', ['documentData' => $documentData]);
            }
            
            if (!$documentUrl) {
                Log::error('No document URL found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing document message', [
                'phone' => $phone,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'caption' => $caption,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки документа
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingDocument($phone, $documentUrl, $documentName, $caption, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Document message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process document message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing document message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка аудио из Wazzup24
     */
    protected function processAudioMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные аудио
            $audioUrl = null;
            $caption = '';
            
            // Формат 1: contentUri (новый формат Wazzup24)
            if (isset($message['contentUri'])) {
                $audioUrl = $message['contentUri'];
                $caption = $message['text'] ?? '';
                Log::info('Found audio in contentUri format', [
                    'contentUri' => $audioUrl,
                    'caption' => $caption
                ]);
            }
            // Формат 2: audioMessage (старый формат)
            elseif (isset($message['audioMessage'])) {
                $audioData = $message['audioMessage'];
                $audioUrl = $audioData['link'] ?? $audioData['url'] ?? null;
                $caption = $audioData['caption'] ?? '';
                Log::info('Found audio in audioMessage format', ['audioData' => $audioData]);
            }
            
            if (!$audioUrl) {
                Log::error('No audio URL found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing audio message', [
                'phone' => $phone,
                'audio_url' => $audioUrl,
                'caption' => $caption,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки аудио
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingAudio($phone, $audioUrl, $caption, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Audio message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process audio message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing audio message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Обработка локации из Wazzup24
     */
    protected function processLocationMessage(array $message): bool
    {
        try {
            $phone = $message['chatId'] ?? null;
            $messageId = $message['messageId'] ?? null;
            $contactName = $message['contact']['name'] ?? null;
            
            // Получаем данные локации
            $latitude = null;
            $longitude = null;
            $address = '';
            
            // Формат 1: locationMessage (новый формат Wazzup24)
            if (isset($message['locationMessage'])) {
                $locationData = $message['locationMessage'];
                $latitude = $locationData['latitude'] ?? null;
                $longitude = $locationData['longitude'] ?? null;
                $address = $locationData['address'] ?? $locationData['name'] ?? '';
                Log::info('Found location in locationMessage format', ['locationData' => $locationData]);
            }
            // Формат 2: location (альтернативный формат)
            elseif (isset($message['location'])) {
                $locationData = $message['location'];
                $latitude = $locationData['latitude'] ?? null;
                $longitude = $locationData['longitude'] ?? null;
                $address = $locationData['address'] ?? $locationData['name'] ?? '';
                Log::info('Found location in location format', ['locationData' => $locationData]);
            }
            
            if (!$latitude || !$longitude) {
                Log::error('No location coordinates found in message', [
                    'message' => $message,
                    'available_keys' => array_keys($message)
                ]);
                return false;
            }
            
            Log::info('Processing location message', [
                'phone' => $phone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address,
                'message_id' => $messageId
            ]);
            
            // Используем MessengerService для обработки локации
            $messengerService = app(\App\Services\MessengerService::class);
            $result = $messengerService->handleIncomingLocation($phone, $latitude, $longitude, $address, $messageId);
            
            if ($result && isset($result['success']) && $result['success']) {
                Log::info('Location message processed successfully', [
                    'chat_id' => $result['chat_id'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::error('Failed to process location message', [
                    'error' => $result['error'] ?? 'unknown error',
                    'result' => $result
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Exception processing location message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Обработка входящего сообщения (старый формат)
     */
    protected function handleIncomingMessage(array $data): JsonResponse
    {
        Log::info('=== HANDLING INCOMING MESSAGE ===');
        
        $messageData = $data['data'] ?? [];
        $phone = $messageData['senderData']['chatId'] ?? null;
        $text = $messageData['textMessageData']['textMessage'] ?? '';
        $wazzupChatId = $messageData['senderData']['chatId'] ?? null;
        $messageId = $messageData['idMessage'] ?? null;

        Log::info('Message details:', [
            'phone' => $phone,
            'text' => $text,
            'wazzup_chat_id' => $wazzupChatId,
            'message_id' => $messageId,
            'full_data' => $messageData
        ]);

        if (!$phone || $text === null || $text === '') {
            Log::error('Invalid message data - missing phone or text', [
                'phone' => $phone,
                'text' => $text,
                'data' => $messageData
            ]);
            return response()->json(['error' => 'Invalid message data'], 400);
        }

        // Проверяем, является ли это мессенджер чатом
        $messengerChat = Chat::where('messenger_phone', $phone)
                            ->where('is_messenger_chat', true)
                            ->first();

        Log::info('Messenger chat check:', [
            'phone' => $phone,
            'existing_messenger_chat' => $messengerChat ? $messengerChat->id : null
        ]);

        if ($messengerChat) {
            Log::info('Processing as MESSENGER chat');
            // Обрабатываем как мессенджер чат
            $this->messengerService->handleIncomingMessage($phone, $text, $wazzupChatId);
        } else {
            Log::info('Processing as REGULAR chat - will also trigger messenger system');
            
            // ВАЖНО: Даже если это первое сообщение, обрабатываем его как мессенджер
            // Это создаст мессенджер чат и отправит меню
            $this->messengerService->handleIncomingMessage($phone, $text, $wazzupChatId);
        }

        if (config('wazzup24.logging.enabled')) {
            Log::channel(config('wazzup24.logging.channel'))->info('Wazzup24: Incoming message processed', [
                'phone' => $phone,
                'is_messenger' => $messengerChat ? true : false
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Обработка исходящего сообщения
     */
    protected function handleOutgoingMessage(array $data): JsonResponse
    {
        // Обработка исходящих сообщений (например, подтверждение отправки)
        return response()->json(['success' => true]);
    }

    /**
     * Обработка API исходящего сообщения
     */
    protected function handleOutgoingAPIMessage(array $data): JsonResponse
    {
        // Обработка сообщений, отправленных через API
        return response()->json(['success' => true]);
    }

    /**
     * Обработка изменения статуса сообщения
     */
    protected function handleMessageStatus(array $data): JsonResponse
    {
        $statusData = $data['data'] ?? [];
        $messageId = $statusData['idMessage'] ?? null;
        $status = $statusData['status'] ?? null;

        if ($messageId && $status) {
            // Находим сообщение по Wazzup ID и обновляем статус
            $message = Message::whereJsonContains('metadata->wazzup_message_id', $messageId)->first();
            
            if ($message) {
                $metadata = $message->metadata ?? [];
                $metadata['wazzup_status'] = $status;
                $metadata['status_updated_at'] = now()->toISOString();
                
                $message->update(['metadata' => $metadata]);

                if (config('wazzup24.logging.enabled')) {
                    Log::channel(config('wazzup24.logging.channel'))->info('Wazzup24: Message status updated', [
                        'message_id' => $message->id,
                        'wazzup_message_id' => $messageId,
                        'status' => $status
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Обработка изменения состояния
     */
    protected function handleStateChange(array $data): JsonResponse
    {
        // Обработка изменений состояния (подключение/отключение)
        return response()->json(['success' => true]);
    }

    /**
     * Валидация webhook
     */
    protected function validateWebhook(Request $request): void
    {
        // Здесь можно добавить проверку подписи webhook'а
        // для дополнительной безопасности
        
        $webhookSecret = config('wazzup24.api.webhook_secret');
        
        if ($webhookSecret) {
            // Проверка подписи (если Wazzup24 поддерживает)
            // $signature = $request->header('X-Wazzup24-Signature');
            // if (!$this->verifySignature($request->getContent(), $signature, $webhookSecret)) {
            //     throw new \Exception('Invalid webhook signature');
            // }
        }
    }

    /**
     * Поиск или создание пользователя по номеру телефона
     */
    protected function findOrCreateUserByPhone(string $phone): User
    {
        // Очищаем номер телефона от лишних символов
        $cleanPhone = $this->cleanPhoneNumber($phone);
        
        $user = User::where('phone', $cleanPhone)->first();
        
        if (!$user) {
            // Создаем нового пользователя
            $user = User::create([
                'name' => 'WhatsApp User (' . $cleanPhone . ')',
                'email' => 'whatsapp_' . $cleanPhone . '@example.com',
                'phone' => $cleanPhone,
                'password' => bcrypt(str_random(16)),
            ]);

            // Привязываем к организации по умолчанию
            $user->organizations()->attach(config('wazzup24.chat.default_organization_id'), [
                'department_id' => config('wazzup24.chat.default_department_id'),
                'role_id' => config('wazzup24.chat.default_role_id'),
                'is_active' => true,
            ]);
        }

        return $user;
    }

    /**
     * Поиск или создание чата
     */
    protected function findOrCreateChat(?string $wazzupChatId, string $phone, User $user): Chat
    {
        // Ищем чат по Wazzup Chat ID
        if ($wazzupChatId) {
            $chat = Chat::whereJsonContains('metadata->wazzup_chat_id', $wazzupChatId)->first();
            if ($chat) {
                return $chat;
            }
        }

        // Ищем чат по номеру телефона
        $chat = Chat::whereJsonContains('metadata->phone', $phone)->first();
        if ($chat) {
            return $chat;
        }

        // Создаем новый чат
        $chat = Chat::create([
            'organization_id' => config('wazzup24.chat.default_organization_id'),
            'title' => 'WhatsApp: ' . $phone,
            'description' => 'WhatsApp чат с ' . $phone,
            'type' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
            'metadata' => [
                'wazzup_chat_id' => $wazzupChatId,
                'phone' => $phone,
                'source' => 'whatsapp'
            ]
        ]);

        // Добавляем пользователя как участника
        ChatParticipant::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'participant',
            'is_active' => true,
        ]);

        return $chat;
    }

    /**
     * Очистка номера телефона
     */
    protected function cleanPhoneNumber(string $phone): string
    {
        // Убираем все кроме цифр
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Убираем код страны если он есть (предполагаем, что это российский номер)
        if (strlen($clean) > 10 && substr($clean, 0, 1) === '7') {
            $clean = substr($clean, 1);
        }
        
        return $clean;
    }
}
