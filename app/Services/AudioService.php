<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class AudioService
{
    /**
     * Сохранение аудио из URL (для входящих сообщений)
     */
    public function saveAudioFromUrl(string $url, int $chatId): ?array
    {
        try {
            Log::info('Saving audio from URL', ['url' => $url, 'chat_id' => $chatId]);

            $audioContent = file_get_contents($url);
            if (!$audioContent) {
                Log::error('Failed to download audio content', ['url' => $url]);
                return null;
            }

            $extension = $this->getExtensionFromUrl($url) ?: 'mp3';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'chat-audio/' . date('Y/m/d/') . $filename;

            $saved = Storage::disk('public')->put($path, $audioContent);
            if (!$saved) {
                Log::error('Failed to save audio to storage', ['path' => $path]);
                return null;
            }

            $size = Storage::disk('public')->size($path);
            $publicUrl = config('app.url') . '/storage/' . $path;

            Log::info('Audio saved successfully', [
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
            Log::error('Error saving audio from URL', [
                'url' => $url,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Сохранение аудио из загруженного файла (для менеджеров)
     */
    public function saveAudioFromFile(UploadedFile $file, int $chatId): ?array
    {
        try {
            Log::info('Saving audio from file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'chat_id' => $chatId
            ]);

            if (!$this->isValidAudio($file)) {
                Log::error('Invalid audio file type', [
                    'mime_type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName()
                ]);
                return null;
            }

            $extension = $file->getClientOriginalExtension() ?: 'mp3';
            $filename = 'chat_' . $chatId . '_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'chat-audio/' . date('Y/m/d/') . $filename;

            $saved = Storage::disk('public')->putFileAs(
                dirname($path),
                $file,
                basename($path)
            );

            if (!$saved) {
                Log::error('Failed to save audio file to storage', ['path' => $path]);
                return null;
            }

            $size = Storage::disk('public')->size($path);
            $publicUrl = config('app.url') . '/storage/' . $path;

            Log::info('Audio file saved successfully', [
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
            Log::error('Error saving audio file', [
                'original_name' => $file->getClientOriginalName(),
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function getExtensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            return $extension ? strtolower($extension) : null;
        }
        return null;
    }

    protected function isValidAudio(UploadedFile $file): bool
    {
        $allowedMimes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/ogg',
            'audio/m4a',
            'audio/aac',
            'audio/webm'
        ];
        return in_array($file->getMimeType(), $allowedMimes);
    }
}


