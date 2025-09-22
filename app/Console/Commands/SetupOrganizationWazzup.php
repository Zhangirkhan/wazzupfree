<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Services\OrganizationWazzupService;

class SetupOrganizationWazzup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organization:wazzup-setup
                            {organization : ID или slug организации}
                            {--api-key= : API ключ Wazzup24}
                            {--channel-id= : ID канала Wazzup24}
                            {--webhook-url= : URL webhook (опционально)}
                            {--test : Только тестирование подключения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Настройка Wazzup24 для организации';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organizationId = $this->argument('organization');
        $apiKey = $this->option('api-key');
        $channelId = $this->option('channel-id');
        $webhookUrl = $this->option('webhook-url');
        $testOnly = $this->option('test');

        $this->info('🔧 Настройка Wazzup24 для организации');
        $this->newLine();

        // Находим организацию
        $organization = Organization::where('id', $organizationId)
            ->orWhere('slug', $organizationId)
            ->first();

        if (!$organization) {
            $this->error('❌ Организация не найдена: ' . $organizationId);
            return 1;
        }

        $this->info('✅ Организация найдена: ' . $organization->name);
        $this->line('   ID: ' . $organization->id);
        $this->line('   Slug: ' . $organization->slug);
        $this->newLine();

        // Если только тестирование
        if ($testOnly) {
            if (!$organization->isWazzup24Configured()) {
                $this->error('❌ Wazzup24 не настроен для организации');
                return 1;
            }

            $this->info('🧪 Тестирование подключения...');
            $wazzupService = new OrganizationWazzupService($organization);
            $result = $wazzupService->testConnection();

            if ($result['success']) {
                $this->info('✅ Подключение успешно!');
                $this->line('   API Key: ***' . substr($organization->wazzup24_api_key, -4));
                $this->line('   Channel ID: ' . $organization->wazzup24_channel_id);
            } else {
                $this->error('❌ Ошибка подключения: ' . $result['error']);
                return 1;
            }

            return 0;
        }

        // Настройка Wazzup24
        if (!$apiKey || !$channelId) {
            $this->error('❌ Необходимо указать --api-key и --channel-id');
            return 1;
        }

        $this->info('⚙️  Настройка Wazzup24...');

        // Обновляем настройки организации
        $organization->update([
            'wazzup24_api_key' => $apiKey,
            'wazzup24_channel_id' => $channelId,
            'wazzup24_webhook_url' => $webhookUrl,
            'wazzup24_enabled' => true,
        ]);

        $this->info('✅ Настройки сохранены');

        // Тестируем подключение
        $this->info('🧪 Тестирование подключения...');
        $wazzupService = new OrganizationWazzupService($organization);
        $result = $wazzupService->testConnection();

        if ($result['success']) {
            $this->info('✅ Подключение успешно!');
        } else {
            $this->error('❌ Ошибка подключения: ' . $result['error']);
            $this->warn('⚠️  Настройки сохранены, но подключение не работает');
            return 1;
        }

        // Настраиваем webhook'и
        if (!$webhookUrl) {
            $webhookUrl = $organization->getWebhookUrl();
        }

        $this->info('🔗 Настройка webhook\'ов...');
        $webhookResult = $wazzupService->setupWebhooks($webhookUrl);

        if ($webhookResult['success']) {
            $this->info('✅ Webhook\'и настроены');
            $this->line('   URL: ' . $webhookUrl);
        } else {
            $this->warn('⚠️  Ошибка настройки webhook\'ов: ' . $webhookResult['error']);
        }

        $this->newLine();
        $this->info('🎉 Настройка завершена!');
        $this->line('   Организация: ' . $organization->name);
        $this->line('   API Key: ***' . substr($apiKey, -4));
        $this->line('   Channel ID: ' . $channelId);
        $this->line('   Webhook URL: ' . $webhookUrl);

        return 0;
    }
}
