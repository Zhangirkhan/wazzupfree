<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Загрузка файла на сервер
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // Максимум 50MB в KB
        ]);

        try {
            $file = $request->file('file');
            
            // Генерируем уникальное имя файла
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Определяем тип файла и папку для сохранения
            $fileType = $this->getFileType($file);
            $folder = $this->getFolderByType($fileType);
            
            // Сохраняем файл
            $path = $file->storeAs($folder, $fileName, 'public');
            
            // Генерируем публичный URL
            $publicUrl = Storage::disk('public')->url($path);
            
            Log::info('File uploaded successfully', [
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'public_url' => $publicUrl,
                'file_type' => $fileType,
                'file_size' => $file->getSize()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно загружен',
                'data' => [
                    'url' => $publicUrl,
                    'path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $fileType,
                    'mime_type' => $file->getMimeType()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файла: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Определение типа файла
     */
    private function getFileType($file): string
    {
        $mimeType = $file->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'document';
        }
    }

    /**
     * Получение папки для сохранения по типу файла
     */
    private function getFolderByType(string $fileType): string
    {
        return match ($fileType) {
            'image' => 'uploads/images',
            'video' => 'uploads/videos',
            'audio' => 'uploads/audio',
            'document' => 'uploads/documents',
            default => 'uploads/files'
        };
    }

    /**
     * Удаление файла
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        try {
            $path = $request->input('path');
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                Log::info('File deleted successfully', ['path' => $path]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Файл успешно удален'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'path' => $request->input('path')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления файла: ' . $e->getMessage()
            ], 500);
        }
    }
}
