<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;

class CheckWebhookProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:check {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет обработку webhook сообщений';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        
        $this->info("Проверяем обработку webhook для номера: {$phone}");
        
        // Проверяем чат
        $chat = Chat::where('messenger_phone', $phone)
                   ->where('is_messenger_chat', true)
                   ->first();
        
        if (!$chat) {
            $this->error("Чат не найден - возможно, webhook не обработался");
            return 1;
        }
        
        $this->info("Чат найден:");
        $this->line("ID: {$chat->id}");
        $this->line("Статус: {$chat->messenger_status}");
        $this->line("Wazzup Chat ID: " . ($chat->wazzup_chat_id ?: 'Не установлен'));
        
        // Проверяем клиента
        $client = Client::where('phone', $phone)->first();
        if ($client) {
            $this->info("Клиент: {$client->name}");
        } else {
            $this->warn("Клиент не найден");
        }
        
        // Проверяем сообщения
        $messages = $chat->messages()->with('user')->orderBy('created_at', 'desc')->get();
        $this->info("Сообщений в чате: " . $messages->count());
        
        if ($messages->count() > 0) {
            $this->line("\nПоследние 5 сообщений:");
            foreach ($messages->take(5) as $message) {
                $sender = $message->user ? $message->user->name : 'Система';
                $time = $message->created_at->format('H:i:s');
                $type = $message->type;
                $this->line("[{$time}] {$sender} ({$type}): {$message->content}");
            }
        }
        
        // Проверяем логи
        $this->info("\nПроверяем логи Laravel...");
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $webhookLines = array_filter(explode("\n", $logContent), function($line) use ($phone) {
                return strpos($line, $phone) !== false && strpos($line, 'WEBHOOK') !== false;
            });
            
            if (!empty($webhookLines)) {
                $this->info("Найдены записи в логах:");
                foreach (array_slice($webhookLines, -3) as $line) {
                    $this->line($line);
                }
            } else {
                $this->warn("Записи о webhook не найдены в логах");
            }
        }
        
        return 0;
    }
}
