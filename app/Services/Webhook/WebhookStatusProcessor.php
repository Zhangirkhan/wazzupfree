<?php

namespace App\Services\Webhook;

use App\Contracts\WebhookStatusProcessorInterface;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class WebhookStatusProcessor implements WebhookStatusProcessorInterface
{
    /**
     * Обработка массива статусов
     */
    public function handleStatuses(array $statuses): array
    {
        $results = [];
        
        foreach ($statuses as $status) {
            $results[] = $this->processStatus($status);
        }
        
        return $results;
    }

    /**
     * Обработка одного статуса
     */
    public function processStatus(array $statusData): bool
    {
        try {
            Log::info('Processing webhook status:', [
                'status_id' => $statusData['id'] ?? 'unknown',
                'status' => $statusData['status'] ?? 'unknown',
                'recipient_id' => $statusData['recipient_id'] ?? 'unknown'
            ]);

            $status = $statusData['status'] ?? '';
            $messageId = $statusData['id'] ?? '';
            $timestamp = $statusData['timestamp'] ?? now()->toISOString();

            if (empty($status) || empty($messageId)) {
                Log::warning('Invalid status data:', $statusData);
                return false;
            }

            // Находим сообщение по wazzup_message_id
            $message = Message::where('wazzup_message_id', $messageId)->first();
            
            if (!$message) {
                Log::warning('Message not found for status update:', [
                    'wazzup_message_id' => $messageId,
                    'status' => $status
                ]);
                return false;
            }

            // Обновляем статус сообщения
            $metadata = $message->metadata ?? [];
            $metadata['wazzup_status'] = $status;
            $metadata['wazzup_status_timestamp'] = $timestamp;
            $metadata['wazzup_status_updated_at'] = now()->toISOString();

            $message->update([
                'metadata' => $metadata
            ]);

            Log::info('Message status updated:', [
                'message_id' => $message->id,
                'wazzup_message_id' => $messageId,
                'status' => $status,
                'timestamp' => $timestamp
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error processing webhook status:', [
                'status' => $statusData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
}
