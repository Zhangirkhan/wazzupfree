<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private User $user,
        private string $message,
        private array $data = []
    ) {}

    public function handle(): void
    {
        try {
            // Здесь можно добавить отправку уведомлений через email, SMS, push и т.д.
            Log::info('Notification sent', [
                'user_id' => $this->user->id,
                'message' => $this->message,
                'data' => $this->data
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
