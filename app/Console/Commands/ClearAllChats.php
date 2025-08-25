<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;
use App\Models\ChatParticipant;

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
        
        // Подсчитываем количество данных
        $chatCount = Chat::count();
        $messageCount = Message::count();
        $clientCount = Client::count();
        $participantCount = ChatParticipant::count();
        
        $this->line("Найдено чатов: {$chatCount}");
        $this->line("Найдено сообщений: {$messageCount}");
        $this->line("Найдено клиентов: {$clientCount}");
        $this->line("Найдено участников чатов: {$participantCount}");
        
        // Удаляем участников чатов
        ChatParticipant::truncate();
        $this->line("Удалено участников чатов");
        
        // Удаляем сообщения
        Message::truncate();
        $this->line("Удалено сообщений");
        
        // Удаляем чаты
        Chat::truncate();
        $this->line("Удалено чатов");
        
        // Удаляем клиентов
        Client::truncate();
        $this->line("Удалено клиентов");
        
        $this->info('✅ Полная очистка завершена!');
        $this->line('Удалены все чаты, сообщения, клиенты и участники чатов.');
        $this->line('Теперь можете протестировать с чистого листа.');
        
        return 0;
    }
}
