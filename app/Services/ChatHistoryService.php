<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatHistory;
use App\Models\User;
use App\Models\Department;

class ChatHistoryService
{
    /**
     * Записать выбор отдела
     */
    public function logDepartmentSelection(Chat $chat, Department $department, ?User $user = null)
    {
        return ChatHistory::create([
            'chat_id' => $chat->id,
            'action' => 'department_selected',
            'description' => "Выбран отдел: {$department->name}",
            'user_id' => $user?->id,
            'department_id' => $department->id,
            'metadata' => [
                'client_phone' => $chat->messenger_phone,
                'client_name' => $chat->client?->name
            ]
        ]);
    }

    /**
     * Записать назначение менеджера
     */
    public function logManagerAssignment(Chat $chat, User $manager)
    {
        return ChatHistory::create([
            'chat_id' => $chat->id,
            'action' => 'assigned_to',
            'description' => "Назначен менеджер: {$manager->name}",
            'user_id' => $manager->id,
            'department_id' => $chat->department_id,
            'metadata' => [
                'client_phone' => $chat->messenger_phone,
                'client_name' => $chat->client?->name,
                'manager_name' => $manager->name
            ]
        ]);
    }

    /**
     * Записать завершение чата
     */
    public function logChatCompletion(Chat $chat, User $user)
    {
        return ChatHistory::create([
            'chat_id' => $chat->id,
            'action' => 'completed',
            'description' => "Чат завершен",
            'user_id' => $user->id,
            'department_id' => $chat->department_id,
            'metadata' => [
                'client_phone' => $chat->messenger_phone,
                'client_name' => $chat->client?->name,
                'completed_by' => $user->name
            ]
        ]);
    }

    /**
     * Записать сброс чата
     */
    public function logChatReset(Chat $chat, ?User $user = null)
    {
        return ChatHistory::create([
            'chat_id' => $chat->id,
            'action' => 'reset',
            'description' => "Чат сброшен к меню",
            'user_id' => $user?->id,
            'department_id' => null,
            'metadata' => [
                'client_phone' => $chat->messenger_phone,
                'client_name' => $chat->client?->name,
                'previous_department' => $chat->department?->name,
                'previous_manager' => $chat->assignedTo?->name
            ]
        ]);
    }

    /**
     * Получить историю чата
     */
    public function getChatHistory(Chat $chat)
    {
        return $chat->history()
            ->with(['user', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
