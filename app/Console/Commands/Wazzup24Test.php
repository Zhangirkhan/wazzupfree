<?php

namespace App\Console\Commands;

use App\Services\Wazzup24Service;
use Illuminate\Console\Command;

class Wazzup24Test extends Command
{
    protected $signature = 'wazzup24:test';
    protected $description = 'Проверка подключения к Wazzup24 API';

    public function handle(Wazzup24Service $wazzupService)
    {
        $this->info('🔍 Проверка подключения к Wazzup24...');

        $result = $wazzupService->testConnection();

        if ($result['success']) {
            $this->info('✅ Подключение успешно!');
            $this->table(
                ['Параметр', 'Значение', 'Статус'],
                [
                    ['API Key', config('services.wazzup24.api_key') ? 'Установлен' : 'Не установлен', '✅'],
                    ['Webhook URL', config('services.wazzup24.webhook_url') ?: 'Не установлен', '✅'],
                    ['Channel ID', config('services.wazzup24.channel_id') ?: 'Не установлен', config('services.wazzup24.channel_id') ? '✅' : '❌'],
                ]
            );
        } else {
            $this->error('❌ Ошибка подключения!');
            $this->error('Ошибка: ' . ($result['error'] ?? 'Неизвестная ошибка'));
            $this->line('');
            $this->line('Проверьте настройки в .env файле:');
            $this->line('- WAZZUP24_API_KEY');
            $this->line('- WAZZUP24_WEBHOOK_URL');
            $this->line('- WAZZUP24_CHANNEL_ID');
        }

        return $result['success'] ? 0 : 1;
    }
}
