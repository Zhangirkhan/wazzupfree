<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем пользователей
        $admin = User::where('email', 'admin@chat.ap.kz')->first();
        $manager = User::where('email', 'manager@chat.ap.kz')->first();
        $leader = User::where('email', 'leader@chat.ap.kz')->first();

        if (!$admin || !$manager || !$leader) {
            $this->command->warn('Не найдены пользователи для создания тестовых чатов');
            return;
        }

        // Получаем первую организацию
        $organization = \App\Models\Organization::first();
        if (!$organization) {
            $this->command->warn('Не найдена организация для создания тестовых чатов');
            return;
        }

        // Создаем тестовые чаты
        $chats = [
            [
                'title' => 'Иван Петров',
                'phone' => '+7 777 123 4567',
                'organization_id' => $organization->id,
                'created_by' => $admin->id,
                'assigned_to' => $manager->id,
                'status' => 'active',
                'messenger_data' => ['email' => 'ivan.petrov@example.com'],
                'messages' => [
                    ['content' => 'Добрый день! Меня интересует ваша продукция', 'direction' => 'in'],
                    ['content' => 'Здравствуйте! Рад помочь вам с выбором продукции. Что именно вас интересует?', 'direction' => 'out'],
                    ['content' => 'Мне нужны консультации по ценам на оптовые закупки', 'direction' => 'in'],
                ]
            ],
            [
                'title' => 'Мария Сидорова',
                'phone' => '+7 777 234 5678',
                'organization_id' => $organization->id,
                'created_by' => $manager->id,
                'assigned_to' => $leader->id,
                'status' => 'active',
                'messenger_data' => ['email' => 'maria.sidorova@company.kz'],
                'messages' => [
                    ['content' => 'Здравствуйте! Когда будет готов заказ?', 'direction' => 'in'],
                    ['content' => 'Добрый день! Ваш заказ будет готов завтра к 15:00', 'direction' => 'out'],
                    ['content' => 'Отлично! Спасибо за информацию', 'direction' => 'in'],
                ]
            ],
            [
                'title' => 'Алексей Козлов',
                'phone' => '+7 777 345 6789',
                'organization_id' => $organization->id,
                'created_by' => $leader->id,
                'assigned_to' => $admin->id,
                'status' => 'active',
                'messenger_data' => ['email' => 'alexey.kozlov@business.kz'],
                'messages' => [
                    ['content' => 'Нужна помощь с техническими вопросами', 'direction' => 'in'],
                ]
            ],
            [
                'title' => 'Елена Волкова',
                'phone' => '+7 777 456 7890',
                'organization_id' => $organization->id,
                'created_by' => $admin->id,
                'assigned_to' => $manager->id,
                'status' => 'closed',
                'messenger_data' => ['email' => 'elena.volkova@email.kz'],
                'messages' => [
                    ['content' => 'Спасибо за отличное обслуживание!', 'direction' => 'in'],
                    ['content' => 'Пожалуйста! Рады были помочь', 'direction' => 'out'],
                ]
            ],
            [
                'title' => 'Дмитрий Новиков',
                'phone' => '+7 777 567 8901',
                'organization_id' => $organization->id,
                'created_by' => $manager->id,
                'assigned_to' => $leader->id,
                'status' => 'transferred',
                'messenger_data' => ['email' => 'dmitry.novikov@corp.kz'],
                'messages' => [
                    ['content' => 'У меня вопрос по договору', 'direction' => 'in'],
                    ['content' => 'Передаю ваш вопрос специалисту по договорам', 'direction' => 'out'],
                ]
            ],
        ];

        foreach ($chats as $chatData) {
            $messages = $chatData['messages'];
            unset($chatData['messages']);

            $chat = Chat::create($chatData);

            // Создаем сообщения для чата
            foreach ($messages as $messageData) {
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $messageData['direction'] === 'out' ? $chat->assigned_to : $chat->created_by,
                    'content' => $messageData['content'],
                    'type' => 'text',
                    'direction' => $messageData['direction'],
                    'created_at' => now()->subMinutes(rand(1, 1440)), // Случайное время в течение последних 24 часов
                ]);
            }

            $this->command->info("Создан чат: {$chat->title}");
        }

        $this->command->info('Тестовые чаты созданы успешно!');
    }
}
