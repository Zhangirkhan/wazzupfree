<?php

namespace App\Services\Media;

use App\Contracts\MediaProcessorInterface;
use App\Services\ImageService;
use App\Services\VideoService;
use App\Services\AudioService;
use App\Services\DocumentService;
use App\Services\StickerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MediaProcessor implements MediaProcessorInterface
{
    public function __construct(
        private ImageService $imageService,
        private VideoService $videoService,
        private AudioService $audioService,
        private DocumentService $documentService,
        private StickerService $stickerService
    ) {}

    /**
     * Обработка изображения
     */
    public function processImage(string $url, int $chatId, ?string $caption = null): ?array
    {
        try {
            Log::info('Processing image', [
                'url' => $url,
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            // Валидируем изображение
            if (!$this->validateMediaFile($url, 'image')) {
                Log::warning('Invalid image file', ['url' => $url]);
                return null;
            }

            // Сохраняем изображение
            $result = $this->imageService->saveImageFromUrl($url, (string)$chatId);
            
            if ($result) {
                $result['caption'] = $caption;
                $result['type'] = 'image';
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing image:', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Обработка видео
     */
    public function processVideo(string $url, int $chatId, ?string $caption = null): ?array
    {
        try {
            Log::info('Processing video', [
                'url' => $url,
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            // Валидируем видео
            if (!$this->validateMediaFile($url, 'video')) {
                Log::warning('Invalid video file', ['url' => $url]);
                return null;
            }

            // Сохраняем видео
            $result = $this->videoService->saveVideoFromUrl($url, $chatId);
            
            if ($result) {
                $result['caption'] = $caption;
                $result['type'] = 'video';
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing video:', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Обработка аудио
     */
    public function processAudio(string $url, int $chatId, ?string $caption = null): ?array
    {
        try {
            Log::info('Processing audio', [
                'url' => $url,
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            // Валидируем аудио
            if (!$this->validateMediaFile($url, 'audio')) {
                Log::warning('Invalid audio file', ['url' => $url]);
                return null;
            }

            // Сохраняем аудио
            $result = $this->audioService->saveAudioFromUrl($url, $chatId);
            
            if ($result) {
                $result['caption'] = $caption;
                $result['type'] = 'audio';
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing audio:', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Обработка документа
     */
    public function processDocument(string $url, int $chatId, string $filename, ?string $caption = null): ?array
    {
        try {
            Log::info('Processing document', [
                'url' => $url,
                'chat_id' => $chatId,
                'filename' => $filename,
                'caption' => $caption
            ]);

            // Валидируем документ
            if (!$this->validateMediaFile($url, 'document')) {
                Log::warning('Invalid document file', ['url' => $url]);
                return null;
            }

            // Сохраняем документ
            $result = $this->documentService->saveDocumentFromUrl($url, $chatId, $filename);
            
            if ($result) {
                $result['caption'] = $caption;
                $result['type'] = 'document';
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing document:', [
                'url' => $url,
                'chat_id' => $chatId,
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Обработка стикера
     */
    public function processSticker(string $url, int $chatId, ?string $caption = null): ?array
    {
        try {
            Log::info('Processing sticker', [
                'url' => $url,
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            // Валидируем стикер
            if (!$this->validateMediaFile($url, 'sticker')) {
                Log::warning('Invalid sticker file', ['url' => $url]);
                return null;
            }

            // Сохраняем стикер
            $result = $this->stickerService->saveStickerFromUrl($url, $chatId);
            
            if ($result) {
                $result['caption'] = $caption;
                $result['type'] = 'sticker';
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing sticker:', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Валидация медиа-файла
     */
    public function validateMediaFile(string $url, string $type): bool
    {
        try {
            // Проверяем, что URL валидный
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::warning('Invalid URL format', ['url' => $url]);
                return false;
            }

            // Получаем информацию о файле
            $mediaInfo = $this->getMediaInfo($url);
            if (!$mediaInfo) {
                return false;
            }

            // Проверяем размер файла (максимум 100MB)
            $maxSize = 100 * 1024 * 1024; // 100MB
            if ($mediaInfo['size'] > $maxSize) {
                Log::warning('File too large', [
                    'url' => $url,
                    'size' => $mediaInfo['size'],
                    'max_size' => $maxSize
                ]);
                return false;
            }

            // Проверяем тип файла
            $allowedTypes = $this->getAllowedTypes($type);
            if (!in_array($mediaInfo['mime_type'], $allowedTypes)) {
                Log::warning('Invalid file type', [
                    'url' => $url,
                    'mime_type' => $mediaInfo['mime_type'],
                    'allowed_types' => $allowedTypes
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error validating media file:', [
                'url' => $url,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Получение информации о медиа-файле
     */
    public function getMediaInfo(string $url): ?array
    {
        try {
            // Получаем заголовки файла
            $headers = get_headers($url, 1);
            if (!$headers) {
                Log::warning('Failed to get headers', ['url' => $url]);
                return null;
            }

            $contentLength = $headers['Content-Length'] ?? null;
            $contentType = $headers['Content-Type'] ?? null;

            // Если Content-Length не найден, пытаемся получить размер через HEAD запрос
            if (!$contentLength) {
                $response = Http::head($url);
                $contentLength = $response->header('Content-Length');
                $contentType = $contentType ?: $response->header('Content-Type');
            }

            return [
                'size' => (int) $contentLength,
                'mime_type' => $contentType,
                'url' => $url,
                'headers' => $headers
            ];

        } catch (\Exception $e) {
            Log::error('Error getting media info:', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Получение разрешенных типов файлов для каждого типа медиа
     */
    private function getAllowedTypes(string $type): array
    {
        return match ($type) {
            'image' => [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/bmp'
            ],
            'video' => [
                'video/mp4',
                'video/avi',
                'video/mov',
                'video/wmv',
                'video/flv',
                'video/webm',
                'video/3gp'
            ],
            'audio' => [
                'audio/mpeg',
                'audio/mp3',
                'audio/wav',
                'audio/ogg',
                'audio/aac',
                'audio/m4a'
            ],
            'document' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv'
            ],
            'sticker' => [
                'image/webp',
                'image/png',
                'image/gif'
            ],
            default => []
        };
    }
}
