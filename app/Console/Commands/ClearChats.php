<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\Client;

class ClearChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить все чаты, сообщения и клиентов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю очистку чатов, сообщений и клиентов...');

        // Очищаем сообщения
        $messagesCount = Message::count();
        Message::truncate();
        $this->info("Удалено сообщений: {$messagesCount}");

        // Очищаем участников чатов
        $participantsCount = ChatParticipant::count();
        ChatParticipant::truncate();
        $this->info("Удалено участников чатов: {$participantsCount}");

        // Очищаем чаты
        $chatsCount = Chat::count();
        Chat::truncate();
        $this->info("Удалено чатов: {$chatsCount}");

        // Очищаем клиентов
        $clientsCount = Client::count();
        Client::truncate();
        $this->info("Удалено клиентов: {$clientsCount}");

        $this->info('Очистка завершена успешно!');
    }
}
