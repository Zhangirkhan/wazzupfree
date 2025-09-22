<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatStreamController extends Controller
{
    /**
     * Создать SSE поток для чата
     */
    public function stream(Request $request, $chatId)
    {
        // Проверяем авторизацию (из токена в URL или стандартным способом)
        $user = $request->user();

        // Если нет пользователя через стандартную авторизацию, пробуем токен из URL
        if (!$user && $request->get('token')) {
            try {
                $token = $request->get('token');

                // Пробуем найти пользователя по токену Sanctum
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                    Log::info('SSE connection with valid token', ['user_id' => $user->id]);
                } else {
                    Log::warning('SSE connection with invalid token', ['token' => substr($token, 0, 10) . '...']);
                    return response()->json(['error' => 'Invalid token'], 401);
                }
            } catch (\Exception $e) {
                Log::error('SSE token verification error', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Token verification failed'], 401);
            }
        }

        if (!$user) {
            return response()->json(['error' => 'Unauthorized - no valid user or token'], 401);
        }

        $userId = $user->id;

        Log::info('SSE connection started', [
            'user_id' => $userId,
            'chat_id' => $chatId
        ]);

        return response()->stream(
            function () use ($chatId, $userId) {
                // Устанавливаем заголовки SSE
                echo "retry: 3000\n";
                echo "data: " . json_encode([
                    'type' => 'connected',
                    'chatId' => $chatId,
                    'userId' => $userId,
                    'timestamp' => now()->toISOString()
                ]) . "\n\n";

                if (ob_get_level()) {
                    ob_end_flush();
                }
                flush();

                // Подписываемся на Redis канал чата
                Redis::subscribe(['chat.' . $chatId], function ($message) {
                    try {
                        $data = json_decode($message, true);

                        // Отправляем данные клиенту
                        echo "data: " . json_encode($data) . "\n\n";

                        if (ob_get_level()) {
                            ob_end_flush();
                        }
                        flush();

                    } catch (\Exception $e) {
                        Log::error('SSE message processing error', [
                            'error' => $e->getMessage(),
                            'message' => $message
                        ]);
                    }
                });
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Для nginx
                'Access-Control-Allow-Origin' => config('cors.allowed_origins')[0] ?? '*',
                'Access-Control-Allow-Credentials' => 'true',
            ]
        );
    }

    /**
     * Глобальный поток уведомлений для пользователя
     */
    public function notifications(Request $request)
    {
        // Проверяем авторизацию
        $user = $request->user();

        if (!$user && $request->get('token')) {
            try {
                $token = $request->get('token');
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                } else {
                    return response()->json(['error' => 'Invalid token'], 401);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Token verification failed'], 401);
            }
        }

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->id;

        Log::info('Global notifications SSE started', ['user_id' => $userId]);

        return response()->stream(
            function () use ($userId) {
                // Устанавливаем заголовки SSE
                echo "retry: 3000\n";
                echo "data: " . json_encode([
                    'type' => 'connected',
                    'userId' => $userId,
                    'timestamp' => now()->toISOString()
                ]) . "\n\n";

                if (ob_get_level()) {
                    ob_end_flush();
                }
                flush();

                // Подписываемся на глобальный канал пользователя
                Redis::subscribe(['user.' . $userId . '.notifications'], function ($message) {
                    try {
                        $data = json_decode($message, true);

                        // Отправляем данные клиенту
                        echo "data: " . json_encode($data) . "\n\n";

                        if (ob_get_level()) {
                            ob_end_flush();
                        }
                        flush();

                    } catch (\Exception $e) {
                        Log::error('Global notifications SSE error', [
                            'error' => $e->getMessage(),
                            'message' => $message
                        ]);
                    }
                });
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Access-Control-Allow-Origin' => config('cors.allowed_origins')[0] ?? '*',
                'Access-Control-Allow-Credentials' => 'true',
            ]
        );
    }

    /**
     * SSE поток для обновления списка чатов
     */
    public function chatsStream(Request $request)
    {
        // Проверяем авторизацию
        $user = $request->user();

        if (!$user && $request->get('token')) {
            try {
                $token = $request->get('token');
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                } else {
                    return response()->json(['error' => 'Invalid token'], 401);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Token verification failed'], 401);
            }
        }

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->id;
        $organizationId = $user->organization_id;

        Log::info('Chat list SSE started', [
            'user_id' => $userId,
            'organization_id' => $organizationId
        ]);

        return response()->stream(
            function () use ($userId, $organizationId) {
                // Устанавливаем заголовки SSE
                echo "retry: 3000\n";
                echo "data: " . json_encode([
                    'type' => 'connected',
                    'userId' => $userId,
                    'timestamp' => now()->toISOString()
                ]) . "\n\n";

                if (ob_get_level()) {
                    ob_end_flush();
                }
                flush();

                // Подписываемся на каналы обновлений чатов
                $channels = [
                    'chats.global', // Глобальные обновления чатов
                    'user.' . $userId . '.chats', // Персональные обновления чатов пользователя
                ];

                if ($organizationId) {
                    $channels[] = 'organization.' . $organizationId . '.chats'; // Обновления чатов организации
                }

                Redis::subscribe($channels, function ($message) {
                    try {
                        $data = json_decode($message, true);

                        // Отправляем данные клиенту
                        echo "data: " . json_encode($data) . "\n\n";

                        if (ob_get_level()) {
                            ob_end_flush();
                        }
                        flush();

                    } catch (\Exception $e) {
                        Log::error('Chat list SSE error', [
                            'error' => $e->getMessage(),
                            'message' => $message
                        ]);
                    }
                });
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Access-Control-Allow-Origin' => config('cors.allowed_origins')[0] ?? '*',
                'Access-Control-Allow-Credentials' => 'true',
            ]
        );
    }

    /**
     * Получить статус подключений к чату
     */
    public function status($chatId)
    {
        try {
            // Получаем количество подписчиков на канал
            $subscribers = Redis::pubsub('numsub', 'chat.' . $chatId);
            $count = $subscribers[1] ?? 0;

            return response()->json([
                'chat_id' => $chatId,
                'connected_users' => $count,
                'status' => 'active'
            ]);
        } catch (\Exception $e) {
            Log::error('Chat status error', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'chat_id' => $chatId,
                'connected_users' => 0,
                'status' => 'error'
            ]);
        }
    }
}
