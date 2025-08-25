<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Wazzup24Monitor extends Command
{
    protected $signature = 'wazzup24:monitor';
    protected $description = 'Мониторинг webhook в реальном времени';

    public function handle()
    {
        $this->info('🔍 Мониторинг webhook и активности мессенджера...');
        $this->info('📋 Webhook URL: ' . config('wazzup24.api.webhook_url'));
        $this->newLine();
        
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            $this->info('📄 Создание файла лога: ' . $logFile);
            file_put_contents($logFile, '');
        }
        
        $this->info('📄 Мониторинг файла лога: ' . $logFile);
        $this->info('🚀 Готов к приему сообщений! Отправьте WhatsApp сообщение для тестирования...');
        $this->newLine();
        
        $lastSize = filesize($logFile);
        
        while (true) {
            clearstatcache();
            $currentSize = filesize($logFile);
            
            if ($currentSize > $lastSize) {
                $handle = fopen($logFile, 'r');
                fseek($handle, $lastSize);
                
                while (($line = fgets($handle)) !== false) {
                    if (
                        strpos($line, 'WEBHOOK RECEIVED') !== false ||
                        strpos($line, 'MESSENGER SERVICE') !== false ||
                        strpos($line, 'HANDLING INCOMING MESSAGE') !== false ||
                        strpos($line, 'Processing webhook type') !== false ||
                        strpos($line, 'Message details') !== false
                    ) {
                        $this->line('<fg=green>' . now()->format('H:i:s') . '</> ' . trim($line));
                    }
                }
                
                fclose($handle);
                $lastSize = $currentSize;
            }
            
            sleep(1);
        }
        
        return 0;
    }
}
