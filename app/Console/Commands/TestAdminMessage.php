<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\User;
use App\Services\MessengerService;

class TestAdminMessage extends Command
{
    protected $signature = 'test:admin-message';
    protected $description = 'Тестирование отправки сообщений из админки';

    public function handle()
    {
        $this->info('Тестирование отправки сообщений из админки...');

        // Находим пользователя
        $user = User::first();
        if (!$user) {
            $this->error('❌ Пользователи не найдены!');
            return;
        }

        $this->info("✅ Найден пользователь: {$user->name}");

        // Создаем тестовый чат
        $chat = Chat::create([
            'title' => 'Тестовый чат',
            'organization_id' => 1,
            'created_by' => $user->id,
            'is_messenger_chat' => true,
            'messenger_phone' => '77476644108',
            'messenger_status' => 'active',
            'last_activity_at' => now()
        ]);

        $this->info("✅ Создан чат ID: {$chat->id}");

        // Тестируем отправку сообщения
        $messengerService = app('\App\Services\MessengerService');
        
        $testMessages = [
            'Привет! Это тестовое сообщение.',
            'Сообщение с кириллицей: привет мир!',
            'Message with English: Hello World!',
            'Сообщение с символами: @#$%^&*()',
            'Сообщение с эмодзи: 😀👍🎉'
        ];

        foreach ($testMessages as $index => $message) {
            $this->info("Тест " . ($index + 1) . ": Отправка сообщения");
            $this->info("Текст: {$message}");
            
            try {
                $result = $messengerService->sendManagerMessage($chat, $message, $user);
                $this->info("✅ Сообщение отправлено успешно! ID: {$result->id}");
            } catch (\Exception $e) {
                $this->error("❌ Ошибка: " . $e->getMessage());
            }
            
            $this->info('---');
        }

        $this->info('Тестирование завершено.');
    }
}
