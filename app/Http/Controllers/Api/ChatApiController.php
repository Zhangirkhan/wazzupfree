<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ChatServiceInterface;
use App\Contracts\MessageServiceInterface;
use App\Http\Requests\CreateChatRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\TransferChatRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatApiController extends ApiController
{
    public function __construct(
        private ChatServiceInterface $chatService,
        private MessageServiceInterface $messageService,
        private LoggingService $loggingService
    ) {}
    /**
     * Получить список чатов пользователя
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $chats = $this->chatService->getUserChats($user);

            return $this->paginatedResponse(ChatResource::collection($chats), 'Chats retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve chats', $e->getMessage(), 500);
        }
    }

    /**
     * Создать новый чат
     */
    public function store(CreateChatRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = $this->chatService->createChat($request->validated(), $user);
            
            $this->loggingService->logChatActivity('chat_created', $chat->id, [
                'user_id' => $user->id,
                'title' => $chat->title
            ]);

            return $this->successResponse(new ChatResource($chat), 'Chat created successfully', 201);
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to create chat', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->validated()
            ]);
            return $this->errorResponse('Failed to create chat', $e->getMessage(), 500);
        }
    }

    /**
     * Получить конкретный чат
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = $this->chatService->getChat($id, $user);

            if (!$chat) {
                return $this->notFoundResponse('Chat not found');
            }

            return $this->successResponse(new ChatResource($chat), 'Chat retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve chat', $e->getMessage(), 500);
        }
    }

    /**
     * Отправить сообщение в чат
     *
     * @param Request $request
     * @param string $chatId
     * @return JsonResponse
     */
    public function sendMessage(Request $request, string $chatId): JsonResponse
    {
        $hasFile = $request->hasFile('file');
        $fileInfo = null;
        if ($hasFile) {
            $file = $request->file('file');
            $fileInfo = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ];
        }

        Log::info('🔹 БЭК: Получен запрос на отправку сообщения', [
            'chat_id' => $chatId,
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'has_file' => $hasFile,
            'file_info' => $fileInfo
        ]);

        try {
            $user = Auth::user();

            // Валидация данных
        $validated = $request->validate([
            'message' => 'required|string',
            'type' => 'nullable|string|in:text,file,image,video,audio,document',
            'file' => 'nullable|file|max:' . config('uploads.max_file_size_kb', 51200), // 50MB лимит из конфигурации
            'reply_to_message_id' => 'nullable|integer|exists:messages,id' // ID сообщения для ответа
        ]);

            Log::info('🔹 БЭК: Данные прошли валидацию', [
                'validated' => $validated,
                'user_id' => $user->id
            ]);

            $file = null;
            if (isset($validated['file'])) {
                $file = $validated['file'];
                Log::info('🔹 БЭК: Файл найден в запросе', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType()
                ]);
            }

            $message = $this->messageService->sendMessage(
                $chatId,
                $validated['message'],
                $user,
                $validated['type'] ?? 'text',
                $file,
                $validated['reply_to_message_id'] ?? null
            );

            Log::info('🔹 БЭК: Сообщение создано успешно', [
                'message_id' => $message->id,
                'message_type' => $message->type,
                'message_content' => $message->content,
                'has_metadata' => !empty($message->metadata),
                'metadata' => $message->metadata
            ]);

            $response = new MessageResource($message);
            Log::info('🔹 БЭК: Отправляем ответ клиенту', [
                'response_data' => $response->toArray($request)
            ]);

            return $this->successResponse($response, 'Message sent successfully', 201);
        } catch (\Exception $e) {
            Log::error('🔸 БЭК: Ошибка при отправке сообщения', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_id' => $chatId,
                'user_id' => Auth::id()
            ]);
            return $this->errorResponse('Failed to send message', $e->getMessage(), 500);
        }
    }

    /**
     * Получить сообщения чата
     */
    public function getMessages(string $chatId, Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 50);
            $messages = $this->messageService->getChatMessages($chatId, $user, $perPage);

            return $this->paginatedResponse(MessageResource::collection($messages), 'Messages retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve messages', $e->getMessage(), 500);
        }
    }

    /**
     * Поиск чатов
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'status' => 'nullable|in:active,closed,transferred'
        ]);

        try {
            $user = Auth::user();
            $chats = $this->chatService->searchChats($request->get('query'), $user, $request->get('status'));

            return $this->paginatedResponse(ChatResource::collection($chats), 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to search chats', $e->getMessage(), 500);
        }
    }

    /**
     * Завершить чат
     */
    public function endChat(string $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = $this->chatService->endChat($chatId, $user);

            return $this->successResponse(new ChatResource($chat), 'Chat ended successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to end chat', $e->getMessage(), 500);
        }
    }

    /**
     * Закрыть мессенджер чат (для сценария 1)
     */
    public function closeMessengerChat(string $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $messengerService = app(\App\Services\MessengerService::class);

            $result = $messengerService->closeChat($chatId, $user->id, 'Чат закрыт менеджером');

            if ($result['success']) {
                return $this->successResponse(null, $result['message']);
            } else {
                return $this->errorResponse('Failed to close chat', $result['error'], 500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to close messenger chat', $e->getMessage(), 500);
        }
    }

    /**
     * Передать чат другому пользователю
     */
    public function transferChat(TransferChatRequest $request, string $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = $this->chatService->transferChat($chatId, $request->validated()['assigned_to'], $user, $request->validated()['note'] ?? null);

            return $this->successResponse(new ChatResource($chat), 'Chat transferred successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to transfer chat', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить чат
     */
    public function destroy(string $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->chatService->deleteChat($chatId, $user);

            if ($result) {
                $this->loggingService->logChatActivity('chat_deleted', $chatId, [
                    'user_id' => $user->id
                ]);

                return $this->successResponse(null, 'Chat deleted successfully');
            } else {
                return $this->errorResponse('Failed to delete chat', 'Unknown error occurred', 500);
            }
        } catch (\Exception $e) {
            $this->loggingService->logError('Failed to delete chat', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'chat_id' => $chatId
            ]);
            return $this->errorResponse('Failed to delete chat', $e->getMessage(), 500);
        }
    }
}
