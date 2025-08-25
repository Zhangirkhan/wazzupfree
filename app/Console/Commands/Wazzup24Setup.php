<?php

namespace App\Console\Commands;

use App\Services\Wazzup24Service;
use Illuminate\Console\Command;

class Wazzup24Setup extends Command
{
    protected $signature = 'wazzup24:setup {--url=}';
    protected $description = 'Установка webhook для Wazzup24';

    public function handle(Wazzup24Service $wazzupService)
    {
        $this->info('🔧 Установка webhook для Wazzup24...');

        $webhookUrl = $this->option('url') ?: config('services.wazzup24.webhook_url');
        
        if (!$webhookUrl) {
            $this->error('❌ URL webhook не настроен!');
            $this->line('Установите WAZZUP24_WEBHOOK_URL в .env файле или используйте --url');
            return 1;
        }

        $this->line("Webhook URL: {$webhookUrl}");

        $result = $wazzupService->setupWebhooks($webhookUrl, [
            'messagesAndStatuses' => true,
            'contactsAndDealsCreation' => true,
            'channelsUpdates' => false,
            'templateStatus' => false
        ]);

        if ($result['success']) {
            $this->info('✅ Webhook успешно установлен!');
            
            $this->info('Проверка настроек webhook...');
            $webhooks = $wazzupService->getWebhooks();
            
            if ($webhooks['success']) {
                $this->table(
                    ['Настройка', 'Значение'],
                    [
                        ['Webhook URL', $webhooks['data']['webhooksUri'] ?? 'Не установлен'],
                        ['Сообщения и статусы', $webhooks['data']['subscriptions']['messagesAndStatuses'] ?? 'false'],
                        ['Создание контактов', $webhooks['data']['subscriptions']['contactsAndDealsCreation'] ?? 'false'],
                        ['Обновления каналов', $webhooks['data']['subscriptions']['channelsUpdates'] ?? 'false'],
                        ['Статус шаблонов', $webhooks['data']['subscriptions']['templateStatus'] ?? 'false'],
                    ]
                );
            }
        } else {
            $this->error('❌ Ошибка установки webhook!');
            $this->error('Ошибка: ' . ($result['error'] ?? 'Неизвестная ошибка'));
            return 1;
        }

        return 0;
    }
}
