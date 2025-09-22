<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Services\Wazzup24Service;
use App\Services\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Wazzup24Controller extends Controller
{
    private Wazzup24Service $wazzupService;
    private MessengerService $messengerService;

    public function __construct(Wazzup24Service $wazzupService, MessengerService $messengerService)
    {
        $this->wazzupService = $wazzupService;
        $this->messengerService = $messengerService;
    }

    /**
     * Отправка сообщения через Wazzup24
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try {
            // Проверяем, что чат связан с Wazzup24
            if (!$chat->phone || $chat->type !== 'wazzup') {
                return response()->json([
                    'success' => false,
                    'message' => 'Этот чат не поддерживает отправку через Wazzup24'
                ], 400);
            }

            $content = $request->input('content');

            // Получаем channelId из конфига или из первого доступного канала
            $channelId = config('services.wazzup24.channel_id');
            if (!$channelId) {
                $channels = $this->wazzupService->getChannels();
                if ($channels['success'] && !empty($channels['channels'])) {
                    $channelId = $channels['channels'][0]['channelId'] ?? null;
                }
            }

            if (!$channelId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не найден активный канал Wazzup24'
                ], 400);
            }

            // Отправляем сообщение через Wazzup24
            $result = $this->wazzupService->sendMessage(
                $channelId,
                'whatsapp', // chatType для WhatsApp
                $chat->phone, // chatId - номер телефона
                $content,
                auth()->id(), // crmUserId
                'msg_' . time() . '_' . rand(1000, 9999) // crmMessageId для идемпотентности
            );

            if (!$result['success']) {
                Log::error('Failed to send Wazzup24 message', [
                    'chat_id' => $chat->id,
                    'phone' => $chat->phone,
                    'error' => $result['error']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка отправки сообщения: ' . $result['error']
                ], 500);
            }

            // Сохраняем сообщение в базе данных
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'content' => $content,
                'wazzup_message_id' => $result['message_id'] ?? null,
                'direction' => 'out',
                'type' => 'text',
                'status' => 'sent'
            ]);

            Log::info('Wazzup24 message sent successfully', [
                'chat_id' => $chat->id,
                'message_id' => $message->id,
                'wazzup_message_id' => $result['message_id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Сообщение отправлено',
                'data' => [
                    'message_id' => $message->id,
                    'wazzup_message_id' => $result['message_id'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Wazzup24 send message error', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера'
            ], 500);
        }
    }

    /**
     * Отправка медиа файла через Wazzup24
     */
    public function sendMedia(Request $request, Chat $chat)
    {
        $request->validate([
            'media_url' => 'required|url',
            'media_type' => 'required|in:image,video,audio,document',
            'caption' => 'nullable|string|max:1000',
            'file_name' => 'nullable|string|max:255'
        ]);

        try {
            // Проверяем, что чат связан с Wazzup24
            if (!$chat->phone || $chat->type !== 'wazzup') {
                return response()->json([
                    'success' => false,
                    'message' => 'Этот чат не поддерживает отправку через Wazzup24'
                ], 400);
            }

            $mediaUrl = $request->input('media_url');
            $mediaType = $request->input('media_type');
            $caption = $request->input('caption');
            $fileName = $request->input('file_name');

            // Получаем channelId из конфига или из первого доступного канала
            $channelId = config('services.wazzup24.channel_id');
            if (!$channelId) {
                $channels = $this->wazzupService->getChannels();
                if ($channels['success'] && !empty($channels['channels'])) {
                    $channelId = $channels['channels'][0]['channelId'] ?? null;
                }
            }

            if (!$channelId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не найден активный канал Wazzup24'
                ], 400);
            }

            // Отправляем медиа через Wazzup24
            $result = $this->wazzupService->sendMedia(
                $channelId,
                'whatsapp', // chatType для WhatsApp
                $chat->phone, // chatId - номер телефона
                $mediaUrl,
                $mediaType,
                $caption,
                $fileName,
                auth()->id(), // crmUserId
                'media_' . time() . '_' . rand(1000, 9999) // crmMessageId для идемпотентности
            );

            if (!$result['success']) {
                Log::error('Failed to send Wazzup24 media', [
                    'chat_id' => $chat->id,
                    'phone' => $chat->phone,
                    'media_type' => $mediaType,
                    'error' => $result['error']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка отправки медиа: ' . $result['error']
                ], 500);
            }

            // Сохраняем сообщение в базе данных
            $messageContent = $caption ?: $fileName ?: 'Медиа файл';
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => auth()->id(),
                'content' => $messageContent,
                'wazzup_message_id' => $result['message_id'] ?? null,
                'direction' => 'out',
                'type' => $mediaType,
                'status' => 'sent',
                'metadata' => [
                    'file_path' => $mediaUrl,
                    'file_name' => $fileName,
                    'wazzup_sent' => true,
                    'sent_via' => 'wazzup24'
                ]
            ]);

            // Публикуем сообщение в Redis для SSE
            $this->messengerService->publishMessageToRedis($chat->id, $message);

            Log::info('Wazzup24 media sent successfully', [
                'chat_id' => $chat->id,
                'message_id' => $message->id,
                'media_type' => $mediaType,
                'wazzup_message_id' => $result['message_id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Медиа файл отправлен',
                'data' => [
                    'message_id' => $message->id,
                    'wazzup_message_id' => $result['message_id'] ?? null,
                    'media_type' => $mediaType
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Wazzup24 send media error', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера'
            ], 500);
        }
    }

    /**
     * Проверка статуса подключения к Wazzup24
     */
    public function checkConnection()
    {
        try {
            $result = $this->wazzupService->testConnection();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Подключение активно' : 'Ошибка подключения',
                'data' => $result['data'] ?? null,
                'error' => $result['error'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Wazzup24 connection check error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка проверки подключения',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение информации о чате в Wazzup24
     */
    public function getChatInfo(Chat $chat)
    {
        try {
            if (!$chat->wazzup_chat_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Чат не связан с Wazzup24'
                ], 400);
            }

            $result = $this->wazzupService->getChatInfo($chat->wazzup_chat_id);

            return response()->json([
                'success' => $result['success'],
                'data' => $result['data'] ?? null,
                'error' => $result['error'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Wazzup24 get chat info error', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения информации о чате',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
