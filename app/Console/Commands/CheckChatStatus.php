<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;

class CheckChatStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:check {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет статус чата по номеру телефона';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        
        $this->info("Проверяем чат для номера: {$phone}");
        
        // Находим чат
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();
        
        if (!$chat) {
            $this->error("Чат с номером {$phone} не найден");
            return 1;
        }
        
        $this->info("Чат найден:");
        $this->line("ID: {$chat->id}");
        $this->line("Статус: {$chat->messenger_status}");
        $this->line("Отдел: " . ($chat->department ? $chat->department->name : 'Не назначен'));
        $this->line("Назначен: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Не назначен'));
        $this->line("Последняя активность: " . ($chat->last_activity_at ? $chat->last_activity_at->format('Y-m-d H:i:s') : 'Нет'));
        $this->line("Wazzup Chat ID: " . ($chat->wazzup_chat_id ?: 'Не установлен'));
        
        // Находим клиента
        $client = Client::where('phone', $phone)->first();
        if ($client) {
            $this->info("Клиент: {$client->name}");
        } else {
            $this->warn("Клиент не найден в базе");
        }
        
        // Показываем сообщения
        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();
        $this->info("Сообщений в чате: " . $messages->count());
        
        if ($messages->count() > 0) {
            $this->line("\nПоследние сообщения:");
            foreach ($messages->take(10) as $message) {
                $sender = $message->user ? $message->user->name : 'Система';
                $time = $message->created_at->format('H:i:s');
                $this->line("[{$time}] {$sender}: {$message->content}");
            }
        }
        
        return 0;
    }
}
