@props(['message', 'lastSystemMessage'])

@if($message['sender_name'] === 'Система')
    <!-- Системное сообщение (по центру) -->
    <div class="flex justify-center mb-4 @if($message['id'] == $lastSystemMessage['id']) sticky top-10 z-10 bg-white dark:bg-gray-900 py-2 border-b border-gray-200 dark:border-gray-600 @endif system-message">
        <div class="bg-gray-100 dark:bg-blue-900/30 rounded-lg px-4 py-2 max-w-md border border-gray-200 dark:border-blue-500/30 shadow-sm">
            <div class="flex items-center space-x-2 mb-1">
                <svg class="h-4 w-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-gray-500 dark:text-blue-300">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
            </div>
            <p class="text-sm text-gray-600 dark:text-blue-200 text-left">{!! nl2br(e($message['content'])) !!}</p>
        </div>
    </div>
@elseif($message['is_from_client'])
    <!-- Сообщение от клиента (слева) -->
    <div class="flex items-start space-x-3 group mb-4" data-message-id="{{ $message['id'] }}" data-message-content="{{ $message['content'] }}" data-message-time="{{ $message['created_at'] }}">
        <div class="flex-shrink-0">
            <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-white">К</span>
            </div>
        </div>
        <div class="flex-1 relative">
            <!-- Имя автора сверху -->
            <div class="flex items-center space-x-2 mb-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $message['sender_name'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
            </div>
            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                @if($message['type'] === 'image' && $message['image_data'])
                    <!-- Изображение -->
                    <div class="mb-2">
                        <a href="{{ $message['image_data']['url'] }}" 
                           data-lightbox="chat-images" 
                           data-title="{{ $message['content'] !== 'Изображение' ? $message['content'] : 'Изображение' }}">
                            <img src="{{ $message['image_data']['url'] }}" 
                                 alt="Изображение" 
                                 class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                 style="max-height: 300px;">
                        </a>
                    </div>
                @elseif($message['type'] === 'video' && $message['video_data'])
                    <!-- Видео -->
                    <div class="mb-2">
                        <video controls 
                               class="max-w-full h-auto rounded-lg"
                               style="max-height: 300px;">
                            <source src="{{ $message['video_data']['url'] }}" type="video/{{ $message['video_data']['extension'] }}">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                @elseif($message['type'] === 'sticker' && $message['sticker_data'])
                    <!-- Стикер -->
                    <div class="mb-2">
                        <img src="{{ $message['sticker_data']['url'] }}" 
                             alt="Стикер" 
                             class="max-w-32 h-auto rounded-lg">
                    </div>
                @elseif($message['type'] === 'document' && $message['document_data'])
                    <!-- Документ -->
                    <div class="mb-2 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $message['document_data']['name'] }}</p>
                                <a href="{{ $message['document_data']['url'] }}" 
                                   target="_blank"
                                   class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    Скачать документ
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($message['type'] === 'audio' && $message['audio_data'])
                    <!-- Аудио -->
                    <div class="mb-2">
                        <audio controls class="w-full">
                            <source src="{{ $message['audio_data']['url'] }}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                    </div>
                @elseif($message['type'] === 'location' && $message['location_data'])
                    <!-- Локация -->
                    <div class="mb-2 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $message['location_data']['address'] ?? 'Локация' }}
                                </p>
                                <a href="https://maps.google.com/?q={{ $message['location_data']['latitude'] }},{{ $message['location_data']['longitude'] }}" 
                                   target="_blank"
                                   class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    Открыть на карте
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                @if($message['content'] && $message['content'] !== 'Изображение' && $message['content'] !== 'Видео' && $message['content'] !== 'Стикер' && $message['content'] !== 'Аудио сообщение')
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $message['content'] }}</p>
                @endif
            </div>
        </div>
    </div>
@else
    <!-- Сообщение от менеджера (справа) -->
    <div class="flex items-start space-x-3 justify-end group mb-4" data-message-id="{{ $message['id'] }}" data-message-content="{{ $message['content'] }}" data-message-time="{{ $message['created_at'] }}">
        <div class="flex-1 relative">
            <!-- Имя автора сверху -->
            <div class="flex items-center space-x-2 mb-1 justify-end">
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $message['sender_name'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
            </div>
            <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
                @if($message['type'] === 'image' && $message['image_data'])
                    <!-- Изображение -->
                    <div class="mb-2">
                        <a href="{{ $message['image_data']['url'] }}" 
                           data-lightbox="chat-images" 
                           data-title="{{ $message['content'] !== 'Изображение' ? $message['content'] : 'Изображение' }}">
                            <img src="{{ $message['image_data']['url'] }}" 
                                 alt="Изображение" 
                                 class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                 style="max-height: 300px;">
                        </a>
                    </div>
                @elseif($message['type'] === 'video' && $message['video_data'])
                    <!-- Видео -->
                    <div class="mb-2">
                        <video controls 
                               class="max-w-full h-auto rounded-lg"
                               style="max-height: 300px;">
                            <source src="{{ $message['video_data']['url'] }}" type="video/{{ $message['video_data']['extension'] }}">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                @elseif($message['type'] === 'sticker' && $message['sticker_data'])
                    <!-- Стикер -->
                    <div class="mb-2">
                        <img src="{{ $message['sticker_data']['url'] }}" 
                             alt="Стикер" 
                             class="max-w-32 h-auto rounded-lg">
                    </div>
                @elseif($message['type'] === 'document' && $message['document_data'])
                    <!-- Документ -->
                    <div class="mb-2 p-3 bg-blue-400 dark:bg-blue-600 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white">{{ $message['document_data']['name'] }}</p>
                                <a href="{{ $message['document_data']['url'] }}" 
                                   target="_blank"
                                   class="text-sm text-blue-100 hover:text-blue-200">
                                    Скачать документ
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($message['type'] === 'audio' && $message['audio_data'])
                    <!-- Аудио -->
                    <div class="mb-2">
                        <audio controls class="w-full">
                            <source src="{{ $message['audio_data']['url'] }}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                    </div>
                @elseif($message['type'] === 'location' && $message['location_data'])
                    <!-- Локация -->
                    <div class="mb-2 p-3 bg-blue-400 dark:bg-blue-600 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-6 w-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white">
                                    {{ $message['location_data']['address'] ?? 'Локация' }}
                                </p>
                                <a href="https://maps.google.com/?q={{ $message['location_data']['latitude'] }},{{ $message['location_data']['longitude'] }}" 
                                   target="_blank"
                                   class="text-sm text-blue-100 hover:text-blue-200">
                                    Открыть на карте
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                @if($message['content'] && $message['content'] !== 'Изображение' && $message['content'] !== 'Видео' && $message['content'] !== 'Стикер' && $message['content'] !== 'Аудио сообщение')
                    <p class="text-sm text-white whitespace-pre-line">{{ $message['content'] }}</p>
                @endif
            </div>
        </div>
        <div class="flex-shrink-0">
            <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-white">М</span>
            </div>
        </div>
    </div>
@endif
