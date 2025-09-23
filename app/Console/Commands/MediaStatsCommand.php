<?php

namespace App\Console\Commands;

use App\Services\Media\MediaManager;
use App\Models\Chat;
use Illuminate\Console\Command;

class MediaStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'media:stats {--chat-id= : Specific chat ID to get stats for}';

    /**
     * The console command description.
     */
    protected $description = 'Get media files statistics';

    /**
     * Execute the console command.
     */
    public function handle(MediaManager $mediaManager): int
    {
        $chatId = $this->option('chat-id');
        
        if ($chatId) {
            $this->showChatStats($mediaManager, (int) $chatId);
        } else {
            $this->showGlobalStats($mediaManager);
        }
        
        return Command::SUCCESS;
    }

    /**
     * Показать статистику для конкретного чата
     */
    private function showChatStats(MediaManager $mediaManager, int $chatId): void
    {
        $chat = Chat::find($chatId);
        if (!$chat) {
            $this->error("Chat with ID {$chatId} not found.");
            return;
        }

        $stats = $mediaManager->getMediaStats($chatId);
        
        $this->info("Media statistics for chat: {$chat->title} (ID: {$chatId})");
        $this->line("Total media files: {$stats['total']}");
        $this->line("Total size: " . $this->formatBytes($stats['total_size']));
        $this->line("From client: {$stats['from_client']}");
        $this->line("From system: {$stats['from_system']}");
        
        $this->line("\nBy type:");
        foreach ($stats['by_type'] as $type => $count) {
            if ($count > 0) {
                $this->line("  {$type}: {$count}");
            }
        }
    }

    /**
     * Показать глобальную статистику
     */
    private function showGlobalStats(MediaManager $mediaManager): void
    {
        $chats = Chat::where('is_messenger_chat', true)->get();
        
        $totalFiles = 0;
        $totalSize = 0;
        $totalFromClient = 0;
        $totalFromSystem = 0;
        $typeStats = [
            'image' => 0,
            'video' => 0,
            'audio' => 0,
            'document' => 0,
            'sticker' => 0
        ];

        $this->info("Calculating global media statistics...");
        $progressBar = $this->output->createProgressBar($chats->count());

        foreach ($chats as $chat) {
            $stats = $mediaManager->getMediaStats($chat->id);
            
            $totalFiles += $stats['total'];
            $totalSize += $stats['total_size'];
            $totalFromClient += $stats['from_client'];
            $totalFromSystem += $stats['from_system'];
            
            foreach ($stats['by_type'] as $type => $count) {
                $typeStats[$type] += $count;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line("\n");

        $this->info("Global media statistics:");
        $this->line("Total chats: {$chats->count()}");
        $this->line("Total media files: {$totalFiles}");
        $this->line("Total size: " . $this->formatBytes($totalSize));
        $this->line("From client: {$totalFromClient}");
        $this->line("From system: {$totalFromSystem}");
        
        $this->line("\nBy type:");
        foreach ($typeStats as $type => $count) {
            if ($count > 0) {
                $this->line("  {$type}: {$count}");
            }
        }
    }

    /**
     * Форматирование размера в байтах
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
