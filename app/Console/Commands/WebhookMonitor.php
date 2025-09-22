<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class WebhookMonitor extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'webhook:monitor {action=stats} {--count=20} {--organization=}';

    /**
     * The console command description.
     */
    protected $description = 'Мониторинг и анализ webhook запросов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $count = $this->option('count');
        $organization = $this->option('organization');

        $this->info('🔍 Мониторинг webhook\'ов Wazzup24');
        $this->info('==================================');

        switch ($action) {
            case 'stats':
                $this->showStats();
                break;
            case 'live':
                $this->liveTail();
                break;
            case 'errors':
                $this->showErrors($count);
                break;
            case 'test':
                $this->sendTestWebhook();
                break;
            case 'recent':
                $this->showRecent($count);
                break;
            default:
                $this->showHelp();
                break;
        }
    }

    /**
     * Показать статистику webhook'ов
     */
    protected function showStats()
    {
        $this->info('📊 Статистика webhook\'ов:');
        $this->newLine();

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->warn('⚠️  Файл логов не найден: ' . $logPath);
            return;
        }

        $logContent = File::get($logPath);
        $lines = explode("\n", $logContent);

        $stats = [
            'total_webhooks' => 0,
            'successful' => 0,
            'errors' => 0,
            'test_webhooks' => 0,
            'organization_webhooks' => 0,
            'today' => 0
        ];

        $today = Carbon::today()->format('Y-m-d');

        foreach ($lines as $line) {
            if (strpos($line, '=== WEBHOOK RECEIVED ===') !== false) {
                $stats['total_webhooks']++;
                if (strpos($line, $today) !== false) {
                    $stats['today']++;
                }
            }

            if (strpos($line, '=== ORGANIZATION WEBHOOK RECEIVED ===') !== false) {
                $stats['organization_webhooks']++;
            }

            if (strpos($line, '=== TEST WEBHOOK') !== false) {
                $stats['test_webhooks']++;
            }

            if (strpos($line, 'ERROR') !== false &&
                (strpos($line, 'webhook') !== false || strpos($line, 'WEBHOOK') !== false)) {
                $stats['errors']++;
            }
        }

        $stats['successful'] = $stats['total_webhooks'] - $stats['errors'];

        $this->table(
            ['Метрика', 'Значение'],
            [
                ['📡 Всего webhook\'ов', $stats['total_webhooks']],
                ['✅ Успешных', $stats['successful']],
                ['❌ Ошибок', $stats['errors']],
                ['🧪 Тестовых', $stats['test_webhooks']],
                ['🏢 Организационных', $stats['organization_webhooks']],
                ['📅 Сегодня', $stats['today']],
                ['📊 Процент успеха', $stats['total_webhooks'] > 0 ? round(($stats['successful'] / $stats['total_webhooks']) * 100, 1) . '%' : '0%']
            ]
        );

        $fileSize = File::size($logPath);
        $this->info('📁 Размер файла логов: ' . $this->formatBytes($fileSize));
    }

    /**
     * Показать последние ошибки
     */
    protected function showErrors($count = 20)
    {
        $this->info("❌ Последние {$count} ошибок webhook'ов:");
        $this->newLine();

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->warn('⚠️  Файл логов не найден');
            return;
        }

        $command = "tail -n 2000 '{$logPath}' | grep -E '(ERROR.*webhook|WEBHOOK.*ERROR|webhook.*error)' -i | tail -n {$count}";
        $output = shell_exec($command);

        if (empty($output)) {
            $this->info('✅ Ошибок webhook\'ов не найдено!');
            return;
        }

        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (!empty($line)) {
                $this->error('• ' . trim($line));
            }
        }
    }

    /**
     * Показать последние записи
     */
    protected function showRecent($count = 20)
    {
        $this->info("📋 Последние {$count} записей webhook'ов:");
        $this->newLine();

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->warn('⚠️  Файл логов не найден');
            return;
        }

        $command = "tail -n 1000 '{$logPath}' | grep -E '(WEBHOOK RECEIVED|ORGANIZATION WEBHOOK|TEST WEBHOOK)' | tail -n {$count}";
        $output = shell_exec($command);

        if (empty($output)) {
            $this->warn('⚠️  Записи webhook\'ов не найдены');
            return;
        }

        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (!empty($line)) {
                if (strpos($line, 'ERROR') !== false) {
                    $this->error('• ' . trim($line));
                } elseif (strpos($line, 'TEST') !== false) {
                    $this->warn('• ' . trim($line));
                } else {
                    $this->info('• ' . trim($line));
                }
            }
        }
    }

    /**
     * Отправить тестовый webhook
     */
    protected function sendTestWebhook()
    {
        $this->info('🧪 Отправка тестового webhook\'а...');

        $url = config('app.url') . '/api/webhooks/wazzup24';
        $data = ['test' => true];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'WebhookMonitor/1.0'
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $this->info('✅ Тестовый webhook отправлен успешно!');
                $this->info('📡 URL: ' . $url);
                $this->info('📊 Код ответа: ' . $response->getStatusCode());
            } else {
                $this->warn('⚠️  Получен неожиданный код ответа: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            $this->error('❌ Ошибка отправки тестового webhook\'а: ' . $e->getMessage());
        }
    }

    /**
     * Мониторинг в реальном времени
     */
    protected function liveTail()
    {
        $this->info('📡 Мониторинг webhook\'ов в реальном времени...');
        $this->info('   Нажмите Ctrl+C для остановки');
        $this->newLine();

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->warn('⚠️  Файл логов не найден');
            return;
        }

        $command = "tail -f '{$logPath}' | grep --line-buffered -E '(WEBHOOK RECEIVED|ORGANIZATION WEBHOOK|TEST WEBHOOK|webhook)'";

        $this->info('🔍 Начинаем мониторинг...');
        $this->newLine();

        // Выполняем команду
        passthru($command);
    }

    /**
     * Показать справку
     */
    protected function showHelp()
    {
        $this->info('📖 Доступные команды мониторинга webhook\'ов:');
        $this->newLine();

        $this->table(
            ['Команда', 'Описание', 'Пример'],
            [
                ['stats', 'Показать статистику', 'php artisan webhook:monitor stats'],
                ['recent', 'Последние записи', 'php artisan webhook:monitor recent --count=50'],
                ['errors', 'Показать ошибки', 'php artisan webhook:monitor errors --count=10'],
                ['test', 'Отправить тест', 'php artisan webhook:monitor test'],
                ['live', 'Мониторинг в реальном времени', 'php artisan webhook:monitor live'],
            ]
        );

        $this->newLine();
        $this->info('🔗 Опции:');
        $this->info('   --count=N        Количество записей для показа');
        $this->info('   --organization=X Фильтр по организации');
    }

    /**
     * Форматирование размера файла
     */
    protected function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
