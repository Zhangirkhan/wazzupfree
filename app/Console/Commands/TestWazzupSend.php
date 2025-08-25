<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\User;
use App\Services\MessengerService;

class TestWazzupSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:wazzup-send {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование отправки сообщения через Wazzup24';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = $this->argument('message');
        
        $this->info('Тестирование отправки сообщения через Wazzup24...');
        $this->info("Сообщение: {$message}");
        
        // Получаем чат с ID 1
        $chat = Chat::find(1);
        if (!$chat) {
            $this->error('Чат с ID 1 не найден');
            return 1;
        }
        
        $this->info("Чат найден: {$chat->title}");
        $this->info("Wazzup Chat ID: {$chat->wazzup_chat_id}");
        $this->info("Телефон: {$chat->messenger_phone}");
        
        // Получаем пользователя
        $user = User::find(1);
        if (!$user) {
            $this->error('Пользователь с ID 1 не найден');
            return 1;
        }
        
        $this->info("Пользователь: {$user->name}");
        
        try {
            // Отправляем сообщение через MessengerService
            $messengerService = app(MessengerService::class);
            $messengerService->sendManagerMessage($chat, $message, $user);
            
            $this->info('Сообщение отправлено успешно!');
            $this->info('Проверьте WhatsApp клиента.');
            
        } catch (\Exception $e) {
            $this->error('Ошибка отправки: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
