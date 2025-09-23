<?php

namespace App\Console\Commands;

use App\Services\Media\MediaManager;
use Illuminate\Console\Command;

class CleanupMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'media:cleanup {--days=30 : Number of days old files to delete}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old media files';

    /**
     * Execute the console command.
     */
    public function handle(MediaManager $mediaManager): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Starting media cleanup for files older than {$days} days...");
        
        $deletedCount = $mediaManager->cleanupOldMedia($days);
        
        $this->info("Media cleanup completed. Deleted {$deletedCount} files.");
        
        return Command::SUCCESS;
    }
}
