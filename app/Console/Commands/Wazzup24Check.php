<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Wazzup24Check extends Command
{
    protected $signature = 'wazzup24:check';
    protected $description = 'Проверка статуса webhook Wazzup24';

    public function handle()
    {
        $this->info('🔍 Проверка статуса webhook Wazzup24...');
        
        $apiKey = config('wazzup24.api.key');
        $channelId = config('wazzup24.api.channel_id');
        $apiUrl = config('wazzup24.api.url');
        $webhookUrl = config('wazzup24.api.webhook_url');
        
        $this->info('📋 Конфигурация:');
        $this->line('  API Key: ' . ($apiKey ? '✅ Установлен' : '❌ Не установлен'));
        $this->line('  Channel ID: ' . ($channelId ? '✅ Установлен' : '❌ Не установлен'));
        $this->line('  API URL: ' . $apiUrl);
        $this->line('  Webhook URL: ' . $webhookUrl);
        $this->newLine();
        
        if (!$apiKey || !$channelId) {
            $this->error('❌ Отсутствует обязательная конфигурация!');
            return 1;
        }
        
        // Проверяем доступность webhook URL
        $this->info('🔗 Тестирование доступности webhook URL...');
        try {
            $response = Http::timeout(10)->post($webhookUrl, [
                'test' => true,
                'messages' => []
            ]);
            if ($response->successful()) {
                $this->info('✅ Webhook URL доступен');
            } else {
                $this->warn('⚠️ Webhook URL вернул статус: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ Webhook URL недоступен: ' . $e->getMessage());
        }
        
        // Проверяем текущие webhook'и в Wazzup24
        $this->info('📡 Проверка конфигурации webhook в Wazzup24...');
        try {
            $wazzupService = app(\App\Services\Wazzup24Service::class);
            $result = $wazzupService->getWebhooks();
            
            if ($result['success']) {
                $webhooks = $result['data'];
                $this->info('✅ Конфигурация webhook успешно получена');
                
                if (empty($webhooks)) {
                    $this->warn('⚠️ Webhook не настроен');
                } else {
                    $this->info('📋 Настроенные webhook:');
                    $this->line('  - URL: ' . ($webhooks['webhooksUri'] ?? 'N/A'));
                    $this->line('  - Подписки:');
                    if (isset($webhooks['subscriptions'])) {
                        foreach ($webhooks['subscriptions'] as $key => $value) {
                            $this->line('    - ' . $key . ': ' . ($value ? 'Да' : 'Нет'));
                        }
                    }
                }
            } else {
                $this->error('❌ Ошибка получения конфигурации webhook!');
                $this->error('Ошибка: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка проверки webhook: ' . $e->getMessage());
        }
        
        $this->newLine();
        $this->info('💡 Для установки webhook выполните: php artisan wazzup24:setup');
        
        return 0;
    }
}
