<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\Client;
use Illuminate\Support\Facades\Redis;

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

        // Отправляем событие через SSE для обновления фронтенда
        $this->broadcastChatsClearedEvent();

        $this->info('Очистка завершена успешно!');
    }

    /**
     * Отправляет событие об очистке чатов через SSE
     */
    private function broadcastChatsClearedEvent(): void
    {
        try {
            $eventData = [
                'type' => 'chats_cleared',
                'timestamp' => now()->toISOString()
            ];

            // Отправляем в глобальный канал чатов
            Redis::publish('chats.global', json_encode($eventData));
            Redis::lpush('sse_queue:chats.global', json_encode($eventData));
            Redis::expire('sse_queue:chats.global', 3600);

            // Отправляем событие всем активным пользователям
            $activeUsers = \App\Models\User::whereNotNull('id')->pluck('id');
            foreach ($activeUsers as $userId) {
                Redis::lpush('sse_queue:user.' . $userId . '.chats', json_encode($eventData));
                Redis::expire('sse_queue:user.' . $userId . '.chats', 3600);
            }

            $this->info('SSE событие об очистке чатов отправлено');
        } catch (\Exception $e) {
            $this->warn('Не удалось отправить SSE событие: ' . $e->getMessage());
        }
    }
}
