<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StickerService
{
    /**
     * Сохранить стикер из URL
     */
    public function saveStickerFromUrl(string $url, string $chatId): ?array
    {
        try {
            Log::info('Saving sticker from URL', ['url' => $url, 'chat_id' => $chatId]);
            
            // Получаем содержимое стикера
            $stickerContent = file_get_contents($url);
            if (!$stickerContent) {
                Log::error('Failed to download sticker from URL', ['url' => $url]);
                return null;
            }
            
            // Определяем расширение файла
            $extension = $this->getExtensionFromUrl($url);
            if (!$extension) {
                // Для стикеров обычно используется WebP или PNG
                $extension = 'webp';
            }
            
            // Генерируем уникальное имя файла
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-stickers/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->put($path, $stickerContent);
            
            if (!$saved) {
                Log::error('Failed to save sticker to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Sticker saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($stickerContent)
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($stickerContent),
                'extension' => $extension
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving sticker from URL', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Сохранить стикер из загруженного файла
     */
    public function saveStickerFromFile(UploadedFile $file, string $chatId): ?array
    {
        try {
            Log::info('Saving sticker from uploaded file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);
            
            // Проверяем тип файла
            if (!$this->isValidSticker($file)) {
                Log::error('Invalid sticker file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            // Генерируем уникальное имя файла
            $extension = $file->getClientOriginalExtension() ?: 'webp';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Путь для сохранения
            $path = 'chat-stickers/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );
            
            if (!$saved) {
                Log::error('Failed to save uploaded sticker', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Uploaded sticker saved successfully', [
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
            Log::error('Error saving uploaded sticker', [
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Удалить стикер
     */
    public function deleteSticker(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Sticker deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error deleting sticker', [
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
     * Проверить, является ли файл валидным стикером
     */
    private function isValidSticker(UploadedFile $file): bool
    {
        $allowedMimes = [
            'image/webp',    // Основной формат стикеров WhatsApp
            'image/png',     // Альтернативный формат
            'image/gif',     // Анимированные стикеры
            'image/jpeg',    // Редко используется для стикеров
            'image/jpg'      // Редко используется для стикеров
        ];
        
        return in_array($file->getMimeType(), $allowedMimes);
    }
    
    /**
     * Получить размеры стикера
     */
    public function getStickerDimensions(string $path): ?array
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
            Log::error('Error getting sticker dimensions', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Проверить, является ли стикер анимированным
     */
    public function isAnimatedSticker(string $path): bool
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) {
                return false;
            }
            
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            
            // GIF файлы обычно анимированные
            if ($extension === 'gif') {
                return true;
            }
            
            // WebP может быть анимированным
            if ($extension === 'webp') {
                $content = file_get_contents($fullPath, false, null, 0, 1024);
                // Проверяем наличие маркера анимации в WebP
                return strpos($content, 'ANIM') !== false || strpos($content, 'ANMF') !== false;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error checking if sticker is animated', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Получить информацию о стикере
     */
    public function getStickerInfo(string $path): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) {
                return null;
            }
            
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $size = filesize($fullPath);
            
            $info = [
                'size' => $size,
                'extension' => $extension,
                'mime_type' => mime_content_type($fullPath),
                'is_animated' => $this->isAnimatedSticker($path)
            ];
            
            // Получаем размеры изображения
            $dimensions = $this->getStickerDimensions($path);
            if ($dimensions) {
                $info['width'] = $dimensions['width'];
                $info['height'] = $dimensions['height'];
                $info['aspect_ratio'] = $dimensions['width'] / $dimensions['height'];
            }
            
            return $info;
            
        } catch (\Exception $e) {
            Log::error('Error getting sticker info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Оптимизировать стикер (сжать для экономии места)
     */
    public function optimizeSticker(string $path, int $maxSize = 512): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) {
                return null;
            }
            
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            
            // Для WebP и PNG можно использовать GD для оптимизации
            if (in_array($extension, ['webp', 'png', 'jpg', 'jpeg'])) {
                $image = null;
                
                switch ($extension) {
                    case 'webp':
                        $image = imagecreatefromwebp($fullPath);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($fullPath);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        $image = imagecreatefromjpeg($fullPath);
                        break;
                }
                
                if ($image) {
                    $originalWidth = imagesx($image);
                    $originalHeight = imagesy($image);
                    
                    // Вычисляем новые размеры
                    if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
                        $ratio = min($maxSize / $originalWidth, $maxSize / $originalHeight);
                        $newWidth = intval($originalWidth * $ratio);
                        $newHeight = intval($originalHeight * $ratio);
                        
                        // Создаем новое изображение
                        $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);
                        
                        // Сохраняем прозрачность для PNG и WebP
                        if ($extension === 'png' || $extension === 'webp') {
                            imagealphablending($optimizedImage, false);
                            imagesavealpha($optimizedImage, true);
                            $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                            imagefill($optimizedImage, 0, 0, $transparent);
                        }
                        
                        // Копируем и изменяем размер
                        imagecopyresampled($optimizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                        
                        // Сохраняем оптимизированное изображение
                        $optimizedPath = str_replace('.' . $extension, '_optimized.' . $extension, $fullPath);
                        
                        $saved = false;
                        switch ($extension) {
                            case 'webp':
                                $saved = imagewebp($optimizedImage, $optimizedPath, 80);
                                break;
                            case 'png':
                                $saved = imagepng($optimizedImage, $optimizedPath, 8);
                                break;
                            case 'jpg':
                            case 'jpeg':
                                $saved = imagejpeg($optimizedImage, $optimizedPath, 80);
                                break;
                        }
                        
                        imagedestroy($image);
                        imagedestroy($optimizedImage);
                        
                        if ($saved) {
                            Log::info('Sticker optimized successfully', [
                                'original_path' => $path,
                                'optimized_path' => $optimizedPath,
                                'original_size' => filesize($fullPath),
                                'optimized_size' => filesize($optimizedPath)
                            ]);
                            
                            return [
                                'optimized_path' => str_replace(Storage::disk('public')->path(''), '', $optimizedPath),
                                'original_size' => filesize($fullPath),
                                'optimized_size' => filesize($optimizedPath),
                                'compression_ratio' => filesize($optimizedPath) / filesize($fullPath)
                            ];
                        }
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error optimizing sticker', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
