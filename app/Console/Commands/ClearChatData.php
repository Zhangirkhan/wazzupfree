<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClearChatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:clear {--force : Без подтверждения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка всех данных, связанных с чатами (сообщения, участники, клиенты)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Вы уверены, что хотите удалить ВСЕ данные чатов? Это действие нельзя отменить!')) {
                $this->info('Операция отменена.');
                return 0;
            }
        }

        $this->info('Начинаю очистку данных чатов...');

        try {
            DB::beginTransaction();

            // Очищаем сообщения
            $messageCount = Message::count();
            Message::truncate();
            $this->info("Удалено сообщений: {$messageCount}");

            // Очищаем участников чатов
            $participantCount = ChatParticipant::count();
            ChatParticipant::truncate();
            $this->info("Удалено участников чатов: {$participantCount}");

            // Очищаем чаты
            $chatCount = Chat::count();
            Chat::truncate();
            $this->info("Удалено чатов: {$chatCount}");

            // Очищаем клиентов
            $clientCount = Client::count();
            Client::truncate();
            $this->info("Удалено клиентов: {$clientCount}");

            // Сбрасываем автоинкремент для PostgreSQL
            DB::statement("SELECT setval('messages_id_seq', 1, false)");
            DB::statement("SELECT setval('chat_participants_id_seq', 1, false)");
            DB::statement("SELECT setval('chats_id_seq', 1, false)");
            DB::statement("SELECT setval('clients_id_seq', 1, false)");

            DB::commit();

            $this->info('✅ Очистка завершена успешно!');
            $this->info('Все данные чатов удалены. Система готова для тестирования.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Ошибка при очистке данных: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
