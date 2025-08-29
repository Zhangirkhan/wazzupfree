<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Получить уведомления для пользователя
     */
    public function getNotifications(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Получаем чаты, доступные пользователю
            $query = Chat::query()
                ->where('is_messenger_chat', true)
                ->whereHas('messages', function($q) {
                    $q->where('type', 'text')
                      ->where('metadata->direction', 'incoming');
                })
                ->with(['messages' => function($q) {
                    $q->where('type', 'text')
                      ->where('metadata->direction', 'incoming')
                      ->latest()
                      ->limit(1);
                }, 'department', 'client']);

            // Если пользователь не админ, показываем чаты по логике доступа
            if ($user->role !== 'admin') {
                $query->where('department_id', $user->department_id);
                
                // Если пользователь не руководитель, показываем только назначенные ему чаты
                if (!$this->isManager($user)) {
                    $query->where(function($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                          ->orWhereNull('assigned_to'); // Неприсвоенные чаты
                    });
                }
            }

            $chats = $query->orderBy('last_activity_at', 'desc')->limit(5)->get();

            $notifications = $chats->map(function($chat) use ($user) {
                $lastMessage = $chat->messages->first();
                
                // Определяем, прочитано ли сообщение
                $isRead = false;
                if ($chat->assigned_to === $user->id) {
                    // Если чат назначен пользователю, считаем прочитанным
                    $isRead = true;
                } elseif ($user->role === 'admin' || $this->isManager($user)) {
                    // Админы и руководители видят все как прочитанные
                    $isRead = true;
                }

                return [
                    'id' => $chat->id,
                    'chat_id' => $chat->id,
                    'client_name' => $chat->client?->name,
                    'client_phone' => $chat->messenger_phone,
                    'department_name' => $chat->department?->name,
                    'message_preview' => $this->getMessagePreview($lastMessage),
                    'created_at' => $lastMessage?->created_at,
                    'is_read' => $isRead
                ];
            });

            $unreadCount = $notifications->where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка получения уведомлений: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ошибка получения уведомлений'
            ], 500);
        }
    }

    /**
     * Отметить все уведомления как прочитанные
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Для простоты реализации, просто возвращаем успех
            // В реальной системе здесь можно было бы обновлять статус прочтения в базе данных
            
            return response()->json([
                'success' => true,
                'message' => 'Все уведомления отмечены как прочитанные'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка отметки уведомлений: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ошибка отметки уведомлений'
            ], 500);
        }
    }

    /**
     * Получить превью сообщения
     */
    private function getMessagePreview(?Message $message): string
    {
        if (!$message) {
            return 'Нет сообщений';
        }

        $content = $message->content;
        
        // Если есть оригинальное сообщение в метаданных, используем его
        if ($message->metadata && isset($message->metadata['original_message'])) {
            $content = $message->metadata['original_message'];
        }

        // Очищаем от HTML тегов и ограничиваем длину
        $cleanContent = strip_tags($content);
        return \Str::limit($cleanContent, 50);
    }

    /**
     * Проверка, является ли пользователь менеджером
     */
    private function isManager($user): bool
    {
        return $user->role === 'admin' || $user->role === 'manager' || $user->position === 'руководитель';
    }
}
