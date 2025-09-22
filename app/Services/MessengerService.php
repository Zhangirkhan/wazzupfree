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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MessengerService
{
    public function __construct()
    {
        // Убрали зависимость от Wazzup24Service
    }

    /**
     * Обработка входящего сообщения в мессенджере
     */
    public function handleIncomingMessage($phone, $message, $contactData = null, $organization = null)
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
            $chat = $this->findOrCreateMessengerChat($phone, $client, $organization);
            $isNewChat = $chat->wasRecentlyCreated;
            Log::info('Chat found', [
                'chat_id' => $chat->id,
                'status' => $chat->messenger_status
            ]);

            // Обрабатываем сообщение в зависимости от статуса
            // (сохранение сообщения происходит внутри processMessage)
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
    public function handleIncomingImage($phone, $imageUrl, $caption = '', $contactData = null, $organization = null)
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
            $chat = $this->findOrCreateMessengerChat($phone, $client, $organization);
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

            case 'closed':
                return $this->handleClosedChat($chat, $message, $client);

            default:
                return $this->resetToMenu($chat, $client);
        }
    }

    /**
     * Отправка начального меню (только один раз)
     */
    protected function sendInitialMenu($chat, $client)
    {
        // Показываем только отделы текущей организации с включенным показом в чат-боте
        $departments = Department::forChatbot()
            ->where('organization_id', $chat->organization_id)
            ->get();
        $menuText = $this->generateMenuText($departments);

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

        // Получаем список отделов для чат-бота ТЕКУЩЕЙ ОРГАНИЗАЦИИ
        $chatbotDepartments = Department::forChatbot()
            ->where('organization_id', $chat->organization_id)
            ->get();

        // Создаем массив соответствия номера выбора к ID отдела
        $departmentMapping = [];
        $validChoices = [];
        foreach ($chatbotDepartments as $index => $dept) {
            $choiceNumber = $index + 1; // Нумерация с 1
            $departmentMapping[$choiceNumber] = $dept->id;
            $validChoices[] = (string)$choiceNumber;
        }

        // Обрабатываем выбор отдела
        if (in_array($message, $validChoices)) {
            $departmentId = $departmentMapping[intval($message)];
            $department = Department::find($departmentId);

            if ($department) {
                // Переводим чат сразу в активный и уведомляем отдел, используя последнее клиентское сообщение
                $alreadyNotified = $chat->messenger_data['department_notified'] ?? false;

                $chat->update([
                    'department_id' => $department->id,
                    'messenger_status' => 'active',
                    'last_activity_at' => now(),
                    'messenger_data' => array_merge($chat->messenger_data ?? [], [
                        'wrong_answers' => 0,
                        'department_notified' => true
                    ])
                ]);

                // Логируем выбор отдела
                $historyService = app(ChatHistoryService::class);
                $historyService->logDepartmentSelection($chat, $department);

                // Отправляем уведомление отделу с последним текстом клиента
                $lastClientText = $this->getLastClientTextMessage($chat);
                $this->notifyDepartment($chat, $lastClientText ?: '');

                // Отправляем клиенту системное сообщение только один раз
                if (!$alreadyNotified) {
                    $this->sendMessage($chat, "Ваш вопрос отправлен в отдел {$department->name}. Ожидайте ответа.");
                }
                return;
            }
        }

        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }

        // Если сообщение не распознано, увеличиваем счетчик неправильных ответов
        $wrongAnswers = $chat->messenger_data['wrong_answers'] ?? 0;
        $wrongAnswers++;

        $chat->update([
            'messenger_data' => array_merge($chat->messenger_data ?? [], [
                'wrong_answers' => $wrongAnswers
            ])
        ]);

        // Отправляем подсказку только после 5 неправильных ответов
        if ($wrongAnswers >= 5) {
            $choicesText = implode(', ', $validChoices);
            $this->sendMessage($chat, "Пожалуйста, выберите номер отдела ({$choicesText}).");

            // Сбрасываем счетчик после отправки подсказки
            $chat->update([
                'messenger_data' => array_merge($chat->messenger_data ?? [], [
                    'wrong_answers' => 0
                ])
            ]);
        }
    }

    /**
     * Обработка выбора для тестового номера
     */
    protected function handleTestNumberSelection($chat, $message, $client)
    {
        // Для тестовых номеров тоже используем реальные отделы организации
        $chatbotDepartments = Department::forChatbot()
            ->where('organization_id', $chat->organization_id)
            ->get();

        // Строим мэппинг по позициям (1..N)
        $departmentMapping = [];
        foreach ($chatbotDepartments as $index => $dept) {
            $departmentMapping[(string)($index + 1)] = $dept;
        }

        if (isset($departmentMapping[$message])) {
            /** @var \App\Models\Department $dept */
            $dept = $departmentMapping[$message];

            $chat->update([
                'messenger_status' => 'department_selected',
                'department_id' => $dept->id
            ]);

            $historyService = app(ChatHistoryService::class);
            $historyService->logDepartmentSelection($chat, $dept);

            $this->sendMessage($chat, "Подключаем с {$dept->name}. Пожалуйста, можете задать вопрос.");
            return;
        }

        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }

        // Если сообщение не распознано, отправляем подсказку
        $this->sendMessage($chat, "Пожалуйста, выберите номер отдела (1 или 2).");
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

        // Проверяем, было ли уже отправлено уведомление о передаче в отдел
        $hasBeenNotified = $chat->messenger_data['department_notified'] ?? false;

        // Создаем активный чат
        $resolvedClientName = $client->name ?: null;
        $chat->update([
            'messenger_status' => 'active',
            'title' => $resolvedClientName ?: ($chat->title ?: ($chat->messenger_phone ?: 'Неизвестный клиент')),
            'last_activity_at' => now(),
            'messenger_data' => array_merge($chat->messenger_data ?? [], [
                'department_notified' => true
            ])
        ]);

        // Уведомляем отдел
        $this->notifyDepartment($chat, $message);

        // Отправляем сообщение о передаче в отдел только один раз
        if (!$hasBeenNotified) {
            $this->sendMessage($chat, "Ваш вопрос отправлен в отдел {$chat->department->name}. Ожидайте ответа.");
        }
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
     * Обработка закрытого чата (сценарий 1)
     */
    protected function handleClosedChat($chat, $message, $client)
    {
        if ($message === '1') {
            // Продолжить общение с последним менеджером/отделом
            if ($chat->assigned_to || $chat->department_id) {
                $chat->update(['messenger_status' => 'active']);

                $managerName = $chat->assignedTo ? $chat->assignedTo->name : 'менеджером отдела';
                $this->sendMessage($chat, "Чат возобновлен с {$managerName}. Можете продолжить общение.");

                // Уведомляем менеджера о возобновлении чата
                $this->notifyManagerChatResumed($chat);
            } else {
                // Если нет назначенного менеджера, возвращаемся к выбору отдела
                $this->sendMessage($chat, "Предыдущий менеджер недоступен. Выберите отдел заново.");
                $this->resetToMenu($chat, $client);
            }
        } elseif ($message === '0') {
            // Вернуться в главное меню
            $this->resetToMenu($chat, $client);
        } else {
            // Неправильный ответ - повторяем предложение
            $this->sendMessage($chat, "Простите, чат был закрыт менеджером.\n\nЕсли хотите продолжить общение с менеджером нажмите 1\nЕсли хотите вернуться в меню нажмите 0");
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
            'assigned_to' => null,
            'messenger_data' => array_merge($chat->messenger_data ?? [], [
                'department_notified' => false // Сбрасываем флаг уведомления
            ])
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

        // Нумерация в меню должна соответствовать позиции (1..N),
        // так как обработчик выбора ожидает именно порядковые номера
        foreach ($departments as $index => $department) {
            $number = $index + 1;
            $text .= "{$number}. {$department->name}\n";
        }

        // Подсказка по возврату в меню из других состояний
        $text .= "\n0. Вернуться в главное меню";

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
        // Больше не используем хардкод отделов; оставлено для совместимости
        return "Добрый день! Выберите отдел, отправив его номер из списка.";
    }

    /**
     * Сохранение сообщения клиента
     */
    protected function saveClientMessage($chat, $message, $client)
    {
        Log::info('💬 Saving client message', [
            'chat_id' => $chat->id,
            'client_id' => $client->id,
            'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
            'phone' => $chat->messenger_phone
        ]);

        $messageRecord = Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Используем системного пользователя для всех сообщений
            'content' => $message,
            'type' => 'text',
            'is_from_client' => true, // Это сообщение от клиента
            'messenger_message_id' => 'client_' . time() . '_' . rand(1000, 9999),
            'metadata' => [
                'original_message' => $message,
                'client_id' => $client->id,
                'client_name' => $client->name,
                'direction' => 'incoming'
            ]
        ]);

        Log::info('✅ Client message saved', [
            'message_id' => $messageRecord->id,
            'chat_id' => $chat->id
        ]);

        // Отправляем событие в Redis для SSE
        $this->publishMessageToRedis($chat->id, $messageRecord);

        return $messageRecord;
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
                'user_id' => 1, // Используем системного пользователя
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
                    'client_id' => $client->id,
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
                'user_id' => 1, // Используем системного пользователя
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
     * Обработка входящего аудио - УДАЛЕНО
     * Аудио больше не поддерживается
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
                'user_id' => 1, // Используем системного пользователя
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
                    'client_id' => $client->id,
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
                'user_id' => 1, // Используем системного пользователя
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
                    'client_id' => $client->id,
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
            // Сохраняем аудио через сервис
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

            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'audio',
                'metadata' => [
                    'file_path' => $audioData['url'],
                    'file_name' => $audioData['filename'],
                    'file_size' => $audioData['size'],
                    'audio_url' => $audioData['url'],
                    'audio_path' => $audioData['path'],
                    'audio_filename' => $audioData['filename'],
                    'audio_size' => $audioData['size'],
                    'audio_extension' => $audioData['extension'],
                    'original_url' => $audioUrl,
                    'caption' => $caption,
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'direction' => 'incoming'
                ]
            ]);

            // Публикуем событие для SSE
            $this->publishMessageToRedis($chat->id, $message);

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
                'user_id' => 1, // Используем системного пользователя
                'content' => $messageContent,
                'type' => 'location',
                'metadata' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                    'client_id' => $client->id,
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
            // Сохраняем в базу как системное сообщение с временной меткой на 100 миллисекунд позже
            // Получаем время последнего сообщения и добавляем 100ms для правильного порядка
            $lastMessage = Message::where('chat_id', $chat->id)->orderBy('created_at', 'desc')->first();
            $systemMessageTime = $lastMessage ?
                $lastMessage->created_at->addMilliseconds(200) :
                now()->addMilliseconds(200);

            // Создаем сообщение с точным временем через DB::table для обхода автоматических timestamps
            $messageId = DB::table('messages')->insertGetId([
                'chat_id' => $chat->id,
                'user_id' => 1, // Системный пользователь
                'content' => $message,
                'type' => 'system', // Системное сообщение от бота
                'is_from_client' => false, // Это сообщение от бота
                'messenger_message_id' => 'bot_' . time() . '_' . rand(1000, 9999),
                'created_at' => $systemMessageTime->format('Y-m-d H:i:s.v'),
                'updated_at' => $systemMessageTime->format('Y-m-d H:i:s.v'),
                'metadata' => json_encode([
                    'direction' => 'outgoing',
                    'is_bot_message' => true,
                    'sender' => 'Система'
                ])
            ]);

            // Получаем созданное сообщение
            $messageRecord = Message::find($messageId);

            Log::info("System message saved", ['chat_id' => $chat->id]);

            // Отправляем системное сообщение в Redis для SSE
            $this->publishMessageToRedis($chat->id, $messageRecord);

            // Отправляем системное сообщение через Wazzup24
            if (class_exists('\App\Services\Wazzup24Service') && $chat->organization) {
                try {
                    $wazzupService = \App\Services\Wazzup24Service::forOrganization($chat->organization);

                    // Получаем данные для отправки
                    $channelId = $wazzupService->getChannelId();
                    $chatType = 'whatsapp';
                    $chatId = $chat->messenger_phone;

                    // Форматируем сообщение для WhatsApp с жирной надписью "Система"
                    $formattedMessage = "*Система*\n\n" . $message;

                    $result = $wazzupService->sendMessage(
                        $channelId,
                        $chatType,
                        $chatId,
                        $formattedMessage,
                        1, // Системный пользователь
                        $messageRecord->id
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to create Wazzup24Service for organization', [
                        'chat_id' => $chat->id,
                        'organization_id' => $chat->organization_id,
                        'error' => $e->getMessage()
                    ]);
                    $result = ['success' => false, 'error' => $e->getMessage()];
                }

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

                // Добавляем заголовок "Система" для сообщений менеджера
                $systemFormattedMessage = "*Система*\n\n" . $formattedMessage;

                $result = $wazzupService->sendMessage(
                    $channelId,
                    $chatType,
                    $chatId,
                    $systemFormattedMessage, // Отправляем сообщение с заголовком "Система"
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
     * Получить последний текстовый входящий месседж клиента
     */
    private function getLastClientTextMessage(Chat $chat): ?string
    {
        try {
            $last = Message::where('chat_id', $chat->id)
                ->where('is_from_client', true)
                ->where('type', 'text')
                ->orderBy('created_at', 'desc')
                ->first();
            return $last?->content;
        } catch (\Exception $e) {
            return null;
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

                // Синхронизируем title чатов по этому номеру, если в title был телефон
                try {
                    $chatsToSync = Chat::where('messenger_phone', $client->phone)
                        ->where('is_messenger_chat', true)
                        ->where(function($q) use ($client) {
                            $q->whereNull('title')
                              ->orWhere('title', $client->phone)
                              ->orWhere('title', 'Клиент ' . $client->phone);
                        })
                        ->get();
                    foreach ($chatsToSync as $c) {
                        $c->update(['title' => $client->name]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Не удалось синхронизировать title чатов с именем клиента', [
                        'client_id' => $client->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $client;
    }

    /**
     * Поиск или создание мессенджер чата
     */
    protected function findOrCreateMessengerChat($phone, $client, $organization = null)
    {
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();

        $isNewChat = false;

        if (!$chat) {
            // Используем переданную организацию или ID 1 по умолчанию
            $organizationId = $organization ? $organization->id : 1;

            // Формируем название чата: имя клиента или номер телефона
            $chatTitle = $client->name && $client->name !== 'Клиент ' . $phone
                ? $client->name
                : $phone;

            $chat = Chat::create([
                'organization_id' => $organizationId,
                'title' => $chatTitle,
                'type' => 'private', // Используем разрешенный тип
                'status' => 'active',
                'created_by' => 1, // Системный пользователь
                'is_messenger_chat' => true,
                'messenger_phone' => $phone,
                'messenger_status' => 'menu',
                'last_activity_at' => now()
            ]);
            $isNewChat = true;

            Log::info('Messenger chat created', [
                'chat_id' => $chat->id,
                'organization_id' => $organizationId,
                'phone' => $phone
            ]);
        }

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

    /**
     * Закрытие чата менеджером (сценарий 1)
     */
    public function closeChat($chatId, $managerId, $reason = 'Чат закрыт менеджером')
    {
        try {
            $chat = Chat::find($chatId);
            if (!$chat) {
                return ['success' => false, 'error' => 'Chat not found'];
            }

            // Обновляем статус чата
            $chat->update([
                'messenger_status' => 'closed',
                'closed_at' => now(),
                'messenger_data' => array_merge($chat->messenger_data ?? [], [
                    'closed_by' => $managerId,
                    'close_reason' => $reason,
                    'closed_at' => now()->toISOString()
                ])
            ]);

            // Отправляем сообщение клиенту о закрытии
            $this->sendMessage($chat, "Простите, чат был закрыт менеджером.\n\nЕсли хотите продолжить общение с менеджером нажмите 1\nЕсли хотите вернуться в меню нажмите 0");

            Log::info('Chat closed by manager', [
                'chat_id' => $chatId,
                'manager_id' => $managerId,
                'reason' => $reason
            ]);

            return ['success' => true, 'message' => 'Chat closed successfully'];

        } catch (\Exception $e) {
            Log::error('Error closing chat', [
                'chat_id' => $chatId,
                'manager_id' => $managerId,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Уведомление менеджера о возобновлении чата
     */
    private function notifyManagerChatResumed($chat)
    {
        try {
            if ($chat->assigned_to) {
                // Отправляем уведомление через Redis
                $notificationData = [
                    'type' => 'chat_resumed',
                    'chat_id' => $chat->id,
                    'client_name' => $chat->title,
                    'message' => 'Клиент возобновил общение в чате',
                    'timestamp' => now()->toISOString()
                ];

                Redis::publish('user.' . $chat->assigned_to . '.notifications', json_encode($notificationData));

                Log::info('Manager notified about chat resume', [
                    'chat_id' => $chat->id,
                    'manager_id' => $chat->assigned_to
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify manager about chat resume', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Отправка сообщения в Redis для SSE
     */
    private function publishMessageToRedis(int $chatId, Message $message): void
    {
        try {
            // Загружаем пользователя для сообщения
            $message->load('user');

            $data = [
                'type' => 'new_message',
                'chatId' => $chatId,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->content, // Для совместимости с фронтендом
                    'content' => $message->content,
                    'type' => $message->type,
                    'is_from_client' => $message->is_from_client,
                    'is_read' => false,
                    'read_at' => null,
                    'file_path' => $message->metadata['file_path'] ?? null,
                    'file_name' => $message->metadata['file_name'] ?? null,
                    'file_size' => $message->metadata['file_size'] ?? null,
                    'created_at' => $message->created_at->toISOString(),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->is_from_client ?
                            ($message->metadata['client_name'] ?? 'Клиент') :
                            $message->user->name,
                        'email' => $message->user->email,
                        'role' => $message->user->role,
                    ],
                ],
                'timestamp' => now()->toISOString()
            ];

            // Публикуем в Redis канал чата
            Redis::publish('chat.' . $chatId, json_encode($data));

            // Также отправляем в глобальные каналы для обновления списка чатов
            if ($message->is_from_client) {
                $chat = Chat::find($chatId);
                if ($chat) {
                    // Данные для обновления списка чатов
                    $chatUpdateData = [
                        'type' => 'new_message',
                        'chat_id' => $chatId,
                        'message' => $data['message'],
                        'sender_name' => $message->metadata['client_name'] ?? 'Клиент',
                        'timestamp' => now()->toISOString()
                    ];

                    // Отправляем в глобальный канал чатов
                    Redis::publish('chats.global', json_encode($chatUpdateData));

                    // Отправляем в канал организации
                    if ($chat->organization_id) {
                        Redis::publish('organization.' . $chat->organization_id . '.chats', json_encode($chatUpdateData));
                    }

                    // Отправляем всем пользователям организации через отделы
                    $users = User::whereHas('department', function($query) use ($chat) {
                        $query->where('organization_id', $chat->organization_id);
                    })->pluck('id');
                    foreach ($users as $userId) {
                        Redis::publish('user.' . $userId . '.chats', json_encode($chatUpdateData));
                    }
                }
            }

            Log::info('📡 Messenger message published to Redis', [
                'chat_id' => $chatId,
                'message_id' => $message->id,
                'channel' => 'chat.' . $chatId,
                'is_from_client' => $message->is_from_client,
                'global_events_sent' => $message->is_from_client
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to publish messenger message to Redis', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'message_id' => $message->id
            ]);
        }
    }
}
