<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Сохранить документ из URL
     */
    public function saveDocumentFromUrl(string $url, string $chatId, string $originalName = null): ?array
    {
        try {
            Log::info('Saving document from URL', [
                'url' => $url, 
                'chat_id' => $chatId,
                'original_name' => $originalName
            ]);
            
            // Получаем содержимое документа
            $documentContent = file_get_contents($url);
            if (!$documentContent) {
                Log::error('Failed to download document from URL', ['url' => $url]);
                return null;
            }
            
            // Определяем расширение файла
            $extension = $this->getExtensionFromUrl($url);
            if (!$extension && $originalName) {
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            }
            if (!$extension) {
                $extension = 'bin'; // По умолчанию
            }
            
            // Генерируем имя файла
            $filename = $this->generateFilename($chatId, $extension, $originalName);
            
            // Путь для сохранения
            $path = 'chat-documents/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->put($path, $documentContent);
            
            if (!$saved) {
                Log::error('Failed to save document to storage', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Document saved successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($documentContent)
            ]);
            
            return [
                'filename' => $filename,
                'path' => $path,
                'url' => $publicUrl,
                'size' => strlen($documentContent),
                'extension' => $extension,
                'original_name' => $originalName ?: $filename
            ];
            
        } catch (\Exception $e) {
            Log::error('Error saving document from URL', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Сохранить документ из загруженного файла
     */
    public function saveDocumentFromFile(UploadedFile $file, string $chatId): ?array
    {
        try {
            Log::info('Saving document from uploaded file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);
            
            // Проверяем тип файла
            if (!$this->isValidDocument($file)) {
                Log::error('Invalid document file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            // Генерируем имя файла
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $filename = $this->generateFilename($chatId, $extension, $file->getClientOriginalName());
            
            // Путь для сохранения
            $path = 'chat-documents/' . date('Y/m/d/') . $filename;
            
            // Сохраняем файл
            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );
            
            if (!$saved) {
                Log::error('Failed to save uploaded document', ['path' => $path]);
                return null;
            }
            
            // Получаем URL для доступа к файлу
            $publicUrl = config('app.url') . '/storage/' . $path;
            
            Log::info('Uploaded document saved successfully', [
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
            Log::error('Error saving uploaded document', [
                'original_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Удалить документ
     */
    public function deleteDocument(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Document deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error deleting document', [
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
     * Проверить, является ли файл валидным документом
     */
    private function isValidDocument(UploadedFile $file): bool
    {
        $allowedMimes = [
            // PDF
            'application/pdf',
            // Microsoft Office
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            // Текстовые файлы
            'text/plain',
            'text/csv',
            'application/rtf',
            // Архивы
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/gzip',
            // Изображения (как документы)
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            // Другие
            'application/json',
            'application/xml',
            'text/xml'
        ];
        
        return in_array($file->getMimeType(), $allowedMimes);
    }
    
    /**
     * Генерировать имя файла
     */
    private function generateFilename(string $chatId, string $extension, string $originalName = null): string
    {
        $timestamp = time();
        $random = Str::random(10);
        
        if ($originalName) {
            // Очищаем оригинальное имя от небезопасных символов
            $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $cleanName = substr($cleanName, 0, 50); // Ограничиваем длину
            return "chat_{$chatId}_{$timestamp}_{$cleanName}_{$random}.{$extension}";
        }
        
        return "chat_{$chatId}_{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * Получить информацию о документе
     */
    public function getDocumentInfo(string $path): ?array
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
                'mime_type' => mime_content_type($fullPath)
            ];
            
            // Дополнительная информация для PDF
            if ($extension === 'pdf') {
                $info['is_pdf'] = true;
                // Можно добавить количество страниц через библиотеку для работы с PDF
            }
            
            // Дополнительная информация для изображений
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $imageInfo = getimagesize($fullPath);
                if ($imageInfo) {
                    $info['width'] = $imageInfo[0];
                    $info['height'] = $imageInfo[1];
                    $info['is_image'] = true;
                }
            }
            
            return $info;
            
        } catch (\Exception $e) {
            Log::error('Error getting document info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Получить иконку для типа документа
     */
    public function getDocumentIcon(string $extension): string
    {
        $extension = strtolower($extension);
        
        $icons = [
            'pdf' => '📄',
            'doc' => '📝',
            'docx' => '📝',
            'xls' => '📊',
            'xlsx' => '📊',
            'ppt' => '📊',
            'pptx' => '📊',
            'txt' => '📄',
            'csv' => '📊',
            'zip' => '🗜️',
            'rar' => '🗜️',
            '7z' => '🗜️',
            'jpg' => '🖼️',
            'jpeg' => '🖼️',
            'png' => '🖼️',
            'gif' => '🖼️',
            'webp' => '🖼️',
            'json' => '📄',
            'xml' => '📄'
        ];
        
        return $icons[$extension] ?? '📄';
    }
}
