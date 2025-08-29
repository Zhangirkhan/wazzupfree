<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatHistoryController extends Controller
{
    /**
     * Получить историю чата
     */
    public function getHistory(Request $request, $chatId)
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
            
            $historyService = app(ChatHistoryService::class);
            $history = $historyService->getChatHistory($chat);
            
            $formattedHistory = $history->map(function($item) {
                return [
                    'id' => $item->id,
                    'action' => $item->action,
                    'description' => $item->description,
                    'user_name' => $item->user?->name ?? 'Система',
                    'department_name' => $item->department?->name ?? null,
                    'created_at' => $item->created_at->format('d.m.Y H:i:s'),
                    'metadata' => $item->metadata
                ];
            });
            
            return response()->json([
                'success' => true,
                'history' => $formattedHistory
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка получения истории: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка получения истории: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Проверка, является ли пользователь менеджером
     */
    private function isManager($user)
    {
        return $user->role === 'admin' || $user->role === 'manager' || $user->position === 'руководитель';
    }
}
