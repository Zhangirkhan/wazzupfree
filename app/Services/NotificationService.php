<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendChatCreatedNotification(Chat $chat): void
    {
        try {
            // Логируем создание чата
            Log::info('Chat created', [
                'chat_id' => $chat->id,
                'title' => $chat->title,
                'client_id' => $chat->client_id,
                'created_by' => $chat->created_by
            ]);

            // Здесь можно добавить отправку email уведомлений
            // или push уведомлений

        } catch (\Exception $e) {
            Log::error('Failed to send chat created notification', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendChatAssignmentNotification(Chat $chat, User $assignedUser, User $assignedBy): void
    {
        try {
            Log::info('Chat assigned', [
                'chat_id' => $chat->id,
                'assigned_to' => $assignedUser->id,
                'assigned_by' => $assignedBy->id
            ]);

            // Здесь можно добавить отправку уведомлений

        } catch (\Exception $e) {
            Log::error('Failed to send chat assignment notification', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendMessageNotification(Chat $chat, User $sender): void
    {
        try {
            Log::info('Message sent', [
                'chat_id' => $chat->id,
                'sender_id' => $sender->id
            ]);

            // Здесь можно добавить отправку уведомлений

        } catch (\Exception $e) {
            Log::error('Failed to send message notification', [
                'chat_id' => $chat->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
