<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;
use App\Models\ChatParticipant;
use App\Models\ChatHistory;
use App\Models\MessageRead;
use Illuminate\Support\Facades\DB;

class ClearAllChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:clear-all {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет все чаты, сообщения и связанные данные';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirm')) {
            $this->warn('⚠️  ВНИМАНИЕ: Эта команда удалит ВСЕ чаты, сообщения и связанные данные!');
            $this->line('Это действие нельзя отменить.');

            if (!$this->confirm('Вы уверены, что хотите продолжить?')) {
                $this->info('Операция отменена.');
                return 0;
            }
        }

        $this->info('🗑️  Полная очистка всех чатов и связанных данных...');

        try {
            DB::beginTransaction();

            // Подсчитываем количество данных
            $chatCount = Chat::count();
            $messageCount = Message::count();
            $clientCount = Client::count();
            $participantCount = ChatParticipant::count();
            $messageReadCount = MessageRead::count();
            $chatHistoryCount = ChatHistory::count();

            $this->line("Найдено чатов: {$chatCount}");
            $this->line("Найдено сообщений: {$messageCount}");
            $this->line("Найдено клиентов: {$clientCount}");
            $this->line("Найдено участников чатов: {$participantCount}");
            $this->line("Найдено отметок о прочтении: {$messageReadCount}");
            $this->line("Найдено записей истории чатов: {$chatHistoryCount}");

            // Удаляем в правильном порядке (сначала зависимые данные)
            MessageRead::truncate();
            $this->line("✓ Удалены отметки о прочтении сообщений");

            ChatHistory::truncate();
            $this->line("✓ Удалена история чатов");

            ChatParticipant::truncate();
            $this->line("✓ Удалены участники чатов");

            Message::truncate();
            $this->line("✓ Удалены сообщения");

            Chat::truncate();
            $this->line("✓ Удалены чаты");

            Client::truncate();
            $this->line("✓ Удалены клиенты");

            // Сбрасываем автоинкремент для PostgreSQL
            $this->info('Сбрасываем счетчики автоинкремента...');
            DB::statement("SELECT setval('message_reads_id_seq', 1, false)");
            DB::statement("SELECT setval('chat_history_id_seq', 1, false)");
            DB::statement("SELECT setval('chat_participants_id_seq', 1, false)");
            DB::statement("SELECT setval('messages_id_seq', 1, false)");
            DB::statement("SELECT setval('chats_id_seq', 1, false)");
            DB::statement("SELECT setval('clients_id_seq', 1, false)");

            DB::commit();

            $this->info('✅ Полная очистка завершена успешно!');
            $this->line('Удалены все чаты, сообщения, клиенты и связанные данные.');
            $this->line('Система готова для тестирования с чистого листа.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Ошибка при очистке данных: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
