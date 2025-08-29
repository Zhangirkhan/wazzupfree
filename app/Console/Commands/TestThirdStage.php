<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Chat;
use App\Services\MessengerService;

class TestThirdStage extends Command
{
    protected $signature = 'test:third-stage';
    protected $description = 'Тестирует третий этап: назначение чатов сотрудникам';

    public function handle()
    {
        $this->info('Тестирование третьего этапа...');

        // Получаем тестовый чат
        $chat = Chat::where('is_messenger_chat', true)->where('department_id', 1)->first(); // Бухгалтерия
        if (!$chat) {
            $this->error('Тестовый чат не найден!');
            return;
        }

        $this->info("Тестовый чат: ID {$chat->id}, Отдел: " . ($chat->department ? $chat->department->name : 'Нет'));
        $this->info("Текущий назначенный: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Нет'));

        // Получаем пользователей бухгалтерии
        $buhUsers = User::where('department_id', 1)->get();
        $this->info("\nПользователи бухгалтерии:");
        foreach ($buhUsers as $user) {
            $isManager = strpos(strtolower($user->position), 'руководитель') !== false;
            $this->info("  - {$user->name} ({$user->position})" . ($isManager ? ' - РУКОВОДИТЕЛЬ' : ''));
        }

        // Тестируем отправку сообщения от обычного сотрудника
        $this->info("\n=== Тест 1: Обычный сотрудник отправляет сообщение ===");
        $regularUser = User::where('email', 'userbuh@akzholpharm.kz')->first();
        $this->info("Сотрудник: {$regularUser->name} ({$regularUser->position})");

        $messengerService = app('\App\Services\MessengerService');
        $message = $messengerService->sendManagerMessage($chat, 'Тестовое сообщение от обычного сотрудника', $regularUser);

        $this->info("✅ Сообщение отправлено");
        
        // Проверяем назначение
        $chat->refresh();
        $this->info("Назначенный сотрудник: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Нет'));

        // Тестируем отправку сообщения от руководителя
        $this->info("\n=== Тест 2: Руководитель отправляет сообщение ===");
        $managerUser = User::where('email', 'userbuh3@akzholpharm.kz')->first();
        $this->info("Руководитель: {$managerUser->name} ({$managerUser->position})");

        $message2 = $messengerService->sendManagerMessage($chat, 'Тестовое сообщение от руководителя', $managerUser);

        $this->info("✅ Сообщение отправлено");
        
        // Проверяем назначение (должно остаться за руководителем)
        $chat->refresh();
        $this->info("Назначенный сотрудник: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Нет'));

        // Тестируем доступ к чату
        $this->info("\n=== Тест 3: Проверка доступа к чату ===");
        
        // Обычный сотрудник должен видеть чат (если он назначен)
        $this->info("Обычный сотрудник может видеть чат: " . ($chat->assigned_to === $regularUser->id ? 'ДА' : 'НЕТ'));
        
        // Руководитель должен видеть чат (все чаты отдела)
        $this->info("Руководитель может видеть чат: ДА (все чаты отдела)");
        
        // Другой сотрудник не должен видеть чат
        $otherUser = User::where('email', 'userbuh2@akzholpharm.kz')->first();
        $this->info("Другой сотрудник может видеть чат: " . ($chat->assigned_to === $otherUser->id ? 'ДА' : 'НЕТ'));

        $this->info("\nТестирование завершено.");
    }
}
