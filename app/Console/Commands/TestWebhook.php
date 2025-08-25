<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;

class TestWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование полного flow бота с меню и отделами';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Тестирование полного flow бота...');

        // Симулируем данные из webhook Wazzup24
        $phone = '77476644108';
        $contactData = [
            'name' => 'Нургалиев Жангирхан',
            'avatarUri' => 'https://store.wazzup24.com/7868488bd6abe2f83f8be22b1ac4200333ee12db'
        ];

        $messengerService = app('\App\Services\MessengerService');

        // Шаг 1: Первое сообщение от клиента
        $this->info('Шаг 1: Клиент отправляет первое сообщение');
        $message1 = 'Привет!';
        $result1 = $messengerService->handleIncomingMessage($phone, $message1, $contactData);
        $this->info("✅ Результат: " . ($result1['success'] ? 'Успешно' : 'Ошибка'));

        // Шаг 2: Клиент выбирает отдел
        $this->info('Шаг 2: Клиент выбирает отдел (1 - Бухгалтерия)');
        $message2 = '1';
        $result2 = $messengerService->handleIncomingMessage($phone, $message2, $contactData);
        $this->info("✅ Результат: " . ($result2['success'] ? 'Успешно' : 'Ошибка'));

        // Шаг 3: Клиент задает вопрос
        $this->info('Шаг 3: Клиент задает вопрос');
        $message3 = 'У меня вопрос по зарплате';
        $result3 = $messengerService->handleIncomingMessage($phone, $message3, $contactData);
        $this->info("✅ Результат: " . ($result3['success'] ? 'Успешно' : 'Ошибка'));

        $this->info('Тестирование завершено.');
    }
}
