<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;

class TestClientContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:client-contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование сохранения контактов клиента при первом сообщении';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Тестирование сохранения контактов клиента...');

        // Симулируем данные из webhook Wazzup24
        $phone = '77476644108';
        $message = 'Привет! Это мое первое сообщение.';
        $contactData = [
            'name' => 'Нургалиев Жангирхан',
            'avatarUri' => 'https://store.wazzup24.com/7868488bd6abe2f83f8be22b1ac4200333ee12db'
        ];

        $this->info("Телефон: {$phone}");
        $this->info("Сообщение: {$message}");
        $this->info("Имя: {$contactData['name']}");
        $this->info("Аватар: {$contactData['avatarUri']}");

        try {
            // Используем MessengerService для обработки сообщения
            $messengerService = app('\App\Services\MessengerService');
            $result = $messengerService->handleIncomingMessage($phone, $message, $contactData);

            if ($result['success']) {
                $this->info('✅ Сообщение обработано успешно!');
                $this->info("Chat ID: {$result['chat_id']}");
                $this->info("Message ID: {$result['message_id']}");
                
                // Проверяем, что клиент создан с правильными данными
                $client = \App\Models\Client::where('phone', $phone)->first();
                if ($client) {
                    $this->info('✅ Клиент создан:');
                    $this->info("  - ID: {$client->id}");
                    $this->info("  - Имя: {$client->name}");
                    $this->info("  - Телефон: {$client->phone}");
                    $this->info("  - Аватар: {$client->avatar}");
                } else {
                    $this->error('❌ Клиент не найден!');
                }
            } else {
                $this->error('❌ Ошибка обработки: ' . ($result['error'] ?? 'Неизвестная ошибка'));
            }

        } catch (\Exception $e) {
            $this->error('❌ Исключение: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        $this->info('Тестирование завершено.');
    }
}
