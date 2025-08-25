<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;

class AddTestMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:add-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавить тестовое сообщение от клиента';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Создаю тестовый чат с сообщением от клиента...');

        // Создаем тестового клиента
        $client = Client::firstOrCreate(
            ['phone' => '+7 (999) 123-45-67'],
            [
                'name' => 'Иван Петров',
                'email' => 'ivan@example.com'
            ]
        );

        // Создаем чат
        $chat = Chat::create([
            'organization_id' => 1,
            'title' => 'Чат с ' . $client->name,
            'description' => 'Тестовый чат',
            'type' => 'private',
            'created_by' => 1,
            'assigned_to' => 1,
            'status' => 'active',
            'phone' => $client->phone,
            'messenger_phone' => $client->phone,
            'is_messenger_chat' => true,
            'messenger_status' => 'active',
            'last_activity_at' => now()
        ]);

        // Добавляем сообщение от клиента
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // Используем ID 1 для клиентских сообщений
            'content' => 'Добрый день.',
            'type' => 'text',
            'metadata' => [
                'direction' => 'incoming',
                'client_name' => $client->name,
                'original_message' => 'Добрый день.',
                'is_client_message' => true
            ]
        ]);

        $this->info("Создан чат ID: {$chat->id}");
        $this->info("Добавлено сообщение от клиента: 'Добрый день.'");
        $this->info("Перейдите по ссылке: http://127.0.0.1:8001/user/chat?chat={$chat->id}");
    }
}
