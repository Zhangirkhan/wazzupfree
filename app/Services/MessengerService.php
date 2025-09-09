<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Department;
use App\Models\User;
use App\Models\Client;
use App\Services\ChatHistoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MessengerService
{
    public function __construct()
    {
        // Убрали зависимость от Wazzup24Service
    }

    /**
     * Обработка входящего сообщения в мессенджере
     */
    public function handleIncomingMessage($phone, $message, $contactData = null)
    {
        try {
                    Log::info('Processing message', [
            'phone' => $phone,
            'message_length' => strlen($message)
        ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Обрабатываем сообщение в зависимости от статуса
            $this->processMessage($chat, $message, $client);
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingMessage:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Обработка входящего изображения в мессенджере
     */
    public function handleIncomingImage($phone, $imageUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing image', [
                'phone' => $phone,
                'image_url' => $imageUrl,
                'caption' => $caption
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем изображение
            $this->saveClientImage($chat, $imageUrl, $caption, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingImage:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Обработка входящего видео
     */
    public function handleIncomingVideo($phone, $videoUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing video', [
                'phone' => $phone,
                'video_url' => $videoUrl,
                'caption' => $caption
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем видео
            $this->saveClientVideo($chat, $videoUrl, $caption, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingVideo:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Обработка сообщения в зависимости от статуса чата
     */
    protected function processMessage($chat, $message, $client)
    {
        $message = trim($message);
        
        // Сохраняем каждое входящее сообщение клиента
        $this->saveClientMessage($chat, $message, $client);
        
        // Если это новый чат, отправляем меню только один раз
        if ($chat->wasRecentlyCreated) {
            $this->sendInitialMenu($chat, $client);
            return;
        }
        
        switch ($chat->messenger_status) {
            case 'menu':
                return $this->handleMenuSelection($chat, $message, $client);
            
            case 'department_selected':
                return $this->handleDepartmentSelection($chat, $message, $client);
            
            case 'active':
                return $this->handleActiveChat($chat, $message, $client);
            
            case 'completed':
                return $this->handleCompletedChat($chat, $message, $client);
            
            default:
                return $this->resetToMenu($chat, $client);
        }
    }

    /**
     * Отправка начального меню (только один раз)
     */
    protected function sendInitialMenu($chat, $client)
    {
        // Специальная обработка для тестовых номеров
        if ($this->isTestNumber($chat->messenger_phone)) {
            $menuText = $this->generateTestMenuText();
        } else {
            $departments = Department::orderBy('name')->get();
            $menuText = $this->generateMenuText($departments);
        }
        
        // Отправляем меню
        $this->sendMessage($chat, $menuText);
        
        // Обновляем статус на ожидание выбора
        $chat->update(['messenger_status' => 'menu']);
    }

    /**
     * Обработка выбора пункта меню
     */
    protected function handleMenuSelection($chat, $message, $client)
    {
        // Специальная обработка для тестовых номеров
        if ($this->isTestNumber($chat->messenger_phone)) {
            $this->handleTestNumberSelection($chat, $message, $client);
            return;
        }
        
        // Обрабатываем выбор отдела
        if (in_array($message, ['1', '2', '3', '4'])) {
            $department = Department::find($message);
            if ($department) {
                $chat->update([
                    'department_id' => $department->id,
                    'messenger_status' => 'department_selected'
                ]);
                
                // Логируем выбор отдела
                $historyService = app(ChatHistoryService::class);
                $historyService->logDepartmentSelection($chat, $department);
                
                $this->sendMessage($chat, "Вы выбрали отдел: {$department->name}\n\nТеперь напишите ваш вопрос:");
                return;
            }
        }
        
        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }
        
        // Если сообщение не распознано, отправляем подсказку
        $this->sendMessage($chat, "Пожалуйста, выберите номер отдела (1, 2, 3, 4).");
    }

    /**
     * Обработка выбора для тестового номера
     */
    protected function handleTestNumberSelection($chat, $message, $client)
    {
        // Обрабатываем выбор отдела
        if (in_array($message, ['1', '2', '3', '4'])) {
            $departments = [
                '1' => ['name' => 'Бухгалтерия', 'id' => 1],
                '2' => ['name' => 'IT отдел', 'id' => 2], 
                '3' => ['name' => 'HR отдел', 'id' => 9], // ID 9 в базе данных
                '4' => ['name' => 'Вопросы по товарам в аптеке', 'id' => 4]
            ];
            
            if (isset($departments[$message])) {
                $department = $departments[$message];
                $chat->update([
                    'messenger_status' => 'department_selected',
                    'department_id' => $department['id']
                ]);
                
                // Логируем выбор отдела для тестовых номеров
                $historyService = app(ChatHistoryService::class);
                $historyService->logDepartmentSelection($chat, Department::find($department['id']));
                
                $this->sendMessage($chat, "Подключаем с {$department['name']}. Пожалуйста, можете задать вопрос.");
                return;
            }
        }
        
        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }
        
        // Если сообщение не распознано, отправляем подсказку
        $this->sendMessage($chat, "Пожалуйста, выберите номер отдела (1, 2, 3, 4).");
    }

    /**
     * Обработка выбора отдела
     */
    protected function handleDepartmentSelection($chat, $message, $client)
    {
        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }
        
        if (empty(trim($message))) {
            $this->sendMessage($chat, "Пожалуйста, напишите ваш вопрос:");
            return;
        }
        
        // Создаем активный чат
        $chat->update([
            'messenger_status' => 'active',
            'title' => "Вопрос клиента: " . substr($message, 0, 50) . "...",
            'last_activity_at' => now()
        ]);
        
        // Уведомляем отдел
        $this->notifyDepartment($chat, $message);
        
        $this->sendMessage($chat, "Ваш вопрос отправлен в отдел {$chat->department->name}. Ожидайте ответа.");
    }

    /**
     * Обработка активного чата
     */
    protected function handleActiveChat($chat, $message, $client)
    {
        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }
        
        // Обновляем время активности
        $chat->update(['last_activity_at' => now()]);
        
        // Уведомляем назначенного сотрудника
        if ($chat->assigned_to) {
            $this->notifyAssignedUser($chat, $message);
        } else {
            // Если никто не назначен, уведомляем отдел
            $this->notifyDepartment($chat, $message);
        }
    }

    /**
     * Обработка завершенного чата
     */
    protected function handleCompletedChat($chat, $message, $client)
    {
        if ($message === '1') {
            // Продолжить чат с тем же менеджером (обход меню и отделов)
            if ($chat->assigned_to) {
                $chat->update(['messenger_status' => 'active']);
                $this->sendMessage($chat, "Чат продолжен с тем же менеджером. Можете задать новый вопрос.");
            } else {
                // Если нет назначенного менеджера, возвращаемся в меню
                $this->sendMessage($chat, "К сожалению, предыдущий менеджер недоступен. Выберите отдел заново.");
                $this->resetToMenu($chat, $client);
            }
        } elseif ($message === '0') {
            // Сбросить менеджера и отдел, показать меню заново
            $this->resetToMenu($chat, $client);
        } else {
            $this->sendMessage($chat, "1 - Продолжить чат с тем же менеджером\n0 - Вернуться в главное меню");
        }
    }



    /**
     * Сброс к главному меню
     */
    protected function resetToMenu($chat, $client)
    {
        $chat->update([
            'messenger_status' => 'menu',
            'department_id' => null,
            'assigned_to' => null
        ]);
        
        // Логируем сброс чата
        $historyService = app(ChatHistoryService::class);
        $historyService->logChatReset($chat);
        
        // Отправляем меню заново при сбросе
        $this->sendInitialMenu($chat, $client);
    }

    /**
     * Генерация текста меню
     */
    protected function generateMenuText($departments)
    {
        $text = "Добро пожаловать! С кем хотите связаться?\n\n";
        
        foreach ($departments as $department) {
            $text .= "{$department->id}. {$department->name}\n";
        }
        
        return $text;
    }

    /**
     * Проверка, является ли номер тестовым
     */
    protected function isTestNumber($phone)
    {
        $testNumbers = [
            '77476644108',  // Оригинальный тестовый номер
            '77079500929',  // +7 707 950 0929
            '77028200002',  // +7 702 820 0002
            '77777895444'   // +7 777 789 5444
        ];
        
        return in_array($phone, $testNumbers);
    }

    /**
     * Генерация текста меню для тестового номера
     */
    protected function generateTestMenuText()
    {
        return "Добрый день. Это Акжол Фарм.\n\nЧто вас интересует (пришлите номер выбранного пункта):\n\n1. Бухгалтерия\n2. IT отдел\n3. HR отдел\n4. Вопросы по товарам в аптеке";
    }

    /**
     * Сохранение сообщения клиента
     */
    protected function saveClientMessage($chat, $message, $client)
    {
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $client->id,
            'content' => $message, // Сохраняем только оригинальное сообщение
            'type' => 'text',
            'metadata' => [
                'original_message' => $message,
                'client_name' => $client->name,
                'direction' => 'incoming'
            ]
        ]);
    }

    /**
     * Сохранение изображения клиента
     */
    protected function saveClientImage($chat, $imageUrl, $caption, $client)
    {
        try {
            // Используем ImageService для сохранения изображения
            $imageService = app(\App\Services\ImageService::class);
            $imageData = $imageService->saveImageFromUrl($imageUrl, $chat->id);
            
            if (!$imageData) {
                Log::error('Failed to save image', [
                    'chat_id' => $chat->id,
                    'image_url' => $imageUrl
                ]);
                return;
            }
            
            // Создаем сообщение с изображением
            $messageContent = !empty($caption) ? $caption : 'Изображение';
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'image',
                'metadata' => [
                    'image_url' => $imageData['url'],
                    'image_path' => $imageData['path'],
                    'image_filename' => $imageData['filename'],
                    'image_size' => $imageData['size'],
                    'image_extension' => $imageData['extension'],
                    'original_url' => $imageUrl,
                    'caption' => $caption,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client image saved successfully', [
                'chat_id' => $chat->id,
                'image_url' => $imageData['url'],
                'caption' => $caption
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client image', [
                'chat_id' => $chat->id,
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Сохранение видео клиента
     */
    protected function saveClientVideo($chat, $videoUrl, $caption, $client)
    {
        try {
            // Используем VideoService для сохранения видео
            $videoService = app(\App\Services\VideoService::class);
            $videoData = $videoService->saveVideoFromUrl($videoUrl, $chat->id);
            
            if (!$videoData) {
                Log::error('Failed to save video', [
                    'chat_id' => $chat->id,
                    'video_url' => $videoUrl
                ]);
                return;
            }
            
            // Создаем сообщение с видео
            $messageContent = !empty($caption) ? $caption : 'Видео';
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'video',
                'metadata' => [
                    'video_url' => $videoData['url'],
                    'video_path' => $videoData['path'],
                    'video_filename' => $videoData['filename'],
                    'video_size' => $videoData['size'],
                    'video_extension' => $videoData['extension'],
                    'original_url' => $videoUrl,
                    'caption' => $caption,
                    'client_name' => $client->id,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client video saved successfully', [
                'chat_id' => $chat->id,
                'video_url' => $videoData['url'],
                'caption' => $caption
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client video', [
                'chat_id' => $chat->id,
                'video_url' => $videoUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Обработка входящего стикера
     */
    public function handleIncomingSticker($phone, $stickerUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing sticker', [
                'phone' => $phone,
                'sticker_url' => $stickerUrl,
                'caption' => $caption
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем стикер
            $this->saveClientSticker($chat, $stickerUrl, $caption, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingSticker:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Обработка входящего документа
     */
    public function handleIncomingDocument($phone, $documentUrl, $documentName, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing document', [
                'phone' => $phone,
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'caption' => $caption
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем документ
            $this->saveClientDocument($chat, $documentUrl, $documentName, $caption, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingDocument:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Обработка входящего аудио
     */
    public function handleIncomingAudio($phone, $audioUrl, $caption = '', $contactData = null)
    {
        try {
            Log::info('Processing audio', [
                'phone' => $phone,
                'audio_url' => $audioUrl,
                'caption' => $caption
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем аудио
            $this->saveClientAudio($chat, $audioUrl, $caption, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingAudio:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Обработка входящей локации
     */
    public function handleIncomingLocation($phone, $latitude, $longitude, $address = '', $contactData = null)
    {
        try {
            Log::info('Processing location', [
                'phone' => $phone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address
            ]);
            
            // Находим или создаем клиента с контактами
            $client = $this->findOrCreateClient($phone, $contactData);
            Log::info('Client found', ['client_id' => $client->id]);
            
            // Находим или создаем чат
            $chat = $this->findOrCreateMessengerChat($phone, $client);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id, 
                'status' => $chat->messenger_status
            ]);
            
            // Сохраняем локацию
            $this->saveClientLocation($chat, $latitude, $longitude, $address, $client);
            
            // Если это новый чат, отправляем меню только один раз
            if ($chat->wasRecentlyCreated) {
                $this->sendInitialMenu($chat, $client);
                return [
                    'success' => true,
                    'chat_id' => $chat->id,
                    'message_id' => $chat->messages()->latest()->first()->id ?? null
                ];
            }
            
            return [
                'success' => true,
                'chat_id' => $chat->id,
                'message_id' => $chat->messages()->latest()->first()->id ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in handleIncomingLocation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Сохранение стикера клиента
     */
    protected function saveClientSticker($chat, $stickerUrl, $caption, $client)
    {
        try {
            // Используем StickerService для сохранения стикера
            $stickerService = app(\App\Services\StickerService::class);
            $stickerData = $stickerService->saveStickerFromUrl($stickerUrl, $chat->id);
            
            if (!$stickerData) {
                Log::error('Failed to save sticker', [
                    'chat_id' => $chat->id,
                    'sticker_url' => $stickerUrl
                ]);
                return;
            }
            
            // Создаем сообщение со стикером
            $messageContent = !empty($caption) ? $caption : 'Стикер';
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'sticker',
                'metadata' => [
                    'sticker_url' => $stickerData['url'],
                    'sticker_path' => $stickerData['path'],
                    'sticker_filename' => $stickerData['filename'],
                    'sticker_size' => $stickerData['size'],
                    'sticker_extension' => $stickerData['extension'],
                    'original_url' => $stickerUrl,
                    'caption' => $caption,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client sticker saved successfully', [
                'chat_id' => $chat->id,
                'sticker_url' => $stickerData['url'],
                'caption' => $caption
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client sticker', [
                'chat_id' => $chat->id,
                'sticker_url' => $stickerUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Сохранение документа клиента
     */
    protected function saveClientDocument($chat, $documentUrl, $documentName, $caption, $client)
    {
        try {
            // Используем DocumentService для сохранения документа
            $documentService = app(\App\Services\DocumentService::class);
            $documentData = $documentService->saveDocumentFromUrl($documentUrl, $chat->id, $documentName);
            
            if (!$documentData) {
                Log::error('Failed to save document', [
                    'chat_id' => $chat->id,
                    'document_url' => $documentUrl
                ]);
                return;
            }
            
            // Создаем сообщение с документом
            $messageContent = !empty($caption) ? $caption : $documentName;
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'document',
                'metadata' => [
                    'document_url' => $documentData['url'],
                    'document_path' => $documentData['path'],
                    'document_filename' => $documentData['filename'],
                    'document_size' => $documentData['size'],
                    'document_extension' => $documentData['extension'],
                    'document_name' => $documentData['original_name'],
                    'original_url' => $documentUrl,
                    'caption' => $caption,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client document saved successfully', [
                'chat_id' => $chat->id,
                'document_url' => $documentData['url'],
                'document_name' => $documentData['original_name'],
                'caption' => $caption
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client document', [
                'chat_id' => $chat->id,
                'document_url' => $documentUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Сохранение аудио клиента
     */
    protected function saveClientAudio($chat, $audioUrl, $caption, $client)
    {
        try {
            // Используем AudioService для сохранения аудио
            $audioService = app(\App\Services\AudioService::class);
            $audioData = $audioService->saveAudioFromUrl($audioUrl, $chat->id);
            
            if (!$audioData) {
                Log::error('Failed to save audio', [
                    'chat_id' => $chat->id,
                    'audio_url' => $audioUrl
                ]);
                return;
            }
            
            // Создаем сообщение с аудио
            $messageContent = !empty($caption) ? $caption : 'Аудио сообщение';
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'audio',
                'metadata' => [
                    'audio_url' => $audioData['url'],
                    'audio_path' => $audioData['path'],
                    'audio_filename' => $audioData['filename'],
                    'audio_size' => $audioData['size'],
                    'audio_extension' => $audioData['extension'],
                    'original_url' => $audioUrl,
                    'caption' => $caption,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client audio saved successfully', [
                'chat_id' => $chat->id,
                'audio_url' => $audioData['url'],
                'caption' => $caption
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client audio', [
                'chat_id' => $chat->id,
                'audio_url' => $audioUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Сохранение локации клиента
     */
    protected function saveClientLocation($chat, $latitude, $longitude, $address, $client)
    {
        try {
            // Создаем сообщение с локацией
            $messageContent = !empty($address) ? $address : "Координаты: {$latitude}, {$longitude}";
            
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $client->id,
                'content' => $messageContent,
                'type' => 'location',
                'metadata' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);
            
            Log::info('Client location saved successfully', [
                'chat_id' => $chat->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving client location', [
                'chat_id' => $chat->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Отправка сообщения клиенту
     */
    protected function sendMessage($chat, $message)
    {
        try {
            // Сохраняем в базу
            $messageRecord = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Системный пользователь
                'content' => $message,
                'type' => 'system',
                'metadata' => [
                    'direction' => 'outgoing',
                    'is_bot_message' => true
                ]
            ]);
            
            Log::info("System message saved", ['chat_id' => $chat->id]);
            
            // Отправляем системное сообщение через Wazzup24
            if (class_exists('\App\Services\Wazzup24Service')) {
                $wazzupService = app('\App\Services\Wazzup24Service');
                
                // Получаем данные для отправки
                $channelId = config('services.wazzup24.channel_id');
                $chatType = 'whatsapp';
                $chatId = $chat->messenger_phone;
                
                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $message,
                    1, // Системный пользователь
                    $messageRecord->id
                );
                
                if ($result['success']) {
                    // Обновляем сообщение с ID от Wazzup24
                    $messageRecord->update([
                        'wazzup_message_id' => $result['message_id'] ?? null,
                        'metadata' => array_merge($messageRecord->metadata ?? [], [
                            'wazzup_sent' => true,
                            'wazzup_message_id' => $result['message_id'] ?? null
                        ])
                    ]);
                    
                    Log::info("System message sent via Wazzup24", [
                        'chat_id' => $chat->id,
                        'wazzup_id' => $result['message_id'] ?? null
                    ]);
                } else {
                    Log::error("Ошибка отправки системного сообщения через Wazzup24", [
                        'chat_id' => $chat->id,
                        'message_id' => $messageRecord->id,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::warning("Wazzup24Service не найден, системное сообщение сохранено только локально", [
                    'chat_id' => $chat->id,
                    'message_id' => $messageRecord->id
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Ошибка в sendMessage: " . $e->getMessage(), [
                'chat_id' => $chat->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Отправка сообщения от менеджера клиенту
     */
    public function sendManagerMessage($chat, $message, $manager)
    {
        try {
            // Автоматически назначаем чат сотруднику, если он еще не назначен
            if (!$chat->assigned_to) {
                $chat->update([
                    'assigned_to' => $manager->id,
                    'last_activity_at' => now()
                ]);
                
                // Логируем назначение менеджера
                $historyService = app(ChatHistoryService::class);
                $historyService->logManagerAssignment($chat, $manager);
                
                Log::info("Chat auto-assigned to manager", [
                    'chat_id' => $chat->id,
                    'manager_id' => $manager->id,
                    'manager_name' => $manager->name
                ]);
            } else {
                // Обновляем только время активности
                $chat->update(['last_activity_at' => now()]);
            }
            
            // Формируем сообщение с именем сотрудника жирным шрифтом
            $formattedMessage = "**{$manager->name}**\n{$message}";
            
            // Сначала сохраняем сообщение в локальную базу данных
            $messageRecord = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $manager->id,
                'content' => $formattedMessage, // Сохраняем отформатированное сообщение
                'type' => 'text',
                'direction' => 'out',
                'metadata' => [
                    'direction' => 'outgoing',
                    'is_manager_message' => true,
                    'manager_name' => $manager->name,
                    'original_message' => $message // Сохраняем оригинальное сообщение
                ]
            ]);
            
            Log::info("Manager message saved", [
                'chat_id' => $chat->id,
                'message_id' => $messageRecord->id
            ]);
            
            // Отправляем сообщение через Wazzup24
            if (class_exists('\App\Services\Wazzup24Service')) {
                $wazzupService = app('\App\Services\Wazzup24Service');
                
                // Получаем данные для отправки
                $channelId = config('services.wazzup24.channel_id');
                $chatType = 'whatsapp';
                $chatId = $chat->messenger_phone;
                
                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $formattedMessage, // Отправляем отформатированное сообщение
                    $manager->id,
                    $messageRecord->id
                );
                
                if ($result['success']) {
                    // Обновляем сообщение с ID от Wazzup24
                    $messageRecord->update([
                        'wazzup_message_id' => $result['message_id'] ?? null,
                        'metadata' => array_merge($messageRecord->metadata ?? [], [
                            'wazzup_sent' => true,
                            'wazzup_message_id' => $result['message_id'] ?? null
                        ])
                    ]);
                    
                    Log::info("Message sent via Wazzup24", [
                        'chat_id' => $chat->id,
                        'wazzup_id' => $result['message_id'] ?? null
                    ]);
                } else {
                    Log::error("Wazzup24 send error", [
                        'chat_id' => $chat->id,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::warning("Wazzup24Service не найден, сообщение сохранено только локально", [
                    'chat_id' => $chat->id,
                    'message_id' => $messageRecord->id
                ]);
            }
            
            return $messageRecord;
            
        } catch (\Exception $e) {
            Log::error("Ошибка в sendManagerMessage: " . $e->getMessage(), [
                'chat_id' => $chat->id,
                'manager' => $manager->name,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Уведомление отдела
     */
    protected function notifyDepartment($chat, $message)
    {
        $department = $chat->department;
        
        if (!$department) {
            Log::warning("Отдел не найден для чата {$chat->id}");
            return;
        }
        
        $users = $department->users;
        
        if ($users->isEmpty()) {
            Log::warning("В отделе {$department->name} нет пользователей для уведомления");
            return;
        }
        
        foreach ($users as $user) {
            // Здесь можно добавить уведомления (email, push, etc.)
            Log::info("Уведомление пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Уведомление назначенного пользователя
     */
    protected function notifyAssignedUser($chat, $message)
    {
        $user = $chat->assignedTo;
        if ($user) {
            Log::info("Уведомление назначенному пользователю {$user->name} о новом сообщении в чате {$chat->id}");
        }
    }

    /**
     * Поиск или создание клиента
     */
    protected function findOrCreateClient($phone, $contactData = null)
    {
        $client = Client::where('phone', $phone)->first();
        
        if (!$client) {
            $client = Client::create([
                'name' => $contactData['name'] ?? 'Клиент ' . $phone,
                'phone' => $phone,
                'is_active' => true,
                'avatar' => $contactData['avatarUri'] ?? $contactData['avatar'] ?? null
            ]);
            
            Log::info('Client created', [
                'client_id' => $client->id,
                'name' => $client->name
            ]);
        } else {
            // Обновляем данные клиента если они изменились
            $updated = false;
            $updates = [];
            
            if ($contactData && isset($contactData['name']) && $client->name !== $contactData['name']) {
                $updates['name'] = $contactData['name'];
                $updated = true;
            }
            
            if ($contactData && isset($contactData['avatarUri']) && $client->avatar !== $contactData['avatarUri']) {
                $updates['avatar'] = $contactData['avatarUri'];
                $updated = true;
            }
            
            if ($updated) {
                $client->update($updates);
                Log::info('Обновлены данные клиента', [
                    'client_id' => $client->id,
                    'updates' => $updates
                ]);
            }
        }
        
        return $client;
    }

    /**
     * Поиск или создание мессенджер чата
     */
    protected function findOrCreateMessengerChat($phone, $client)
    {
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();
        
        $isNewChat = false;
        
        if (!$chat) {
            $chat = Chat::create([
                'organization_id' => 1, // Используем ID 1 по умолчанию
                'title' => 'Мессенджер чат: ' . $phone,
                'type' => 'private', // Используем разрешенный тип
                'status' => 'active',
                'created_by' => 1, // Системный пользователь
                'is_messenger_chat' => true,
                'messenger_phone' => $phone,
                'messenger_status' => 'menu',
                'last_activity_at' => now()
            ]);
            $isNewChat = true;
        }
        
        // Примечание: меню будет отправлено в handleMenuMessage после обработки входящего сообщения
        
        return $chat;
    }

    /**
     * Передача чата другому отделу
     */
    public function transferToDepartment($chat, $newDepartmentId, $reason = null)
    {
        $newDepartment = Department::find($newDepartmentId);
        if (!$newDepartment) {
            return false;
        }
        
        $oldDepartment = $chat->department;
        
        $chat->update([
            'department_id' => $newDepartmentId,
            'assigned_to' => null, // Сбрасываем назначение
            'last_activity_at' => now()
        ]);
        
        // Отправляем уведомление клиенту
        $message = "Ваш диалог был перемещен в отдел {$newDepartment->name}";
        if ($reason) {
            $message .= ". Причина: {$reason}";
        }
        
        $this->sendMessage($chat, $message);
        
        // Сохраняем системное сообщение о передаче
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => null,
            'content' => "Чат передан из отдела '{$oldDepartment->name}' в отдел '{$newDepartment->name}'",
            'type' => 'system',
            'metadata' => [
                'transfer_reason' => $reason,
                'old_department' => $oldDepartment->name,
                'new_department' => $newDepartment->name
            ]
        ]);
        
        return true;
    }

    /**
     * Передача чата другому сотруднику
     */
    public function transferToUser($chat, $newUserId, $reason = null)
    {
        $newUser = User::find($newUserId);
        if (!$newUser) {
            return false;
        }
        
        $oldUser = $chat->assignedTo;
        
        $chat->update([
            'assigned_to' => $newUserId,
            'last_activity_at' => now()
        ]);
        
        // Сохраняем системное сообщение о передаче
        $message = "Чат передан от '{$oldUser->name}' к '{$newUser->name}'";
        if ($reason) {
            $message .= ". Причина: {$reason}";
        }
        
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => null,
            'content' => $message,
            'type' => 'system',
            'metadata' => [
                'transfer_reason' => $reason,
                'old_user' => $oldUser ? $oldUser->name : 'Не назначен',
                'new_user' => $newUser->name
            ]
        ]);
        
        return true;
    }

    /**
     * Завершение чата
     */
    public function completeChat($chat, $reason = null)
    {
        $chat->update([
            'messenger_status' => 'completed',
            'last_activity_at' => now()
        ]);
        
        $message = "Разговор завершен.";
        if ($reason) {
            $message .= " Причина: {$reason}";
        }
        $message .= "\n\n1 - Продолжить чат\n0 - Вернуться в главное меню";
        
        $this->sendMessage($chat, $message);
        
        // Сохраняем системное сообщение о завершении
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Системный пользователь
            'content' => "Чат завершен. Причина: " . ($reason ?: 'Не указана'),
            'type' => 'system',
            'metadata' => [
                'completion_reason' => $reason
            ]
        ]);
    }

    /**
     * Назначение менеджера на чат
     */
    public function assignManager($chat, $manager)
    {
        $chat->update([
            'assigned_to' => $manager->id,
            'last_activity_at' => now()
        ]);
        
        // Сохраняем системное сообщение о назначении
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Системный пользователь
            'content' => "Чат назначен менеджеру: {$manager->name}",
            'type' => 'system',
            'metadata' => [
                'assigned_manager' => $manager->name,
                'assigned_manager_id' => $manager->id
            ]
        ]);
        
        return true;
    }

    /**
     * Получение доступных отделов для передачи
     */
    public function getAvailableDepartments($currentDepartmentId = null)
    {
        $departments = Department::orderBy('name')->get();
        
        if ($currentDepartmentId) {
            $departments = $departments->filter(function($dept) use ($currentDepartmentId) {
                return $dept->id != $currentDepartmentId;
            });
        }
        
        return $departments;
    }

    /**
     * Получение доступных менеджеров для передачи
     */
    public function getAvailableManagers($currentUserId = null, $departmentId = null)
    {
        $query = User::where('role', 'manager')->orWhere('role', 'admin');
        
        if ($currentUserId) {
            $query->where('id', '!=', $currentUserId);
        }
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Передача чата в другой отдел с уведомлением клиента
     */
    public function transferToDepartmentWithNotification($chat, $newDepartmentId, $reason = null, $notifyClient = true)
    {
        $result = $this->transferToDepartment($chat, $newDepartmentId, $reason);
        
        if ($result && $notifyClient) {
            $newDepartment = Department::find($newDepartmentId);
            $message = "Ваш диалог был перемещен в отдел '{$newDepartment->name}'";
            if ($reason) {
                $message .= ". Причина: {$reason}";
            }
            $message .= "\n\nОжидайте ответа от специалистов отдела.";
            
            $this->sendMessage($chat, $message);
        }
        
        return $result;
    }

    /**
     * Передача чата другому менеджеру с уведомлением клиента
     */
    public function transferToUserWithNotification($chat, $newUserId, $reason = null, $notifyClient = true)
    {
        $result = $this->transferToUser($chat, $newUserId, $reason);
        
        if ($result && $notifyClient) {
            $newUser = User::find($newUserId);
            $message = "Ваш диалог был передан менеджеру '{$newUser->name}'";
            if ($reason) {
                $message .= ". Причина: {$reason}";
            }
            $message .= "\n\nОжидайте ответа.";
            
            $this->sendMessage($chat, $message);
        }
        
        return $result;
    }

    /**
     * Массовая передача чатов в отдел
     */
    public function bulkTransferToDepartment($chatIds, $newDepartmentId, $reason = null)
    {
        $results = [];
        $newDepartment = Department::find($newDepartmentId);
        
        foreach ($chatIds as $chatId) {
            $chat = Chat::find($chatId);
            if ($chat && $chat->is_messenger_chat) {
                $results[$chatId] = $this->transferToDepartmentWithNotification($chat, $newDepartmentId, $reason);
            }
        }
        
        return $results;
    }

    /**
     * Получение истории передач чата
     */
    public function getChatTransferHistory($chat)
    {
        return Message::where('chat_id', $chat->id)
            ->where('type', 'system')
            ->where('content', 'like', '%передан%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Автоматическое закрытие неактивных чатов
     */
    public function closeInactiveChats()
    {
        $inactiveDate = Carbon::now()->subDays(7);
        
        $inactiveChats = Chat::where('is_messenger_chat', true)
                            ->where('messenger_status', 'active')
                            ->where('last_activity_at', '<', $inactiveDate)
                            ->get();
        
        foreach ($inactiveChats as $chat) {
            $this->completeChat($chat, 'Автоматическое закрытие из-за неактивности');
        }
        
        return $inactiveChats->count();
    }
}
