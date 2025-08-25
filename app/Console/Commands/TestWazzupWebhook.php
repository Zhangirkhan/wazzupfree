<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WebhookController;

class TestWazzupWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:wazzup-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование данных вебхука Wazzup24';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Тестирование данных вебхука Wazzup24...');
        
        // Пример данных, которые приходят из Wazzup24
        $webhookData = [
            'messages' => [
                [
                    'messageId' => 'test_message_123',
                    'channelId' => config('wazzup24.api.channel_id'),
                    'chatType' => 'whatsapp',
                    'chatId' => '77476644108',
                    'text' => 'Привет! Это тестовое сообщение от клиента.',
                    'status' => 'inbound',
                    'authorName' => 'Клиент',
                    'dateTime' => now()->toISOString()
                ]
            ],
            'statuses' => [
                [
                    'messageId' => 'test_message_123',
                    'status' => 'delivered'
                ]
            ]
        ];
        
        $this->info('Пример данных вебхука:');
        $this->line(json_encode($webhookData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info('');
        $this->info('Структура данных:');
        $this->line('messages[] - массив входящих сообщений');
        $this->line('  ├── messageId - уникальный ID сообщения');
        $this->line('  ├── channelId - ID канала');
        $this->line('  ├── chatType - тип чата (whatsapp, telegram, etc.)');
        $this->line('  ├── chatId - ID чата (для WhatsApp это номер телефона)');
        $this->line('  ├── text - текст сообщения');
        $this->line('  ├── status - статус (inbound = входящее)');
        $this->line('  ├── authorName - имя автора');
        $this->line('  └── dateTime - время отправки');
        $this->line('');
        $this->line('statuses[] - массив статусов сообщений');
        $this->line('  ├── messageId - ID сообщения');
        $this->line('  └── status - статус (sent, delivered, read, failed)');
        
        return 0;
    }
}
