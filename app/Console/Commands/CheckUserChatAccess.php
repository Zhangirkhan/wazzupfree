<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\User;

class CheckUserChatAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check-chats {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет доступ пользователя к чатам в user/chat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::first();
        }
        
        if (!$user) {
            $this->error("Пользователь не найден");
            return 1;
        }
        
        $this->info("Проверяем доступ для пользователя: {$user->name} (ID: {$user->id}, Роль: {$user->role})");
        
        // Получаем все мессенджер чаты (как в контроллере)
        $chats = Chat::query()
            ->where('is_messenger_chat', true)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'department', 'assignedTo'])
            ->orderBy('last_activity_at', 'desc')
            ->get();
        
        $this->info("Всего доступных чатов: " . $chats->count());
        
        foreach ($chats as $chat) {
            $this->line("\nЧат ID: {$chat->id}");
            $this->line("Телефон: {$chat->messenger_phone}");
            $this->line("Статус: {$chat->messenger_status}");
            $this->line("Отдел: " . ($chat->department ? $chat->department->name : 'Не назначен'));
            $this->line("Назначен: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Не назначен'));
            $this->line("Сообщений: " . $chat->messages()->count());
            $this->line("Последняя активность: " . ($chat->last_activity_at ? $chat->last_activity_at->format('Y-m-d H:i:s') : 'Нет'));
            
            if ($chat->messages->count() > 0) {
                $lastMessage = $chat->messages->first();
                $sender = $lastMessage->user ? $lastMessage->user->name : 'Система';
                $this->line("Последнее сообщение: {$sender}: " . substr($lastMessage->content, 0, 50) . "...");
            }
        }
        
        return 0;
    }
}
