<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Обработка входящих сообщений от Wazzup24
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Wazzup24 webhook received', $request->all());

            // Получаем данные из webhook
            $data = $request->all();
            
            // Проверяем, что это тестовый webhook
            if (isset($data['test']) && $data['test'] === true) {
                Log::info('Wazzup24 test webhook received');
                return response()->json(['status' => 'success'], 200);
            }

            // Обрабатываем сообщения
            if (isset($data['messages']) && is_array($data['messages'])) {
                foreach ($data['messages'] as $messageData) {
                    $this->processMessage($messageData);
                }
            }

            // Обрабатываем статусы сообщений
            if (isset($data['statuses']) && is_array($data['statuses'])) {
                foreach ($data['statuses'] as $statusData) {
                    $this->processMessageStatus($statusData);
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Wazzup24 webhook error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Обработка входящего сообщения
     */
    private function processMessage($messageData)
    {
        try {
            // Извлекаем данные сообщения согласно API v3
            $messageId = $messageData['messageId'] ?? null;
            $channelId = $messageData['channelId'] ?? null;
            $chatType = $messageData['chatType'] ?? null;
            $chatId = $messageData['chatId'] ?? null;
            $text = $messageData['text'] ?? '';
            $status = $messageData['status'] ?? 'inbound';
            $authorName = $messageData['authorName'] ?? 'Клиент';
            $dateTime = $messageData['dateTime'] ?? null;
            $contact = $messageData['contact'] ?? null;

            // Обрабатываем только входящие сообщения
            if ($status !== 'inbound' || !$text) {
                return;
            }

            // Логируем данные перед отправкой в MessengerService
            Log::info('Sending to MessengerService', [
                'chatId' => $chatId,
                'text' => $text,
                'contact' => $contact,
                'contact_type' => gettype($contact)
            ]);

            // Используем MessengerService для обработки сообщения
            $messengerService = app('\App\Services\MessengerService');
            $result = $messengerService->handleIncomingMessage($chatId, $text, $contact);

            Log::info('Wazzup24 message processed successfully', [
                'chat_id' => $result['chat_id'] ?? null,
                'message_id' => $result['message_id'] ?? null,
                'chat_id_wazzup' => $chatId,
                'contact' => $contact
            ]);

        } catch (\Exception $e) {
            Log::error('Wazzup24 message processing error', [
                'error' => $e->getMessage(),
                'message_data' => $messageData
            ]);
        }
    }

    /**
     * Обработка статуса сообщения
     */
    private function processMessageStatus($statusData)
    {
        try {
            $messageId = $statusData['messageId'] ?? null;
            $status = $statusData['status'] ?? null;

            if ($messageId && $status) {
                // Обновляем статус сообщения в базе данных
                $message = Message::where('wazzup_message_id', $messageId)->first();
                if ($message) {
                    $message->update(['status' => $status]);
                    Log::info('Wazzup24 message status updated', [
                        'message_id' => $messageId,
                        'status' => $status
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Wazzup24 status processing error', [
                'error' => $e->getMessage(),
                'status_data' => $statusData
            ]);
        }
    }

    /**
     * Найти или создать чат
     */
    private function findOrCreateChat(string $chatId, ?string $channelId, string $senderName, string $chatType = 'whatsapp'): Chat
    {
        // Сначала ищем по wazzup_chat_id
        $chat = Chat::where('wazzup_chat_id', $chatId)->first();
        if ($chat) {
            return $chat;
        }

        // Ищем по номеру телефона (для WhatsApp chatId это номер телефона)
        $chat = Chat::where('phone', $chatId)
            ->where('type', 'wazzup')
            ->where('status', 'active')
            ->first();

        if ($chat) {
            // Обновляем wazzup_chat_id если его не было
            if (!$chat->wazzup_chat_id) {
                $chat->update(['wazzup_chat_id' => $chatId]);
            }
            return $chat;
        }

        // Создаем новый чат
        // Для тестирования используем первую организацию
        $organization = Organization::first();
        
        if (!$organization) {
            throw new \Exception('No organization found for Wazzup24 chat');
        }

        return Chat::create([
            'title' => "Чат с {$senderName}",
            'phone' => $chatId, // Для WhatsApp chatId это номер телефона
            'wazzup_chat_id' => $chatId,
            'type' => 'wazzup',
            'status' => 'active',
            'organization_id' => $organization->id,
            'creator_id' => 1, // Для тестирования используем первого пользователя
            'description' => "Автоматически созданный {$chatType} чат с {$senderName} ({$chatId})"
        ]);
    }

    /**
     * Сохранить сообщение
     */
    private function saveMessage(Chat $chat, string $content, ?string $wazzupMessageId, string $direction, ?string $dateTime = null): Message
    {
        $data = [
            'chat_id' => $chat->id,
            'user_id' => $direction === 'in' ? null : auth()->id(),
            'content' => $content,
            'wazzup_message_id' => $wazzupMessageId,
            'direction' => $direction,
            'type' => 'text',
            'status' => $direction === 'in' ? 'received' : 'sent'
        ];

        // Добавляем время создания, если передано
        if ($dateTime) {
            $data['created_at'] = $dateTime;
        }

        return Message::create($data);
    }
}
