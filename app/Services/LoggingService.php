<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LoggingService
{
    public function logApiRequest(Request $request, Response $response): void
    {
        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'status_code' => $response->getStatusCode(),
            'response_time' => microtime(true) - LARAVEL_START,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    public function logUserAction(string $action, array $data = []): void
    {
        Log::info('User Action', [
            'action' => $action,
            'user_id' => auth()->id(),
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function logError(string $message, array $context = []): void
    {
        Log::error($message, [
            'user_id' => auth()->id(),
            'context' => $context,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function logChatActivity(string $action, int $chatId, array $data = []): void
    {
        Log::info('Chat Activity', [
            'action' => $action,
            'chat_id' => $chatId,
            'user_id' => auth()->id(),
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}
