<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\User;
use App\Services\MessengerService;

class TestSendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send {chat_id} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет тестовое сообщение в чат';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chatId = $this->argument('chat_id');
        $message = $this->argument('message');
        
        $chat = Chat::find($chatId);
        if (!$chat) {
            $this->error("Чат с ID {$chatId} не найден");
            return 1;
        }
        
        $user = User::first();
        if (!$user) {
            $this->error("Пользователь не найден");
            return 1;
        }
        
        $this->info("Отправляем сообщение в чат {$chatId}:");
        $this->line("Чат: {$chat->messenger_phone}");
        $this->line("Сообщение: {$message}");
        $this->line("Отправитель: {$user->name}");
        
        try {
            $messengerService = app(MessengerService::class);
            $messengerService->sendManagerMessage($chat, $message, $user);
            
            $this->info("Сообщение отправлено успешно!");
            
            // Показываем обновленную информацию о чате
            $chat->refresh();
            $this->line("Последняя активность: " . $chat->last_activity_at->format('Y-m-d H:i:s'));
            $this->line("Всего сообщений: " . $chat->messages()->count());
            
        } catch (\Exception $e) {
            $this->error("Ошибка отправки: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
