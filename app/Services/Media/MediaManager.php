<?php

namespace App\Services\Media;

use App\Contracts\MediaProcessorInterface;
use App\Models\Message;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaManager
{
    public function __construct(
        private MediaProcessorInterface $mediaProcessor
    ) {}

    /**
     * Обработка медиа-файла по типу
     */
    public function processMediaByType(string $type, string $url, int $chatId, ?string $caption = null, ?string $filename = null): ?array
    {
        return match ($type) {
            'image' => $this->mediaProcessor->processImage($url, $chatId, $caption),
            'video' => $this->mediaProcessor->processVideo($url, $chatId, $caption),
            'audio' => $this->mediaProcessor->processAudio($url, $chatId, $caption),
            'document' => $this->mediaProcessor->processDocument($url, $chatId, $filename ?: 'Документ', $caption),
            'sticker' => $this->mediaProcessor->processSticker($url, $chatId, $caption),
            default => null
        };
    }

    /**
     * Создание сообщения с медиа-файлом
     */
    public function createMediaMessage(Chat $chat, array $mediaData, bool $isFromClient = true, ?int $userId = null): ?Message
    {
        try {
            $messageContent = $mediaData['caption'] ?: $this->getDefaultMessageContent($mediaData['type']);
            
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $userId ?: 1,
                'content' => $messageContent,
                'type' => $mediaData['type'],
                'is_from_client' => $isFromClient,
                'metadata' => $this->buildMediaMetadata($mediaData, $isFromClient)
            ]);

            Log::info('Media message created:', [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'type' => $mediaData['type'],
                'is_from_client' => $isFromClient
            ]);

            return $message;

        } catch (\Exception $e) {
            Log::error('Error creating media message:', [
                'chat_id' => $chat->id,
                'media_data' => $mediaData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Удаление медиа-файла
     */
    public function deleteMediaFile(string $filePath): bool
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('Media file deleted:', ['path' => $filePath]);
                return true;
            }

            Log::warning('Media file not found for deletion:', ['path' => $filePath]);
            return false;

        } catch (\Exception $e) {
            Log::error('Error deleting media file:', [
                'path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Получение статистики медиа-файлов
     */
    public function getMediaStats(int $chatId): array
    {
        try {
            $messages = Message::where('chat_id', $chatId)
                ->whereIn('type', ['image', 'video', 'audio', 'document', 'sticker'])
                ->get();

            $stats = [
                'total' => $messages->count(),
                'by_type' => [
                    'image' => 0,
                    'video' => 0,
                    'audio' => 0,
                    'document' => 0,
                    'sticker' => 0
                ],
                'total_size' => 0,
                'from_client' => 0,
                'from_system' => 0
            ];

            foreach ($messages as $message) {
                $stats['by_type'][$message->type]++;
                
                if ($message->is_from_client) {
                    $stats['from_client']++;
                } else {
                    $stats['from_system']++;
                }

                // Подсчитываем размер файлов
                $metadata = $message->metadata ?? [];
                if (isset($metadata['size'])) {
                    $stats['total_size'] += (int) $metadata['size'];
                }
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('Error getting media stats:', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'total' => 0,
                'by_type' => [
                    'image' => 0,
                    'video' => 0,
                    'audio' => 0,
                    'document' => 0,
                    'sticker' => 0
                ],
                'total_size' => 0,
                'from_client' => 0,
                'from_system' => 0
            ];
        }
    }

    /**
     * Очистка старых медиа-файлов
     */
    public function cleanupOldMedia(int $daysOld = 30): int
    {
        try {
            $cutoffDate = now()->subDays($daysOld);
            
            $oldMessages = Message::whereIn('type', ['image', 'video', 'audio', 'document', 'sticker'])
                ->where('created_at', '<', $cutoffDate)
                ->get();

            $deletedCount = 0;

            foreach ($oldMessages as $message) {
                $metadata = $message->metadata ?? [];
                $filePath = $metadata['path'] ?? null;

                if ($filePath && $this->deleteMediaFile($filePath)) {
                    $deletedCount++;
                }
            }

            Log::info('Media cleanup completed:', [
                'deleted_files' => $deletedCount,
                'days_old' => $daysOld
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Error during media cleanup:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 0;
        }
    }

    /**
     * Получение содержимого сообщения по умолчанию
     */
    private function getDefaultMessageContent(string $type): string
    {
        return match ($type) {
            'image' => 'Изображение',
            'video' => 'Видео',
            'audio' => 'Аудио сообщение',
            'document' => 'Документ',
            'sticker' => 'Стикер',
            default => 'Медиа-файл'
        };
    }

    /**
     * Построение метаданных для медиа-файла
     */
    private function buildMediaMetadata(array $mediaData, bool $isFromClient): array
    {
        $metadata = [
            'type' => $mediaData['type'],
            'url' => $mediaData['url'] ?? null,
            'path' => $mediaData['path'] ?? null,
            'filename' => $mediaData['filename'] ?? null,
            'size' => $mediaData['size'] ?? null,
            'extension' => $mediaData['extension'] ?? null,
            'caption' => $mediaData['caption'] ?? null,
            'direction' => $isFromClient ? 'incoming' : 'outgoing',
            'processed_at' => now()->toISOString()
        ];

        // Добавляем специфичные для типа поля
        switch ($mediaData['type']) {
            case 'image':
                $metadata['image_url'] = $mediaData['url'] ?? null;
                $metadata['image_path'] = $mediaData['path'] ?? null;
                break;
            case 'video':
                $metadata['video_url'] = $mediaData['url'] ?? null;
                $metadata['video_path'] = $mediaData['path'] ?? null;
                break;
            case 'audio':
                $metadata['audio_url'] = $mediaData['url'] ?? null;
                $metadata['audio_path'] = $mediaData['path'] ?? null;
                break;
            case 'document':
                $metadata['document_url'] = $mediaData['url'] ?? null;
                $metadata['document_path'] = $mediaData['path'] ?? null;
                $metadata['document_name'] = $mediaData['filename'] ?? null;
                break;
            case 'sticker':
                $metadata['sticker_url'] = $mediaData['url'] ?? null;
                $metadata['sticker_path'] = $mediaData['path'] ?? null;
                break;
        }

        return $metadata;
    }
}
