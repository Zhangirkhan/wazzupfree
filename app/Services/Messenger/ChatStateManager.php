<?php

namespace App\Services\Messenger;

use App\Contracts\ChatStateManagerInterface;
use App\Contracts\MessageProcessorInterface;
use App\Models\Chat;
use App\Models\Client;
use App\Models\Department;
use App\Services\ChatHistoryService;
use Illuminate\Support\Facades\Log;

class ChatStateManager implements ChatStateManagerInterface
{
    public function __construct(
        private MessageProcessorInterface $messageProcessor
    ) {}

    /**
     * Обработка сообщения в зависимости от текущего состояния чата
     */
    public function processMessage(Chat $chat, string $message, Client $client, ?string $wazzupMessageId = null): void
    {
        $message = trim($message);

        // Сохраняем каждое входящее сообщение клиента
        $this->messageProcessor->saveClientMessage($chat, $message, $client, $wazzupMessageId);

        // Если это новый чат, отправляем меню только один раз
        if ($chat->wasRecentlyCreated) {
            $this->sendInitialMenu($chat, $client);
            return;
        }

        switch ($chat->messenger_status) {
            case 'menu':
                $this->handleMenuSelection($chat, $message, $client);
                break;

            case 'department_selected':
                $this->handleDepartmentSelection($chat, $message, $client);
                break;

            case 'active':
                $this->handleActiveChat($chat, $message, $client);
                break;

            case 'completed':
                $this->handleCompletedChat($chat, $message, $client);
                break;

            case 'closed':
                $this->handleClosedChat($chat, $message, $client);
                break;

            default:
                $this->resetToMenu($chat, $client);
                break;
        }
    }

    /**
     * Отправка начального меню (только один раз)
     */
    public function sendInitialMenu(Chat $chat, Client $client): void
    {
        // Показываем только отделы текущей организации с включенным показом в чат-боте
        $departments = Department::forChatbot()
            ->where('organization_id', $chat->organization_id)
            ->get();
        $menuText = $this->generateMenuText($departments);

        // Отправляем меню
        $this->messageProcessor->sendMessage($chat, $menuText);

        // Обновляем статус на ожидание выбора
        $chat->update(['messenger_status' => 'menu']);
    }

    /**
     * Обработка выбора пункта меню
     */
    public function handleMenuSelection(Chat $chat, string $message, Client $client): void
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
                $this->messageProcessor->notifyDepartment($chat, $lastClientText ?: '');

                // Отправляем клиенту системное сообщение только один раз
                if (!$alreadyNotified) {
                    $this->messageProcessor->sendMessage($chat, "Ваш вопрос отправлен в отдел {$department->name}. Ожидайте ответа.");
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
            $this->messageProcessor->sendMessage($chat, "Пожалуйста, выберите номер отдела ({$choicesText}).");

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
    public function handleTestNumberSelection(Chat $chat, string $message, Client $client): void
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

            $this->messageProcessor->sendMessage($chat, "Подключаем с {$dept->name}. Пожалуйста, можете задать вопрос.");
            return;
        }

        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }

        // Если сообщение не распознано, отправляем подсказку
        $this->messageProcessor->sendMessage($chat, "Пожалуйста, выберите номер отдела (1 или 2).");
    }

    /**
     * Обработка выбора отдела
     */
    public function handleDepartmentSelection(Chat $chat, string $message, Client $client): void
    {
        // Обрабатываем "0" - сброс к меню
        if ($message === '0') {
            $this->resetToMenu($chat, $client);
            return;
        }

        if (empty(trim($message))) {
            $this->messageProcessor->sendMessage($chat, "Пожалуйста, напишите ваш вопрос:");
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
        $this->messageProcessor->notifyDepartment($chat, $message);

        // Отправляем сообщение о передаче в отдел только один раз
        if (!$hasBeenNotified) {
            $this->messageProcessor->sendMessage($chat, "Ваш вопрос отправлен в отдел {$chat->department->name}. Ожидайте ответа.");
        }
    }

    /**
     * Обработка активного чата
     */
    public function handleActiveChat(Chat $chat, string $message, Client $client): void
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
            $this->messageProcessor->notifyAssignedUser($chat, $message);
        } else {
            // Если никто не назначен, уведомляем отдел
            $this->messageProcessor->notifyDepartment($chat, $message);
        }
    }

    /**
     * Обработка завершенного чата
     */
    public function handleCompletedChat(Chat $chat, string $message, Client $client): void
    {
        if ($message === '1') {
            // Продолжить чат с тем же менеджером (обход меню и отделов)
            if ($chat->assigned_to) {
                $chat->update(['messenger_status' => 'active']);
                $this->messageProcessor->sendMessage($chat, "Чат продолжен с тем же менеджером. Можете задать новый вопрос.");
            } else {
                // Если нет назначенного менеджера, возвращаемся в меню
                $this->messageProcessor->sendMessage($chat, "К сожалению, предыдущий менеджер недоступен. Выберите отдел заново.");
                $this->resetToMenu($chat, $client);
            }
        } elseif ($message === '0') {
            // Сбросить менеджера и отдел, показать меню заново
            $this->resetToMenu($chat, $client);
        } else {
            $this->messageProcessor->sendMessage($chat, "1 - Продолжить чат с тем же менеджером\n0 - Вернуться в главное меню");
        }
    }

    /**
     * Обработка закрытого чата (сценарий 1)
     */
    public function handleClosedChat(Chat $chat, string $message, Client $client): void
    {
        if ($message === '1') {
            // Продолжить общение с последним менеджером/отделом
            if ($chat->assigned_to || $chat->department_id) {
                $chat->update(['messenger_status' => 'active']);

                $managerName = $chat->assignedTo ? $chat->assignedTo->name : 'менеджером отдела';
                $this->messageProcessor->sendMessage($chat, "Чат возобновлен с {$managerName}. Можете продолжить общение.");

                // Уведомляем менеджера о возобновлении чата
                $this->notifyManagerChatResumed($chat);
            } else {
                // Если нет назначенного менеджера, возвращаемся к выбору отдела
                $this->messageProcessor->sendMessage($chat, "Предыдущий менеджер недоступен. Выберите отдел заново.");
                $this->resetToMenu($chat, $client);
            }
        } elseif ($message === '0') {
            // Вернуться в главное меню
            $this->resetToMenu($chat, $client);
        } else {
            // Неправильный ответ - повторяем предложение
            $this->messageProcessor->sendMessage($chat, "Простите, чат был закрыт менеджером.\n\nЕсли хотите продолжить общение с менеджером нажмите 1\nЕсли хотите вернуться в меню нажмите 0");
        }
    }

    /**
     * Сброс к главному меню
     */
    public function resetToMenu(Chat $chat, Client $client): void
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
    private function generateMenuText($departments): string
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
    private function isTestNumber(string $phone): bool
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
     * Получение последнего текстового сообщения клиента
     */
    private function getLastClientTextMessage(Chat $chat): ?string
    {
        $lastMessage = $chat->messages()
            ->where('is_from_client', true)
            ->where('type', 'text')
            ->latest()
            ->first();

        return $lastMessage ? $lastMessage->content : null;
    }

    /**
     * Уведомление менеджера о возобновлении чата
     */
    private function notifyManagerChatResumed(Chat $chat): void
    {
        // Логика уведомления менеджера о возобновлении чата
        // Этот метод можно будет реализовать позже или вынести в отдельный сервис
        Log::info('Chat resumed', ['chat_id' => $chat->id, 'assigned_to' => $chat->assigned_to]);
    }
}
