<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;

class RestoreChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:restore {chat_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Восстанавливает завершенный чат';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chatId = $this->argument('chat_id');
        
        $chat = Chat::find($chatId);
        if (!$chat) {
            $this->error("Чат с ID {$chatId} не найден");
            return 1;
        }

        $oldStatus = $chat->messenger_status;
        $chat->update(['messenger_status' => 'active']);
        
        $this->info("Чат {$chatId} восстановлен");
        $this->info("Статус изменен с '{$oldStatus}' на 'active'");
        
        return 0;
    }
}
