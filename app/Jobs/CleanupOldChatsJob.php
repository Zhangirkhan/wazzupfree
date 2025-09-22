<?php

namespace App\Jobs;

use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupOldChatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $daysOld = 90
    ) {}

    public function handle(): void
    {
        try {
            $cutoffDate = now()->subDays($this->daysOld);
            
            $deletedCount = Chat::where('status', 'closed')
                ->where('updated_at', '<', $cutoffDate)
                ->delete();
            
            Log::info('Old chats cleaned up', [
                'deleted_count' => $deletedCount,
                'days_old' => $this->daysOld
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old chats', [
                'error' => $e->getMessage(),
                'days_old' => $this->daysOld
            ]);
            
            throw $e;
        }
    }
}
