<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageReadController extends Controller
{
    /**
     * Отметить сообщение как прочитанное
     */
    public function markAsRead(Request $request, int $messageId): JsonResponse
    {
        try {
            $user = Auth::user();
            $message = Message::findOrFail($messageId);

            // Проверяем, что пользователь может читать это сообщение
            if (!$message->chat->canBeViewedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому сообщению'
                ], 403);
            }

            $messageRead = MessageRead::markAsRead($messageId, $user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'message_id' => $messageId,
                    'user_id' => $user->id,
                    'read_at' => $messageRead->read_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка отметки сообщения как прочитанного', [
                'message_id' => $messageId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отметки сообщения как прочитанного'
            ], 500);
        }
    }

    /**
     * Отметить множество сообщений как прочитанные
     */
    public function markMultipleAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id'
        ]);

        try {
            $user = Auth::user();
            $messageIds = $request->input('message_ids');

            // Получаем сообщения и проверяем доступ
            $messages = Message::with('chat')->whereIn('id', $messageIds)->get();

            $accessibleMessageIds = [];
            foreach ($messages as $message) {
                if ($message->chat->canBeViewedBy($user)) {
                    $accessibleMessageIds[] = $message->id;
                }
            }

            if (empty($accessibleMessageIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к указанным сообщениям'
                ], 403);
            }

            MessageRead::markMultipleAsRead($accessibleMessageIds, $user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'marked_count' => count($accessibleMessageIds),
                    'message_ids' => $accessibleMessageIds
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка отметки сообщений как прочитанных', [
                'message_ids' => $request->input('message_ids'),
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отметки сообщений как прочитанных'
            ], 500);
        }
    }

    /**
     * Отметить все сообщения чата как прочитанные
     */
    public function markChatAsRead(Request $request, int $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = Chat::findOrFail($chatId);

            // Проверяем доступ к чату
            if (!$chat->canBeViewedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому чату'
                ], 403);
            }

            // Получаем все непрочитанные сообщения чата
            $messageIds = $chat->messages()
                ->whereNotExists(function ($query) use ($user) {
                    $query->select(\DB::raw(1))
                        ->from('message_reads')
                        ->whereColumn('message_reads.message_id', 'messages.id')
                        ->where('message_reads.user_id', $user->id);
                })
                ->pluck('id')
                ->toArray();

            if (!empty($messageIds)) {
                MessageRead::markMultipleAsRead($messageIds, $user->id);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'chat_id' => $chatId,
                    'marked_count' => count($messageIds)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка отметки чата как прочитанного', [
                'chat_id' => $chatId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отметки чата как прочитанного'
            ], 500);
        }
    }

    /**
     * Получить статус прочтения сообщений
     */
    public function getReadStatus(Request $request): JsonResponse
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id'
        ]);

        try {
            $user = Auth::user();
            $messageIds = $request->input('message_ids');

            $readStatuses = MessageRead::where('user_id', $user->id)
                ->whereIn('message_id', $messageIds)
                ->get()
                ->keyBy('message_id')
                ->map(function ($read) {
                    return [
                        'is_read' => true,
                        'read_at' => $read->read_at->toISOString()
                    ];
                });

            // Добавляем непрочитанные сообщения
            foreach ($messageIds as $messageId) {
                if (!$readStatuses->has($messageId)) {
                    $readStatuses[$messageId] = [
                        'is_read' => false,
                        'read_at' => null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $readStatuses
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка получения статуса прочтения', [
                'message_ids' => $request->input('message_ids'),
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения статуса прочтения'
            ], 500);
        }
    }
}
