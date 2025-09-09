<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Сохранить изображение из URL
     */
    public function saveImageFromUrl(string $url, string $chatId): ?array
    {
        try {
            Log::info('Saving image from URL', ['url' => $url, 'chat_id' => $chatId]);
            
            // Получаем содержимое изображения
            $imageContent = file_get_contents($url);
            if (!$imageContent) {
                Log::error('Failed to download image from URL', ['url' => $url]);
                return null;
            }
            
            // Определяем расширение файла
            $extension = $this->getExtensionFromUrl($url);
            if (!$extension) {
                $extension = 'jpg'; // По умолчанию
            }
            
            // Генерируем уникальное имя файла
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-images/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->put($path, $imageContent);
            
            if (!$saved) {
                Log::error('Failed to save image to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Image saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($imageContent)
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($imageContent),
                'extension' => $extension
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving image from URL', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Сохранить изображение из загруженного файла
     */
    public function saveImageFromFile(UploadedFile $file, string $chatId): ?array
    {
        try {
            Log::info('Saving image from uploaded file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);
            
            // Проверяем тип файла
            if (!$this->isValidImage($file)) {
                Log::error('Invalid image file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            // Генерируем уникальное имя файла
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-images/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );
            
            if (!$saved) {
                Log::error('Failed to save uploaded image', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Uploaded image saved successfully', [
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
            Log::error('Error saving uploaded image', [
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Удалить изображение
     */
    public function deleteImage(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Image deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error deleting image', [
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
     * Проверить, является ли файл валидным изображением
     */
    private function isValidImage(UploadedFile $file): bool
    {
        $allowedMimes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        return in_array($file->getMimeType(), $allowedMimes);
    }
    
    /**
     * Получить размеры изображения
     */
    public function getImageDimensions(string $path): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) {
                return null;
            }
            
            $imageInfo = getimagesize($fullPath);
            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'mime' => $imageInfo['mime']
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting image dimensions', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
