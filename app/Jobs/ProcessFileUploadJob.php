<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $filePath,
        private int $messageId,
        private string $originalName
    ) {}

    public function handle(): void
    {
        try {
            // Обработка файла (сжатие, создание превью, сканирование на вирусы и т.д.)
            $this->processFile();
            
            Log::info('File processed successfully', [
                'file_path' => $this->filePath,
                'message_id' => $this->messageId,
                'original_name' => $this->originalName
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process file', [
                'file_path' => $this->filePath,
                'message_id' => $this->messageId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    private function processFile(): void
    {
        // Здесь можно добавить:
        // - Сжатие изображений
        // - Создание превью
        // - Сканирование на вирусы
        // - Извлечение метаданных
        // - Конвертация форматов
        
        if (Storage::exists($this->filePath)) {
            // Пример: создание превью для изображений
            $extension = pathinfo($this->originalName, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->createThumbnail();
            }
        }
    }

    private function createThumbnail(): void
    {
        // Логика создания превью
        Log::info('Thumbnail created', ['file_path' => $this->filePath]);
    }
}
