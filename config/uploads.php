<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Настройки для загрузки файлов в приложении
    |
    */

    'max_file_size_kb' => env('MAX_FILE_SIZE_KB', 51200), // 50MB в килобайтах
    'max_file_size_mb' => env('MAX_FILE_SIZE_MB', 50), // 50MB в мегабайтах

    'allowed_types' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'video' => ['mp4', 'mov', 'avi', 'mkv', 'webm'],
        'audio' => ['mp3', 'wav', 'm4a', 'ogg'], // Только для отображения существующих файлов
        'document' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'csv', 'json', 'xml', 'zip', 'rar', '7z'],
    ],

    'mime_types' => [
        // Images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        // Videos
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm',
        // Audio (только для отображения существующих файлов)
        'audio/mpeg', 'audio/wav', 'audio/mp4', 'audio/ogg',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv',
        'application/json', 'application/xml',
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
    ],
];
