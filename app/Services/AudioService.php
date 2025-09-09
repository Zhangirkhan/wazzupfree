<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AudioService
{
    /**
     * Сохранить аудио из URL
     */
    public function saveAudioFromUrl(string $url, string $chatId): ?array
    {
        try {
            Log::info('Saving audio from URL', ['url' => $url, 'chat_id' => $chatId]);
            
            // Получаем содержимое аудио
            $audioContent = file_get_contents($url);
            if (!$audioContent) {
                Log::error('Failed to download audio from URL', ['url' => $url]);
                return null;
            }
            
            // Определяем расширение файла
            $extension = $this->getExtensionFromUrl($url);
            if (!$extension) {
                $extension = 'mp3'; // По умолчанию
            }
            
            // Генерируем уникальное имя файла
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-audio/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->put($path, $audioContent);
            
            if (!$saved) {
                Log::error('Failed to save audio to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Audio saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($audioContent)
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($audioContent),
                'extension' => $extension
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving audio from URL', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Сохранить аудио из загруженного файла
     */
    public function saveAudioFromFile(UploadedFile $file, string $chatId): ?array
    {
        try {
            Log::info('Saving audio from uploaded file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);
            
            // Проверяем тип файла
            if (!$this->isValidAudio($file)) {
                Log::error('Invalid audio file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            // Генерируем уникальное имя файла
            $extension = $file->getClientOriginalExtension() ?: 'mp3';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-audio/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );
            
            if (!$saved) {
                Log::error('Failed to save uploaded audio', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Uploaded audio saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $file->getSize()
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => $file->getSize(),
                'extension' => $extension,
                'original_name' => $file->getClientOriginalName()
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving uploaded audio', [
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Удалить аудио
     */
    public function deleteAudio(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Audio deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error deleting audio', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Получить расширение файла из URL
     */
    private function getExtensionFromUrl(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['path'])) {
            $pathInfo = pathinfo($parsedUrl['path']);
            return $pathInfo['extension'] ?? null;
        }
        return null;
    }
    
    /**
     * Проверить, является ли файл валидным аудио
     */
    private function isValidAudio(UploadedFile $file): bool
    {
        $allowedMimes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/ogg',
            'audio/aac',
            'audio/m4a',
            'audio/flac',
            'audio/webm'
        ];
        
        return in_array($file->getMimeType(), $allowedMimes);
    }
    
    /**
     * Получить информацию об аудио файле
     */
    public function getAudioInfo(string $path): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) {
                return null;
            }
            
            // Используем getid3 для получения информации об аудио
            if (class_exists('getID3')) {
                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($fullPath);
                
                return [
                    'duration' => $fileInfo['playtime_seconds'] ?? null,
                    'bitrate' => $fileInfo['audio']['bitrate'] ?? null,
                    'sample_rate' => $fileInfo['audio']['sample_rate'] ?? null,
                    'channels' => $fileInfo['audio']['channels'] ?? null,
                    'format' => $fileInfo['audio']['dataformat'] ?? null
                ];
            }
            
            // Fallback - базовая информация
            return [
                'size' => filesize($fullPath),
                'format' => pathinfo($fullPath, PATHINFO_EXTENSION)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting audio info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
