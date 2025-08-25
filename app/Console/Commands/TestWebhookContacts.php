<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWebhookContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:test-contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует webhook с данными контактов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookUrl = config('services.wazzup24.webhook_url');
        
        if (!$webhookUrl) {
            $this->error('Webhook URL не настроен!');
            return 1;
        }
        
        $this->info("Тестируем webhook с контактами:");
        $this->line("URL: {$webhookUrl}");
        
        // Тестовые данные контакта
        $testData = [
            'contacts' => [
                [
                    'contactId' => 'test-contact-123',
                    'name' => 'Тестовый Контакт',
                    'phone' => '77001234567',
                    'email' => 'test@example.com',
                    'createdAt' => now()->toISOString(),
                    'updatedAt' => now()->toISOString()
                ]
            ]
        ];
        
        $this->line("Отправляем данные:");
        $this->line(json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        try {
            $response = Http::post($webhookUrl, $testData);
            
            $this->info("Ответ от webhook:");
            $this->line("Статус: " . $response->status());
            $this->line("Тело ответа: " . $response->body());
            
            if ($response->successful()) {
                $this->info("✅ Webhook успешно обработал контакты!");
            } else {
                $this->error("❌ Webhook вернул ошибку!");
            }
            
        } catch (\Exception $e) {
            $this->error("Ошибка отправки: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
