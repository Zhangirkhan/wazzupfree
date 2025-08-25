<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;

class TestWebhookMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:test {phone} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует обработку webhook сообщения';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message');
        
        $this->info("Тестируем обработку сообщения:");
        $this->line("Телефон: {$phone}");
        $this->line("Сообщение: {$message}");
        
        try {
            $messengerService = app(MessengerService::class);
            $result = $messengerService->handleIncomingMessage($phone, $message, 'test-message-id');
            
            $this->info("Результат обработки:");
            $this->line("Успех: " . ($result['success'] ? 'Да' : 'Нет'));
            if (isset($result['chat_id'])) {
                $this->line("ID чата: {$result['chat_id']}");
            }
            if (isset($result['message_id'])) {
                $this->line("ID сообщения: {$result['message_id']}");
            }
            if (isset($result['error'])) {
                $this->error("Ошибка: {$result['error']}");
            }
            
        } catch (\Exception $e) {
            $this->error("Исключение: " . $e->getMessage());
            $this->line("Файл: " . $e->getFile() . ":" . $e->getLine());
        }
        
        return 0;
    }
}
