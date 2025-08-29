<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Client;
use App\Models\Message;
use App\Services\ChatHistoryService;
use Illuminate\Support\Facades\Auth;

class UserChatController extends Controller
{
    /**
     * Показать страницу чата
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Получаем чаты пользователя
        $query = Chat::query()
            ->where('is_messenger_chat', true)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'department', 'assignedTo'])
            ->orderBy('last_activity_at', 'desc');

        // Если пользователь не админ, показываем чаты по новой логике
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

        // Поиск по названию, телефону или описанию
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('messenger_phone', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $chats = $query->get();

        // Подготавливаем данные для отображения
        $chatsData = $chats->map(function($chat) {
            $lastMessage = $chat->messages->first();
            $lastMessagePreview = '';
            $lastMessageTime = null;
            
            if ($lastMessage) {
                $lastMessageTime = $lastMessage->created_at;
                
                // Определяем содержимое последнего сообщения
                if ($lastMessage->metadata && isset($lastMessage->metadata['direction']) && $lastMessage->metadata['direction'] === 'incoming') {
                    // Сообщение от клиента
                    if ($lastMessage->metadata && isset($lastMessage->metadata['original_message'])) {
                        $content = $lastMessage->metadata['original_message'];
                    } else {
                        $content = $lastMessage->content;
                    }
                } else {
                    $content = $lastMessage->content;
                }
                
                $lastMessagePreview = \Str::limit(strip_tags(str_replace('<br>', ' ', $content)), 50);
            } else {
                // Если нет сообщений, показываем статус чата
                switch($chat->messenger_status) {
                    case 'menu':
                        $lastMessagePreview = 'В главном меню';
                        break;
                    case 'department_selected':
                        $lastMessagePreview = 'Отдел выбран';
                        break;
                    case 'active':
                        $lastMessagePreview = 'Активный чат';
                        break;
                    case 'completed':
                        $lastMessagePreview = 'Завершен';
                        break;
                    default:
                        $lastMessagePreview = 'Новый чат';
                }
            }

            // Рассчитываем количество непрочитанных сообщений
            $unreadCount = 0;
            $user = Auth::user();
            
            if ($chat->assigned_to === $user->id) {
                // Если чат назначен пользователю, считаем все прочитанными
                $unreadCount = 0;
            } elseif ($user->role === 'admin' || $this->isManager($user)) {
                // Админы и руководители видят все как прочитанные
                $unreadCount = 0;
            } else {
                // Для обычных сотрудников считаем непрочитанными сообщения от клиентов
                $unreadCount = $chat->messages()
                    ->where('metadata->direction', 'incoming')
                    ->where('created_at', '>', now()->subDays(1)) // Только за последний день
                    ->count();
            }

            return [
                'id' => $chat->id,
                'title' => $chat->messenger_phone ?? $chat->title ?? 'Без названия',
                'avatar_text' => strtoupper(substr($chat->messenger_phone ?? 'К', 0, 1)),
                'is_online' => $chat->messenger_status === 'active',
                'last_message_preview' => $lastMessagePreview,
                'last_message_time' => $lastMessageTime,
                'unread_count' => $unreadCount,
                'department' => $chat->department ? $chat->department->name : null,
                'assigned_to' => $chat->assignedTo ? $chat->assignedTo->name : null,
                'status' => $chat->messenger_status,
                'phone' => $chat->messenger_phone
            ];
        });

        // Если указан параметр chat, получаем данные чата
        $currentChat = null;
        $currentClient = null;
        $currentMessages = [];
        
        if ($request->filled('chat')) {
            $chatId = $request->chat;
            $chat = Chat::with(['messages' => function($query) {
                $query->orderBy('created_at', 'asc');
            }, 'assignedTo'])
            ->where('is_messenger_chat', true)
            ->find($chatId);

            if ($chat) {
                // Проверяем доступ к чату
                if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
                    abort(403, 'Доступ запрещен. Этот чат не принадлежит вашему отделу.');
                }
                // Находим клиента по номеру телефона
                $client = Client::where('phone', $chat->messenger_phone)->first();

                $currentChat = [
                    'id' => $chat->id,
                    'title' => $chat->messenger_phone ?? $chat->title,
                    'status' => $chat->messenger_status,
                    'assigned_to' => $chat->assignedTo ? $chat->assignedTo->name : null,
                    'phone' => $chat->messenger_phone
                ];

                $currentClient = $client ? [
                    'id' => $client->id,
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'email' => $client->email
                ] : null;

                $currentMessages = $chat->messages->map(function($message) {
                    $isFromClient = false;
                    $senderName = 'Система';
                    $senderAvatar = 'С';

                    // Проверяем, является ли сообщение клиентским
                    if ($message->metadata && isset($message->metadata['is_client_message']) && $message->metadata['is_client_message']) {
                        $isFromClient = true;
                        $senderName = $message->metadata['client_name'] ?? 'Клиент';
                        $senderAvatar = 'К';
                    } elseif ($message->metadata && isset($message->metadata['direction']) && $message->metadata['direction'] === 'incoming') {
                        $isFromClient = true;
                        $senderName = $message->metadata['client_name'] ?? 'Клиент';
                        $senderAvatar = 'К';
                    } elseif ($message->metadata && isset($message->metadata['manager_name'])) {
                        // Для сообщений от менеджера используем его имя
                        $senderName = $message->metadata['manager_name'];
                        $senderAvatar = 'М';
                    } elseif ($message->type === 'system' || ($message->metadata && isset($message->metadata['is_bot_message']))) {
                        $senderName = 'Система';
                        $senderAvatar = 'С';
                    } elseif ($message->user) {
                        $senderName = $message->user->name;
                        $senderAvatar = strtoupper(substr($message->user->name, 0, 1));
                    }

                    // Для сообщений от менеджера используем оригинальное сообщение без **Имя**
                    $content = $this->cleanForJson($message->content);
                    if ($message->metadata && isset($message->metadata['manager_name']) && isset($message->metadata['original_message'])) {
                        $content = $this->cleanForJson($message->metadata['original_message']);
                    }

                    return [
                        'id' => $message->id,
                        'content' => $content,
                        'created_at' => $message->created_at,
                        'is_from_client' => $isFromClient,
                        'sender_name' => $senderName,
                        'sender_avatar' => $senderAvatar,
                        'type' => $message->type,
                        'metadata' => $message->metadata
                    ];
                });
            }
        }

        return view('user.chat.index', compact('chatsData', 'currentChat', 'currentClient', 'currentMessages'));
    }

    /**
     * API для поиска чатов (AJAX)
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search', '');
        
        $query = Chat::query()
            ->where('is_messenger_chat', true)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('messenger_phone', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });

        // Если пользователь не админ, показываем чаты по новой логике
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

        $chats = $query->orderBy('last_activity_at', 'desc')->get();

        $chatsData = $chats->map(function($chat) {
            $lastMessage = $chat->messages->first();
            $lastMessagePreview = '';
            $lastMessageTime = null;
            
            if ($lastMessage) {
                $lastMessageTime = $lastMessage->created_at;
                if ($lastMessage->metadata && isset($lastMessage->metadata['original_message'])) {
                    $content = $lastMessage->metadata['original_message'];
                } else {
                    $content = $lastMessage->content;
                }
                $lastMessagePreview = \Str::limit(strip_tags(str_replace('<br>', ' ', $content)), 50);
            } else {
                switch($chat->messenger_status) {
                    case 'menu': $lastMessagePreview = 'В главном меню'; break;
                    case 'department_selected': $lastMessagePreview = 'Отдел выбран'; break;
                    case 'active': $lastMessagePreview = 'Активный чат'; break;
                    case 'completed': $lastMessagePreview = 'Завершен'; break;
                    default: $lastMessagePreview = 'Новый чат';
                }
            }

            return [
                'id' => $chat->id,
                'title' => $chat->messenger_phone ?? $chat->title ?? 'Без названия',
                'avatar_text' => strtoupper(substr($chat->messenger_phone ?? 'К', 0, 1)),
                'is_online' => $chat->messenger_status === 'active',
                'last_message_preview' => $lastMessagePreview,
                'last_message_time' => $lastMessageTime,
                'unread_count' => $chat->messenger_status === 'active' && !$chat->assigned_to ? 1 : 0,
                'status' => $chat->messenger_status,
                'phone' => $chat->messenger_phone
            ];
        });

        return response()->json($chatsData);
    }

    /**
     * API для поиска клиентов (для Select2)
     */
    public function searchClients(Request $request)
    {
        $search = $request->input('q', '');
        
        $clients = Client::query()
            ->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('name', 'asc')
            ->limit(20)
            ->get();

        $results = $clients->map(function($client) {
            return [
                'id' => $client->id,
                'text' => $client->name . ' (' . $client->phone . ')',
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email
            ];
        });

        // Если это AJAX запрос от Select2, возвращаем в нужном формате
        if ($request->ajax()) {
            return response()->json([
                'results' => $results
            ]);
        }

        // Если это обычный GET запрос, возвращаем простой JSON
        return response()->json($results);
    }

