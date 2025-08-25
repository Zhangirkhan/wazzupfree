<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Client;
use App\Models\Message;

class AddTestMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:add-test {chat_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет тестовые сообщения в чат';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chatId = $this->argument('chat_id');
        
        $chat = Chat::find($chatId);
        if (!$chat) {
            $this->error("Чат с ID {$chatId} не найден");
            return 1;
        }

        // Находим или создаем клиента
        $client = Client::where('phone', $chat->messenger_phone)->first();
        if (!$client) {
            $client = Client::create([
                'name' => 'Клиент ' . $chat->messenger_phone,
                'phone' => $chat->messenger_phone,
                'is_active' => true
            ]);
            $this->info("Создан клиент: {$client->name}");
        }

        // Добавляем тестовые сообщения
        $messages = [
            [
                'user_id' => $client->id,
                'content' => 'Здравствуйте! У меня проблема с компьютером',
                'type' => 'text'
            ],
            [
                'user_id' => 1, // Системный пользователь
                'content' => 'Добрый день! Расскажите подробнее о проблеме',
                'type' => 'system'
            ],
            [
                'user_id' => $client->id,
                'content' => 'Компьютер не включается, черный экран',
                'type' => 'text'
            ],
            [
                'user_id' => 1,
                'content' => 'Понятно. Попробуйте проверить подключение питания и монитора',
                'type' => 'system'
            ]
        ];

        foreach ($messages as $messageData) {
            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $messageData['user_id'],
                'content' => $messageData['content'],
                'type' => $messageData['type']
            ]);
        }

        $this->info("Добавлено " . count($messages) . " тестовых сообщений в чат {$chat->id}");
        
        return 0;
    }
}
