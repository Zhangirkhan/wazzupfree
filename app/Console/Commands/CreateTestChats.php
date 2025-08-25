<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use Carbon\Carbon;

class CreateTestChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает тестовые чаты для демонстрации';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Создание тестовых чатов...');

        // Получаем первого пользователя для назначения чатов
        $user = User::first();
        if (!$user) {
            $this->error('Пользователи не найдены. Создайте пользователя сначала.');
            return 1;
        }

        // Получаем или создаем организацию
        $organization = Organization::first();
        if (!$organization) {
            $organization = Organization::create([
                'name' => 'Тестовая организация',
                'description' => 'Организация для тестирования'
            ]);
        }

        // Получаем или создаем отдел
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Тестовый отдел',
                'description' => 'Отдел для тестирования',
                'organization_id' => $organization->id
            ]);
        }

        // Создаем тестовые чаты
        $testChats = [
            [
                'title' => 'Поддержка клиентов',
                'description' => 'Общие вопросы по поддержке клиентов',
                'type' => 'group',
                'phone' => '+7 777 123 45 67',
                'status' => 'active',
                'assigned_to' => $user->id,
                'organization_id' => $organization->id,
                'is_messenger_chat' => false,
                'last_activity_at' => Carbon::now()->subMinutes(5),
                'messages' => [
                    ['content' => 'Добрый день! У меня вопрос по заказу #12345', 'created_at' => Carbon::now()->subMinutes(30)],
                    ['content' => 'Здравствуйте! Конечно, помогу вам. Какой именно вопрос?', 'created_at' => Carbon::now()->subMinutes(25)],
                    ['content' => 'Хочу узнать статус доставки', 'created_at' => Carbon::now()->subMinutes(20)],
                    ['content' => 'Ваш заказ в пути, ожидаемая доставка завтра', 'created_at' => Carbon::now()->subMinutes(15)],
                    ['content' => 'Спасибо за информацию!', 'created_at' => Carbon::now()->subMinutes(10)],
                    ['content' => 'Пожалуйста! Если будут еще вопросы, обращайтесь', 'created_at' => Carbon::now()->subMinutes(5)]
                ]
            ],
            [
                'title' => 'Техническая поддержка',
                'description' => 'Вопросы по техническим проблемам',
                'type' => 'group',
                'phone' => '+7 777 234 56 78',
                'status' => 'active',
                'assigned_to' => $user->id,
                'organization_id' => $organization->id,
                'is_messenger_chat' => false,
                'last_activity_at' => Carbon::now()->subMinutes(15),
                'messages' => [
                    ['content' => 'Не могу войти в систему', 'created_at' => Carbon::now()->subMinutes(45)],
                    ['content' => 'Попробуйте очистить кэш браузера', 'created_at' => Carbon::now()->subMinutes(40)],
                    ['content' => 'Не помогло, все еще не работает', 'created_at' => Carbon::now()->subMinutes(35)],
                    ['content' => 'Проверьте, правильно ли вводите логин и пароль', 'created_at' => Carbon::now()->subMinutes(30)],
                    ['content' => 'Да, ввожу правильно', 'created_at' => Carbon::now()->subMinutes(25)],
                    ['content' => 'Тогда попробуйте сбросить пароль', 'created_at' => Carbon::now()->subMinutes(20)],
                    ['content' => 'Хорошо, попробую', 'created_at' => Carbon::now()->subMinutes(15)]
                ]
            ],
            [
                'title' => 'Продажи',
                'description' => 'Вопросы по продажам и заказам',
                'type' => 'private',
                'phone' => '+7 777 345 67 89',
                'status' => 'active',
                'assigned_to' => $user->id,
                'organization_id' => $organization->id,
                'is_messenger_chat' => false,
                'last_activity_at' => Carbon::now()->subMinutes(2),
                'messages' => [
                    ['content' => 'Интересует оптовая закупка', 'created_at' => Carbon::now()->subMinutes(60)],
                    ['content' => 'Здравствуйте! Расскажите подробнее о ваших потребностях', 'created_at' => Carbon::now()->subMinutes(55)],
                    ['content' => 'Нужно 100 единиц товара А', 'created_at' => Carbon::now()->subMinutes(50)],
                    ['content' => 'Отлично! У нас есть в наличии. Цена за единицу 1000 тенге', 'created_at' => Carbon::now()->subMinutes(45)],
                    ['content' => 'А есть ли скидка при оптовой закупке?', 'created_at' => Carbon::now()->subMinutes(40)],
                    ['content' => 'Да, при заказе от 50 единиц скидка 10%', 'created_at' => Carbon::now()->subMinutes(35)],
                    ['content' => 'Отлично! Тогда оформляю заказ', 'created_at' => Carbon::now()->subMinutes(30)],
                    ['content' => 'Хорошо, отправлю вам коммерческое предложение', 'created_at' => Carbon::now()->subMinutes(25)],
                    ['content' => 'Получил, спасибо!', 'created_at' => Carbon::now()->subMinutes(20)],
                    ['content' => 'Жду вашего решения', 'created_at' => Carbon::now()->subMinutes(15)],
                    ['content' => 'Согласен на условия', 'created_at' => Carbon::now()->subMinutes(10)],
                    ['content' => 'Отлично! Оформляю заказ', 'created_at' => Carbon::now()->subMinutes(5)],
                    ['content' => 'Заказ оформлен, номер #67890', 'created_at' => Carbon::now()->subMinutes(2)]
                ]
            ],
            [
                'title' => 'Бухгалтерия',
                'description' => 'Вопросы по документам и оплате',
                'type' => 'group',
                'phone' => '+7 777 456 78 90',
                'status' => 'active',
                'assigned_to' => $user->id,
                'organization_id' => $organization->id,
                'is_messenger_chat' => false,
                'last_activity_at' => Carbon::now()->subHours(1),
                'messages' => [
                    ['content' => 'Нужен акт выполненных работ', 'created_at' => Carbon::now()->subHours(2)],
                    ['content' => 'Конечно, подготовлю сегодня', 'created_at' => Carbon::now()->subHours(1)->subMinutes(55)],
                    ['content' => 'Спасибо!', 'created_at' => Carbon::now()->subHours(1)]
                ]
            ],
            [
                'title' => 'Склад',
                'description' => 'Вопросы по наличию товаров',
                'type' => 'group',
                'phone' => '+7 777 567 89 01',
                'status' => 'active',
                'assigned_to' => $user->id,
                'organization_id' => $organization->id,
                'is_messenger_chat' => false,
                'last_activity_at' => Carbon::now()->subHours(3),
                'messages' => [
                    ['content' => 'Есть ли в наличии товар Б?', 'created_at' => Carbon::now()->subHours(4)],
                    ['content' => 'Проверяю...', 'created_at' => Carbon::now()->subHours(3)->subMinutes(55)],
                    ['content' => 'Да, есть 25 единиц', 'created_at' => Carbon::now()->subHours(3)]
                ]
            ]
        ];

        foreach ($testChats as $chatData) {
            $messages = $chatData['messages'];
            unset($chatData['messages']);

            // Создаем чат
            $chatData['created_by'] = $user->id;
            $chat = Chat::create($chatData);

            // Добавляем участника
            ChatParticipant::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Создаем сообщения
            foreach ($messages as $messageData) {
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'content' => $messageData['content'],
                    'type' => 'text',
                    'created_at' => $messageData['created_at'],
                    'updated_at' => $messageData['created_at'],
                ]);
            }

            $this->info("Создан чат: {$chat->title}");
        }

        $this->info('Тестовые чаты созданы успешно!');
        $this->info('Всего создано: ' . count($testChats) . ' чатов');
        
        return 0;
    }
}
