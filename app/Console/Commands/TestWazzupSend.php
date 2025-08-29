<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Wazzup24Service;

class TestWazzupSend extends Command
{
    protected $signature = 'test:wazzup-send';
    protected $description = 'Тестирование отправки сообщения через Wazzup24';

    public function handle()
    {
        $this->info('Тестирование отправки сообщения через Wazzup24...');

        $wazzupService = app('\App\Services\Wazzup24Service');
        
        $channelId = config('services.wazzup24.channel_id');
        $chatType = 'whatsapp';
        $chatId = '77476644108';
        $text = 'Тестовое сообщение из системы!';
        
        $this->info("Channel ID: {$channelId}");
        $this->info("Chat Type: {$chatType}");
        $this->info("Chat ID: {$chatId}");
        $this->info("Text: {$text}");
        
        try {
            $result = $wazzupService->sendMessage($channelId, $chatType, $chatId, $text);
            
            if ($result['success']) {
                $this->info('✅ Сообщение отправлено успешно!');
                $this->info('Message ID: ' . ($result['message_id'] ?? 'N/A'));
                $this->info('Response: ' . json_encode($result['data'] ?? [], JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Ошибка отправки: ' . ($result['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Исключение: ' . $e->getMessage());
        }
        
        $this->info('Тестирование завершено.');
    }
}
