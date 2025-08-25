<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;
use App\Models\Chat;
use App\Models\Client;
use App\Models\Message;

class TestMessengerSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messenger:test {phone} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует систему сохранения сообщений мессенджера';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Привет';

        $this->info("Тестирование системы мессенджера для номера: {$phone}");
        $this->info("Сообщение: {$message}");

        // Создаем экземпляр сервиса
        $messengerService = app(MessengerService::class);

        try {
            // Обрабатываем сообщение
            $result = $messengerService->handleIncomingMessage($phone, $message);

            // Получаем информацию о клиенте и чате
            $client = Client::where('phone', $phone)->first();
            $chat = Chat::where('messenger_phone', $phone)->first();

            if ($client && $chat) {
                $this->info("✅ Клиент найден/создан:");
                $this->info("   ID: {$client->id}");
                $this->info("   Имя: {$client->name}");
                $this->info("   Телефон: {$client->phone}");

                $this->info("✅ Чат найден/создан:");
                $this->info("   ID: {$chat->id}");
                $this->info("   Статус: {$chat->messenger_status}");
                $this->info("   Отдел: " . ($chat->department_id ?: 'Не назначен'));
                $this->info("   Назначен: " . ($chat->assigned_to ?: 'Не назначен'));

                // Показываем последние сообщения
                $messages = Message::where('chat_id', $chat->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $this->info("✅ Последние сообщения в чате:");
                foreach ($messages as $msg) {
                    $this->info("   [{$msg->created_at->format('H:i:s')}] {$msg->content}");
                }

                $this->info("✅ Тест завершен успешно!");
            } else {
                $this->error("❌ Ошибка: клиент или чат не найден");
            }

        } catch (\Exception $e) {
            $this->error("❌ Ошибка: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
