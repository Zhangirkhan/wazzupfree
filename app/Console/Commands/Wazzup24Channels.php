<?php

namespace App\Console\Commands;

use App\Services\Wazzup24Service;
use Illuminate\Console\Command;

class Wazzup24Channels extends Command
{
    protected $signature = 'wazzup24:channels';
    protected $description = 'Получение каналов Wazzup24 и их статусов';

    public function handle(Wazzup24Service $wazzupService)
    {
        $this->info('📡 Получение каналов Wazzup24...');

        $result = $wazzupService->getChannels();

        if ($result['success']) {
            $this->info('✅ Каналы получены успешно!');
            
            if (empty($result['channels'])) {
                $this->warn('⚠️ Каналы не найдены. Проверьте аккаунт Wazzup24.');
                return 0;
            }

            $this->table(
                ['ID канала', 'Транспорт', 'Plain ID', 'Статус'],
                collect($result['channels'])->map(function ($channel) {
                    return [
                        $channel['channelId'] ?? 'N/A',
                        $channel['transport'] ?? 'N/A',
                        $channel['plainId'] ?? 'N/A',
                        $channel['state'] ?? 'N/A'
                    ];
                })->toArray()
            );

            $activeChannel = collect($result['channels'])->firstWhere('state', 'active');
            if ($activeChannel) {
                $this->info('✅ Активный канал найден:');
                $this->line("ID канала: {$activeChannel['channelId']}");
                $this->line("Транспорт: {$activeChannel['transport']}");
                $this->line("Plain ID: {$activeChannel['plainId']}");
                $this->line("Статус: {$activeChannel['state']}");
            } else {
                $this->warn('⚠️ Активные каналы не найдены.');
            }

        } else {
            $this->error('❌ Ошибка получения каналов!');
            $this->error('Ошибка: ' . ($result['error'] ?? 'Неизвестная ошибка'));
        }

        return $result['success'] ? 0 : 1;
    }
}
