<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class VideoService
{
    /**
     * Сохранение видео из URL (для входящих сообщений)
     */
    public function saveVideoFromUrl(string $url, int $chatId): ?array
    {
        try {
            Log::info('Saving video from URL', ['url' => $url, 'chat_id' => $chatId]);
            
            // Получаем содержимое видео
            $videoContent = file_get_contents($url);
            if (!$videoContent) {
                Log::error('Failed to download video content', ['url' => $url]);
                return null;
            }
            
            // Определяем расширение из URL
            $extension = $this->getExtensionFromUrl($url);
            if (!$extension) {
                $extension = 'mp4'; // По умолчанию
            }
            
            // Генерируем уникальное имя файла
            $filename = 'chat_' . $chatId . '_' . time() . '_' . uniqid() . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-videos/' . date('Y/m/d/') . $filename;
            
            // Сохраняем видео
            $saved = Storage::disk('public')->put($path, $videoContent);
            if (!$saved) {
                Log::error('Failed to save video to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем размер файла
            $size = Storage::disk('public')->size($path);
            
            // Генерируем публичный URL
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Video saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $size
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $size,
                'extension' => $extension,
                'original_url' => $url
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving video from URL', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Сохранение видео из загруженного файла (для менеджеров)
     */
    public function saveVideoFromFile(UploadedFile $file, int $chatId): ?array
    {
        try {
            Log::info('Saving video from file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);
            
            // Проверяем тип файла
            if (!$this->isValidVideo($file)) {
                Log::error('Invalid video file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            // Генерируем уникальное имя файла
            $extension = $file->getClientOriginalExtension() ?: 'mp4';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . uniqid() . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-videos/' . date('Y/m/d/') . $filename;
            
            // Сохраняем видео
            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );
            
            if (!$saved) {
                Log::error('Failed to save video file to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем размер файла
            $size = Storage::disk('public')->size($path);
            
            // Генерируем публичный URL
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Video file saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $size,
                'original_name' => $file->getClientOriginalName()
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $size,
                'extension' => $extension,
                'original_name' => $file->getClientOriginalName()
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving video file', [
                'original_name' => $file->getClientOriginalName(),
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Удаление видео
     */
    public function deleteVideo(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Video deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error deleting video', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Получение расширения из URL
     */
    protected function getExtensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            return $extension ? strtolower($extension) : null;
        }
        return null;
    }
    
    /**
     * Проверка валидности видео файла
     */
    protected function isValidVideo(UploadedFile $file): bool
    {
        $allowedMimes = [
            'video/mp4',
            'video/avi',
            'video/mov',
            'video/wmv',
            'video/flv',
            'video/webm',
            'video/mkv',
            'video/3gp'
        ];
        
        return in_array($file->getMimeType(), $allowedMimes);
    }
    
    /**
     * Получение информации о видео (размеры, длительность)
     */
    public function getVideoInfo(string $path): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            
            // Используем ffprobe для получения информации о видео
            $command = "ffprobe -v quiet -print_format json -show_format -show_streams " . escapeshellarg($fullPath);
            $output = shell_exec($command);
            
            if (!$output) {
                return null;
            }
            
            $info = json_decode($output, true);
            if (!$info) {
                return null;
            }
            
            $videoStream = null;
            foreach ($info['streams'] ?? [] as $stream) {
                if ($stream['codec_type'] === 'video') {
                    $videoStream = $stream;
                    break;
                }
            }
            
            return [
                'duration' => $info['format']['duration'] ?? null,
                'width' => $videoStream['width'] ?? null,
                'height' => $videoStream['height'] ?? null,
                'bitrate' => $info['format']['bit_rate'] ?? null,
                'size' => $info['format']['size'] ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting video info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