    /**
     * Создать новый чат
     */
    public function createChat(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'message' => 'nullable|string|max:1000'
        ]);

        $client = Client::findOrFail($request->client_id);
        $user = Auth::user();

        // Создаем новый чат
        $chat = Chat::create([
            'organization_id' => $user->organizations->first()->id ?? 1,
            'title' => 'Чат с ' . $client->name,
            'description' => $request->message,
            'type' => 'messenger',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'active',
            'phone' => $client->phone,
            'messenger_phone' => $client->phone,
            'is_messenger_chat' => true,
            'messenger_status' => 'active',
            'last_activity_at' => now()
        ]);

        // Если есть начальное сообщение, создаем его
        if ($request->filled('message')) {
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'content' => $request->message,
                'type' => 'text',
                'direction' => 'out'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Чат создан успешно',
            'chat_id' => $chat->id
        ]);
    }

    /**
     * Получить сообщения чата
     */
    public function getMessages(Request $request, $chatId)
    {
        try {
            $user = Auth::user();
            $lastMessageId = $request->get('last_id') ?: $request->get('last_message_id');
            \Log::info('getMessages вызван', ['chatId' => $chatId, 'lastMessageId' => $lastMessageId]);
            
            $chat = Chat::where('is_messenger_chat', true)->findOrFail($chatId);
            \Log::info('Чат найден', ['chat_id' => $chat->id, 'title' => $chat->title]);

            // Проверяем доступ к чату
            if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
                abort(403, 'Доступ запрещен. Этот чат не принадлежит вашему отделу.');
            }
            
            // Если пользователь не руководитель, проверяем назначение
            if ($user->role !== 'admin' && !$this->isManager($user) && $chat->assigned_to !== $user->id) {
                abort(403, 'Доступ запрещен. Этот чат не назначен вам.');
            }
        
            $query = $chat->messages()->orderBy('created_at', 'asc');
            
            // Если передан ID последнего сообщения, получаем только новые
            if ($lastMessageId) {
                $query->where('id', '>', $lastMessageId);
            }
            
            $messages = $query->get(['id', 'content', 'created_at', 'type', 'user_id', 'metadata']);
            
            \Log::info('Сообщения получены', ['count' => $messages->count(), 'lastMessageId' => $lastMessageId]);
            
            $formattedMessages = $messages->map(function ($message) {
                $isFromClient = false;
                $senderName = 'Система';
                
                // Простая проверка metadata
                if ($message->metadata) {
                    if (isset($message->metadata['direction']) && $message->metadata['direction'] === 'incoming') {
                        $isFromClient = true;
                        $senderName = 'Клиент';
                    } elseif (isset($message->metadata['manager_name'])) {
                        // Для сообщений от менеджера используем его имя
                        $senderName = $message->metadata['manager_name'];
                    }
                }
                
                // Для сообщений от менеджера убираем **Имя** из содержимого
                $cleanedContent = $this->cleanForJson($message->content);
                if ($message->metadata && isset($message->metadata['manager_name']) && isset($message->metadata['original_message'])) {
                    // Используем оригинальное сообщение без **Имя**
                    $cleanedContent = $this->cleanForJson($message->metadata['original_message']);
                }
                
                // Логируем системные сообщения для отладки
                if (!$isFromClient) {
                    \Log::info('Системное сообщение для JSON:', [
                        'original' => $message->content,
                        'cleaned' => $cleanedContent,
                        'length' => strlen($cleanedContent)
                    ]);
                }
                
                return [
                    'id' => $message->id,
                    'content' => $cleanedContent,
                    'created_at' => $message->created_at,
                    'is_from_client' => $isFromClient,
                    'sender_name' => $senderName,
                    'sender_avatar' => $isFromClient ? 'К' : 'М',
                    'type' => $message->type,
                    'user_id' => $message->user_id
                ];
            });
            
            \Log::info('Сообщения отформатированы');
        
            // Определяем количество непрочитанных сообщений
            $unreadCount = 0;
            if ($chat->assigned_to === $user->id) {
                // Если чат назначен пользователю, считаем все прочитанными
                $unreadCount = 0;
            } elseif ($user->role === 'admin' || $this->isManager($user)) {
                // Админы и руководители видят все как прочитанные
                $unreadCount = 0;
            } else {
                // Для обычных сотрудников считаем непрочитанными сообщения от клиентов
                $unreadCount = $chat->messages()
                    ->where('metadata->direction', 'incoming')
                    ->where('created_at', '>', now()->subDays(1)) // Только за последний день
                    ->count();
            }

            return response()->json([
                'success' => true,
                'messages' => $formattedMessages,
                'last_message_id' => $formattedMessages->last() ? $formattedMessages->last()['id'] : $lastMessageId,
                'unread_count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка в getMessages: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка получения сообщений: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(Request $request, $chatId)
    {
        try {
            // Очищаем текст от некорректных UTF-8 символов
            $cleanContent = mb_convert_encoding($request->content, 'UTF-8', 'UTF-8');
            $cleanContent = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $cleanContent); // Удаляем управляющие символы, но сохраняем переносы строк
            
            \Log::info('sendMessage вызван', ['chatId' => $chatId, 'content' => $cleanContent]);
            
            $request->validate([
                'content' => 'required|string|max:1000'
            ]);
            
            // Дополнительная проверка на корректность UTF-8
            if (!mb_check_encoding($cleanContent, 'UTF-8')) {
                throw new \Exception('Некорректная кодировка текста');
            }

            $chat = Chat::where('is_messenger_chat', true)->findOrFail($chatId);
            $user = Auth::user();
            
            \Log::info('Чат и пользователь найдены', ['chat_id' => $chat->id, 'user_id' => $user->id]);

            // Проверяем доступ к чату
            if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
                abort(403, 'Доступ запрещен. Этот чат не принадлежит вашему отделу.');
            }
            
            // Если пользователь не руководитель, проверяем назначение
            if ($user->role !== 'admin' && !$this->isManager($user) && $chat->assigned_to !== $user->id) {
                abort(403, 'Доступ запрещен. Этот чат не назначен вам.');
            }

        // Отправляем сообщение через MessengerService
        $messengerService = app('\App\Services\MessengerService');
        $message = $messengerService->sendManagerMessage($chat, $cleanContent, $user);

        \Log::info('Сообщение создано', ['message_id' => $message->id, 'content' => $message->content]);

        // Очищаем данные перед отправкой JSON
        $responseData = [
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $this->cleanForJson($cleanContent), // Используем оригинальное сообщение
                'created_at' => $message->created_at,
                'is_from_client' => false,
                'sender_name' => $this->cleanForJson($user->name),
                'sender_avatar' => strtoupper(substr($this->cleanForJson($user->name), 0, 1)),
                'type' => $message->type,
                'user_id' => $message->user_id
            ]
        ];

        return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Ошибка в sendMessage: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка отправки сообщения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Проверяет, является ли пользователь руководителем
     */
    private function isManager($user)
    {
        // Проверяем роль пользователя
        if ($user->role === 'admin' || $user->role === 'manager') {
            return true;
        }
        
        // Проверяем должность
        return $user->position && (
            strpos(strtolower($user->position), 'руководитель') !== false ||
            strpos(strtolower($user->position), 'менеджер') !== false
        );
    }

    /**
     * Очистка текста для JSON
     */
    private function cleanForJson($text)
    {
        if (empty($text)) {
            return '';
        }
        
        // Конвертируем в UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Удаляем управляющие символы, но сохраняем переносы строк (\n, \r)
        $text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Удаляем UTF-8 replacement characters
        $text = str_replace("\u{FFFD}", '', $text);
        
        // Дополнительная проверка
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }
        
        return $text;
    }

    /**
     * Завершить чат
     */
    public function endChat(Request $request, $chatId)
    {
        try {
            $user = Auth::user();
            
            $chat = Chat::where('is_messenger_chat', true)->findOrFail($chatId);
            
            // Проверяем доступ к чату
            if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
                abort(403, 'Доступ запрещен. Этот чат не принадлежит вашему отделу.');
            }
            
            // Если пользователь не руководитель, проверяем назначение
            if ($user->role !== 'admin' && !$this->isManager($user) && $chat->assigned_to !== $user->id) {
                abort(403, 'Доступ запрещен. Этот чат не назначен вам.');
            }
            
            // Обновляем статус чата на завершенный
            $chat->update([
                'messenger_status' => 'completed',
                'closed_at' => now()
            ]);
            
            // Логируем завершение чата
            $historyService = app(ChatHistoryService::class);
            $historyService->logChatCompletion($chat, $user);
            
            return response()->json([
                'success' => true,
                'message' => 'Чат успешно завершен'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка в endChat: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка завершения чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Переключить чат в другой отдел
     */
    public function transferChat(Request $request, $chatId)
    {
        try {
            $user = Auth::user();
            $chat = Chat::where('is_messenger_chat', true)->findOrFail($chatId);
            
            // Проверяем доступ к чату
            if ($user->role !== 'admin' && $chat->department_id !== $user->department_id) {
                abort(403, 'Доступ запрещен. Этот чат не принадлежит вашему отделу.');
            }
            
            // Если пользователь не руководитель, проверяем назначение
            if ($user->role !== 'admin' && !$this->isManager($user) && $chat->assigned_to !== $user->id) {
                abort(403, 'Доступ запрещен. Этот чат не назначен вам.');
            }
            
            $departmentId = $request->input('department_id');
            
            // Обновляем отдел чата
            $chat->update([
                'department_id' => $departmentId,
                'assigned_to' => null, // Сбрасываем назначение менеджера
                'messenger_status' => $departmentId ? 'department_selected' : 'menu'
            ]);
            
            // Логируем переключение отдела
            if ($departmentId) {
                $department = \App\Models\Department::find($departmentId);
                $historyService = app(ChatHistoryService::class);
                $historyService->logDepartmentSelection($chat, $department, $user);
            } else {
                $historyService = app(ChatHistoryService::class);
                $historyService->logChatReset($chat, $user);
            }
            
            return response()->json([
                'success' => true,
                'message' => $departmentId ? 'Чат успешно переведен в отдел' : 'Назначение отдела сброшено'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка переключения чата: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'user_id' => Auth::id(),
                'department_id' => $request->input('department_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при переключении чата: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить сообщение
     */
    public function deleteMessage(Request $request, $messageId)
    {
        try {
            $message = Message::findOrFail($messageId);
            $user = Auth::user();
            
            // Проверяем права на удаление (только свои сообщения или админ)
            if ($message->user_id !== $user->id && !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Нет прав на удаление этого сообщения'
                ], 403);
            }
            
            // Если сообщение было отправлено через Wazzup24, удаляем и там
            if ($message->wazzup_message_id && class_exists('\App\Services\Wazzup24Service')) {
                try {
                    $wazzupService = app('\App\Services\Wazzup24Service');
                    $wazzupService->deleteMessage($message->wazzup_message_id);
                    \Log::info("Сообщение удалено из Wazzup24", ['message_id' => $message->wazzup_message_id]);
                } catch (\Exception $e) {
                    \Log::error("Ошибка удаления из Wazzup24: " . $e->getMessage());
                }
            }
            
            // Удаляем сообщение из базы данных
            $message->delete();
            
            \Log::info("Сообщение удалено", ['message_id' => $messageId, 'user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Сообщение удалено'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка удаления сообщения: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка удаления сообщения: ' . $e->getMessage()
            ], 500);
        }
    }
}
