<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Message;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatSSEController extends Controller
{
    /**
     * Server-Sent Events endpoint для real-time обновлений чата
     */
    public function stream(Request $request, $chatId)
    {
        $user = Auth::user();
        
        // Проверяем доступ к чату
        $chat = Chat::find($chatId);
        if (!$chat) {
            return response('Chat not found', 404);
        }
        
        // Проверяем права доступа
        if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
            return response('Access denied', 403);
        }
        
        // Если пользователь не руководитель, проверяем назначение
        if ($user->role !== 'admin' && !$this->isManager($user) && $chat->assigned_to !== $user->id) {
            return response('Access denied - chat not assigned to you', 403);
        }
        
        // Получаем последний ID сообщения для отслеживания новых
        $lastMessageId = $request->get('last_message_id', 0);
        
        // Настраиваем SSE заголовки
        $response = new Response();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Cache-Control');
        
        // Функция для отправки данных
        $sendData = function($data) {
            echo "data: " . json_encode($data) . "\n\n";
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        };
        
        // Отправляем начальное сообщение о подключении
        $sendData([
            'type' => 'connected',
            'message' => 'Connected to chat stream',
            'chat_id' => $chatId,
            'timestamp' => now()->toISOString()
        ]);
        
        // Основной цикл для отслеживания новых сообщений
        $lastCheckTime = now();
        $maxExecutionTime = 300; // 5 минут
        $startTime = time();
        
        while (true) {
            // Проверяем время выполнения
            if (time() - $startTime > $maxExecutionTime) {
                $sendData([
                    'type' => 'timeout',
                    'message' => 'Connection timeout',
                    'timestamp' => now()->toISOString()
                ]);
                break;
            }
            
            // Проверяем соединение
            if (connection_aborted()) {
                Log::info('SSE connection aborted', ['chat_id' => $chatId, 'user_id' => $user->id]);
                break;
            }
            
            try {
                // Получаем новые сообщения
                $newMessages = Message::where('chat_id', $chatId)
                    ->where('id', '>', $lastMessageId)
                    ->with(['user'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                if ($newMessages->count() > 0) {
                    foreach ($newMessages as $message) {
                        // Формируем данные сообщения
                        $messageData = $this->formatMessageData($message);
                        
                        $sendData([
                            'type' => 'new_message',
                            'message' => $messageData,
                            'timestamp' => now()->toISOString()
                        ]);
                        
                        $lastMessageId = $message->id;
                    }
                }
                
                // Проверяем обновления статуса чата
                $chat->refresh();
                if ($chat->updated_at > $lastCheckTime) {
                    $sendData([
                        'type' => 'chat_updated',
                        'chat' => [
                            'id' => $chat->id,
                            'status' => $chat->messenger_status,
                            'assigned_to' => $chat->assignedTo ? $chat->assignedTo->name : null,
                            'last_activity_at' => $chat->last_activity_at
                        ],
                        'timestamp' => now()->toISOString()
                    ]);
                    $lastCheckTime = $chat->updated_at;
                }
                
                // Отправляем ping каждые 30 секунд для поддержания соединения
                if (now()->diffInSeconds($lastCheckTime) >= 30) {
                    $sendData([
                        'type' => 'ping',
                        'timestamp' => now()->toISOString()
                    ]);
                    $lastCheckTime = now();
                }
                
            } catch (\Exception $e) {
                Log::error('SSE error', [
                    'chat_id' => $chatId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $sendData([
                    'type' => 'error',
                    'message' => 'An error occurred',
                    'timestamp' => now()->toISOString()
                ]);
                break;
            }
            
            // Пауза между проверками
            sleep(1);
        }
        
        return $response;
    }
    
    /**
     * Форматирование данных сообщения для отправки
     */
    private function formatMessageData($message)
    {
        $isFromClient = $message->user->role === 'client';
        $senderName = $isFromClient ? $message->user->name : 'Менеджер';
        $senderAvatar = $isFromClient ? 'К' : 'М';
        
        // Обрабатываем медиа данные
        $imageData = null;
        $videoData = null;
        $stickerData = null;
        $documentData = null;
        $audioData = null;
        $locationData = null;
        
        if ($message->type === 'image' && $message->metadata && isset($message->metadata['image_url'])) {
            $imageData = [
                'url' => $message->metadata['image_url'],
                'path' => $message->metadata['image_path'] ?? null,
                'filename' => $message->metadata['image_filename'] ?? null,
                'size' => $message->metadata['image_size'] ?? null,
                'extension' => $message->metadata['image_extension'] ?? null,
                'caption' => $message->metadata['caption'] ?? null
            ];
        } elseif ($message->type === 'video' && $message->metadata && isset($message->metadata['video_url'])) {
            $videoData = [
                'url' => $message->metadata['video_url'],
                'path' => $message->metadata['video_path'] ?? null,
                'filename' => $message->metadata['video_filename'] ?? null,
                'size' => $message->metadata['video_size'] ?? null,
                'extension' => $message->metadata['video_extension'] ?? null,
                'caption' => $message->metadata['caption'] ?? null
            ];
        } elseif ($message->type === 'sticker' && $message->metadata && isset($message->metadata['sticker_url'])) {
            $stickerData = [
                'url' => $message->metadata['sticker_url'],
                'caption' => $message->metadata['caption'] ?? null
            ];
        } elseif ($message->type === 'document' && $message->metadata && isset($message->metadata['document_url'])) {
            $documentData = [
                'url' => $message->metadata['document_url'],
                'name' => $message->metadata['document_name'] ?? 'Документ',
                'caption' => $message->metadata['caption'] ?? null
            ];
        } elseif ($message->type === 'audio' && $message->metadata && isset($message->metadata['audio_url'])) {
            $audioData = [
                'url' => $message->metadata['audio_url'],
                'caption' => $message->metadata['caption'] ?? null
            ];
        } elseif ($message->type === 'location' && $message->metadata && isset($message->metadata['latitude'])) {
            $locationData = [
                'latitude' => $message->metadata['latitude'],
                'longitude' => $message->metadata['longitude'],
                'address' => $message->metadata['address'] ?? null
            ];
        }
        
        return [
            'id' => $message->id,
            'content' => $message->content,
            'created_at' => $message->created_at->toISOString(),
            'is_from_client' => $isFromClient,
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'type' => $message->type,
            'metadata' => $message->metadata,
            'image_data' => $imageData,
            'video_data' => $videoData,
            'sticker_data' => $stickerData,
            'document_data' => $documentData,
            'audio_data' => $audioData,
            'location_data' => $locationData
        ];
    }
    
    /**
     * Отправка события о новом сообщении (для использования в других контроллерах)
     */
    public static function broadcastNewMessage($chatId, $message)
    {
        // Этот метод можно использовать для отправки событий через Redis/Broadcasting
        // Пока что просто логируем
        Log::info('Broadcasting new message', [
            'chat_id' => $chatId,
            'message_id' => $message->id
        ]);
    }
    
    /**
     * Проверяет, является ли пользователь руководителем
     */
    private function isManager($user): bool
    {
        // Проверяем роль пользователя
        if ($user->role === 'admin' || $user->role === 'manager') {
            return true;
        }
        
        // Можно добавить дополнительную логику проверки должности
        // Например, проверка через таблицу user_positions
        
        return false;
    }
}
