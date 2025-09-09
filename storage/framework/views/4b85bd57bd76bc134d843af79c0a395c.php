<?php if (isset($component)) { $__componentOriginalc113672a4057e9d1a374a45c3d49bb0a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startSection('title', 'Чат'); ?>
    
    <!-- Подключаем Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    
    <!-- Подключаем Emoji CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-mart@latest/css/emoji-mart.css">
    
    <!-- Подключаем Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Подключаем jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Подключаем Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Стили для sticky элементов */
        #messageInputContainer {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        #chatHeader {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        /* Стили для Select2 */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: white;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 12px;
            color: #374151;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
        }
        
        /* Темная тема для Select2 */
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151;
            border-color: #4b5563;
        }
        
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f9fafb;
        }
        
        .dark .select2-dropdown {
            background-color: #374151;
            border-color: #4b5563;
        }
        
        .dark .select2-container--default .select2-results__option {
            background-color: #374151;
            color: #f9fafb;
        }
        
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
        }
        
        /* Плавная прокрутка */
        #messagesContainer {
            scroll-behavior: smooth;
        }
        
        /* Стили для модальных окон */
        .modal-overlay {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        
        /* Анимация появления модальных окон */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .modal-content {
            animation: modalFadeIn 0.2s ease-out;
        }
        
        /* Стили для sticky системных сообщений */
        .sticky-system-message {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .dark .sticky-system-message {
            background: #111827;
            border-bottom: 1px solid #4b5563;
        }
        
        /* Плавная анимация для системных сообщений */
        .system-message {
            transition: all 0.3s ease;
        }
        
        /* Стили для не-sticky системных сообщений */
        .system-message:not(.sticky) {
            opacity: 0.8;
        }
        
        .system-message:not(.sticky):hover {
            opacity: 1;
        }
        
        /* Дополнительные стили для системных сообщений в темной теме */
        .dark .system-message {
            box-shadow: 0 1px 3px 0 rgba(59, 130, 246, 0.1);
        }
        
        .dark .system-message:hover {
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
    </style>
    
    <?php $__env->startSection('content'); ?>
    <div class="w-full" style="height: 700px;">
        <div class="h-full bg-white dark:bg-gray-800">
            <div class="flex h-full">
            <!-- Контакты слева -->
            <div class="w-64 border-r border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                <!-- Заголовок контактов -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Чаты</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Активные диалоги</p>
                        </div>
                        <button id="addChatBtn" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Поиск -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Поиск чатов..." 
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Список контактов -->
                <div class="flex-1 overflow-y-auto min-h-0" id="chatsList">
                    <?php $__empty_1 = true; $__currentLoopData = $chatsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600 chat-item <?php echo e(request('chat') == $chat['id'] ? 'bg-blue-50 dark:bg-blue-900/20' : ''); ?>" data-chat-id="<?php echo e($chat['id']); ?>">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 <?php echo e($chat['is_online'] ? 'bg-green-500' : 'bg-blue-500'); ?> rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white"><?php echo e($chat['avatar_text']); ?></span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($chat['title']); ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate"><?php echo e($chat['last_message_preview']); ?></p>
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end">
                                    <?php if($chat['last_message_time']): ?>
                                        <span class="text-xs text-gray-400"><?php echo e($chat['last_message_time']->format('H:i')); ?></span>
                                    <?php endif; ?>
                                    <?php if($chat['unread_count'] > 0): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1 unread-badge">
                                            <?php echo e($chat['unread_count']); ?>

                                        </span>
                                    <?php elseif($chat['status'] === 'active' && !$chat['assigned_to']): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 mt-1">
                                            Новый
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-4 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm">Чатов пока нет</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Окно чата справа -->
            <div class="flex-1 flex flex-col min-h-0" id="chatWindow">
                <!-- Заголовок чата -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10" id="chatHeader">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white" id="chatAvatar">
                                <?php if($currentClient): ?>
                                    <?php echo e(strtoupper(substr($currentClient['name'], 0, 1))); ?>

                                <?php elseif($currentChat): ?>
                                    К
                                <?php else: ?>
                                    К
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="chatTitle">
                                <?php if($currentClient): ?>
                                    <?php echo e($currentClient['name']); ?>

                                <?php elseif($currentChat): ?>
                                    <?php echo e($currentChat['title']); ?>

                                <?php else: ?>
                                    Выберите чат
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" id="chatStatus">
                                <?php if($currentChat): ?>
                                    <?php echo e($currentChat['status'] === 'active' ? 'Онлайн' : 'Оффлайн'); ?>

                                <?php else: ?>
                                    Начните диалог
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <!-- Кнопка переключения отдела -->
                            <button id="transferBtn" 
                                    class="p-2 text-green-400 hover:text-green-600 dark:hover:text-green-300 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200" 
                                    title="Переключить отдел">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </button>
                            <!-- Кнопка истории -->
                            <button id="historyBtn" 
                                    class="p-2 text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200" 
                                    title="История чата">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                            <!-- Кнопка завершения диалога -->
                            <button id="endChatBtn" 
                                    class="p-2 text-red-400 hover:text-red-600 dark:hover:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200" 
                                    style="display: block;"
                                    title="Завершить диалог">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Сообщения -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0 pb-20" id="messagesContainer">
                    <?php if($currentChat && count($currentMessages) > 0): ?>
                        <?php
                            $systemMessages = $currentMessages->where('sender_name', 'Система');
                            $lastSystemMessage = $systemMessages->last();
                        ?>
                        
                        <?php $__currentLoopData = $currentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($message['sender_name'] === 'Система'): ?>
                                <!-- Системное сообщение (по центру) -->
                                <div class="flex justify-center mb-4 <?php if($message['id'] == $lastSystemMessage['id']): ?> sticky top-10 z-10 bg-white dark:bg-gray-900 py-2 border-b border-gray-200 dark:border-gray-600 <?php endif; ?> system-message">
                                    <div class="bg-gray-100 dark:bg-blue-900/30 rounded-lg px-4 py-2 max-w-md border border-gray-200 dark:border-blue-500/30 shadow-sm">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <svg class="h-4 w-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-xs text-gray-500 dark:text-blue-300"><?php echo e(\Carbon\Carbon::parse($message['created_at'])->format('H:i')); ?></p>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-blue-200 text-left"><?php echo nl2br(e($message['content'])); ?></p>
                                    </div>
                                </div>
                            <?php elseif($message['is_from_client']): ?>
                                <!-- Сообщение от клиента (слева) -->
                                <div class="flex items-start space-x-3 group mb-4" data-message-id="<?php echo e($message['id']); ?>" data-message-content="<?php echo e($message['content']); ?>" data-message-time="<?php echo e($message['created_at']); ?>">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">К</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 relative">
                                        <!-- Имя автора сверху -->
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e($message['sender_name']); ?></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($message['created_at'])->format('H:i')); ?></p>
                                        </div>
                                        <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                                            <?php if($message['type'] === 'image' && $message['image_data']): ?>
                                                <!-- Изображение -->
                                                <div class="mb-2">
                                                    <a href="<?php echo e($message['image_data']['url']); ?>" 
                                                       data-lightbox="chat-images" 
                                                       data-title="<?php echo e($message['content'] !== 'Изображение' ? $message['content'] : 'Изображение'); ?>">
                                                        <img src="<?php echo e($message['image_data']['url']); ?>" 
                                                             alt="Изображение" 
                                                             class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                                             style="max-height: 300px;">
                                                    </a>
                                                </div>
                                            <?php elseif($message['type'] === 'video' && $message['video_data']): ?>
                                                <!-- Видео -->
                                                <div class="mb-2">
                                                    <video controls 
                                                           class="max-w-full h-auto rounded-lg"
                                                           style="max-height: 300px;">
                                                        <source src="<?php echo e($message['video_data']['url']); ?>" type="video/<?php echo e($message['video_data']['extension']); ?>">
                                                        Ваш браузер не поддерживает видео.
                                                    </video>
                                                </div>
                                            <?php elseif($message['type'] === 'sticker' && $message['sticker_data']): ?>
                                                <!-- Стикер -->
                                                <div class="mb-2">
                                                    <img src="<?php echo e($message['sticker_data']['url']); ?>" 
                                                         alt="Стикер" 
                                                         class="max-w-32 h-auto rounded-lg">
                                                </div>
                                            <?php elseif($message['type'] === 'document' && $message['document_data']): ?>
                                                <!-- Документ -->
                                                <div class="mb-2 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($message['document_data']['name']); ?></p>
                                                            <a href="<?php echo e($message['document_data']['url']); ?>" 
                                                               target="_blank"
                                                               class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                                Скачать документ
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php elseif($message['type'] === 'audio' && $message['audio_data']): ?>
                                                <!-- Аудио -->
                                                <div class="mb-2">
                                                    <audio controls class="w-full">
                                                        <source src="<?php echo e($message['audio_data']['url']); ?>" type="audio/mpeg">
                                                        Ваш браузер не поддерживает аудио.
                                                    </audio>
                                                </div>
                                            <?php elseif($message['type'] === 'location' && $message['location_data']): ?>
                                                <!-- Локация -->
                                                <div class="mb-2 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                <?php echo e($message['location_data']['address'] ?? 'Локация'); ?>

                                                            </p>
                                                            <a href="https://maps.google.com/?q=<?php echo e($message['location_data']['latitude']); ?>,<?php echo e($message['location_data']['longitude']); ?>" 
                                                               target="_blank"
                                                               class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                                Открыть на карте
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($message['content'] && $message['content'] !== 'Изображение' && $message['content'] !== 'Видео' && $message['content'] !== 'Стикер' && $message['content'] !== 'Аудио сообщение'): ?>
                                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line"><?php echo e($message['content']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Сообщение от менеджера (справа) -->
                                <div class="flex items-start space-x-3 justify-end group mb-4" data-message-id="<?php echo e($message['id']); ?>" data-message-content="<?php echo e($message['content']); ?>" data-message-time="<?php echo e($message['created_at']); ?>">
                                    <div class="flex-1 relative">
                                        <!-- Имя автора сверху -->
                                        <div class="flex items-center space-x-2 mb-1 justify-end">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e($message['sender_name']); ?></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($message['created_at'])->format('H:i')); ?></p>
                                        </div>
                                        <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
                                            <?php if($message['type'] === 'image' && $message['image_data']): ?>
                                                <!-- Изображение -->
                                                <div class="mb-2">
                                                    <a href="<?php echo e($message['image_data']['url']); ?>" 
                                                       data-lightbox="chat-images" 
                                                       data-title="<?php echo e($message['content'] !== 'Изображение' ? $message['content'] : 'Изображение'); ?>">
                                                        <img src="<?php echo e($message['image_data']['url']); ?>" 
                                                             alt="Изображение" 
                                                             class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                                             style="max-height: 300px;">
                                                    </a>
                                                </div>
                                            <?php elseif($message['type'] === 'video' && $message['video_data']): ?>
                                                <!-- Видео -->
                                                <div class="mb-2">
                                                    <video controls 
                                                           class="max-w-full h-auto rounded-lg"
                                                           style="max-height: 300px;">
                                                        <source src="<?php echo e($message['video_data']['url']); ?>" type="video/<?php echo e($message['video_data']['extension']); ?>">
                                                        Ваш браузер не поддерживает видео.
                                                    </video>
                                                </div>
                                            <?php elseif($message['type'] === 'sticker' && $message['sticker_data']): ?>
                                                <!-- Стикер -->
                                                <div class="mb-2">
                                                    <img src="<?php echo e($message['sticker_data']['url']); ?>" 
                                                         alt="Стикер" 
                                                         class="max-w-32 h-auto rounded-lg">
                                                </div>
                                            <?php elseif($message['type'] === 'document' && $message['document_data']): ?>
                                                <!-- Документ -->
                                                <div class="mb-2 p-3 bg-blue-400 dark:bg-blue-600 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-white"><?php echo e($message['document_data']['name']); ?></p>
                                                            <a href="<?php echo e($message['document_data']['url']); ?>" 
                                                               target="_blank"
                                                               class="text-sm text-blue-100 hover:text-blue-200">
                                                                Скачать документ
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php elseif($message['type'] === 'audio' && $message['audio_data']): ?>
                                                <!-- Аудио -->
                                                <div class="mb-2">
                                                    <audio controls class="w-full">
                                                        <source src="<?php echo e($message['audio_data']['url']); ?>" type="audio/mpeg">
                                                        Ваш браузер не поддерживает аудио.
                                                    </audio>
                                                </div>
                                            <?php elseif($message['type'] === 'location' && $message['location_data']): ?>
                                                <!-- Локация -->
                                                <div class="mb-2 p-3 bg-blue-400 dark:bg-blue-600 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="h-6 w-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-white">
                                                                <?php echo e($message['location_data']['address'] ?? 'Локация'); ?>

                                                            </p>
                                                            <a href="https://maps.google.com/?q=<?php echo e($message['location_data']['latitude']); ?>,<?php echo e($message['location_data']['longitude']); ?>" 
                                                               target="_blank"
                                                               class="text-sm text-blue-100 hover:text-blue-200">
                                                                Открыть на карте
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($message['content'] && $message['content'] !== 'Изображение' && $message['content'] !== 'Видео' && $message['content'] !== 'Стикер' && $message['content'] !== 'Аудио сообщение'): ?>
                                                <p class="text-sm text-white whitespace-pre-line"><?php echo e($message['content']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">М</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php elseif($currentChat): ?>
                        <div class="text-center py-12">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm">Начните диалог</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm">Выберите чат для начала диалога</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Поле ввода -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky bottom-0 z-10" id="messageInputContainer" style="display: <?php echo e($currentChat ? 'block' : 'none'); ?>;">
                    <form id="messageForm" class="flex items-center space-x-3">
                        <!-- Кнопка загрузки изображения -->
                        <label for="imageUpload" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </label>
                        <input type="file" 
                               id="imageUpload" 
                               accept="image/*" 
                               class="hidden" 
                               onchange="handleImageUpload(event)">
                        
                        <!-- Кнопка загрузки видео -->
                        <label for="videoUpload" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </label>
                        <input type="file" 
                               id="videoUpload" 
                               accept="video/*" 
                               class="hidden" 
                               onchange="handleVideoUpload(event)">
                        
                        <!-- Кнопка эмодзи -->
                        <button type="button" 
                                onclick="toggleEmojiPicker()"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        
                        <!-- Кнопка шаблонов ответов -->
                        <button type="button" 
                                onclick="toggleTemplatesPicker()"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </button>
                        
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                        <div class="flex-1">
                            <input type="text" 
                                   id="messageInput"
                                   placeholder="Введите сообщение..." 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        <button type="submit" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для создания чата -->
    <div id="addChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Начать новый чат</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="createChatForm">
                    <div class="mb-4">
                        <label for="clientSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Выберите клиента
                        </label>
                        <select id="clientSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Начните вводить имя или телефон...</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="initialMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Начальное сообщение (необязательно)
                        </label>
                        <textarea id="initialMessage" rows="3" placeholder="Введите сообщение..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            Создать чат
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно подтверждения завершения диалога -->
    <div id="endChatConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Завершить диалог</h3>
                    <button id="closeEndChatModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Подтверждение завершения</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Это действие нельзя отменить</p>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Что произойдет:</h5>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Клиент получит сообщение о завершении</li>
                                        <li>Диалог будет помечен как завершенный</li>
                                        <li>Клиент сможет продолжить общение, написав 1 или 0</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button id="cancelEndChatBtn" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Отмена
                    </button>
                    <button id="confirmEndChatBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Завершить диалог</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Начать новый чат</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="createChatForm">
                    <div class="mb-4">
                        <label for="clientSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Выберите клиента
                        </label>
                        <select id="clientSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Начните вводить имя или телефон...</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="initialMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Начальное сообщение (необязательно)
                        </label>
                        <textarea id="initialMessage" rows="3" placeholder="Введите сообщение..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            Создать чат
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM загружен, инициализация элементов');
            
            const searchInput = document.getElementById('searchInput');
            const chatsList = document.getElementById('chatsList');
            const addChatBtn = document.getElementById('addChatBtn');
            const addChatModal = document.getElementById('addChatModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const createChatForm = document.getElementById('createChatForm');
            const clientSelect = document.getElementById('clientSelect');
            
            // Проверяем существование элементов
            if (!addChatBtn) {
                console.error('Элемент addChatBtn не найден');
                return;
            }
            if (!addChatModal) {
                console.error('Элемент addChatModal не найден');
                return;
            }
            if (!clientSelect) {
                console.error('Элемент clientSelect не найден');
                return;
            }
            
            console.log('Все элементы найдены успешно');
            
            // Проверяем jQuery
            if (typeof $ === 'undefined') {
                console.error('jQuery не загружен');
                return;
            }
            console.log('jQuery загружен успешно');
            
            const messageForm = document.getElementById('messageForm');
            const messageInput = document.getElementById('messageInput');
            const messagesContainer = document.getElementById('messagesContainer');
            const messageInputContainer = document.getElementById('messageInputContainer');
            const chatTitle = document.getElementById('chatTitle');
            const chatStatus = document.getElementById('chatStatus');
            const chatAvatar = document.getElementById('chatAvatar');
            const endChatBtn = document.getElementById('endChatBtn');



            let searchTimeout;
            let currentChatId = null;

            // Функция поиска чатов
            function searchChats(query) {
                fetch('<?php echo e(route("user.chat.search")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ search: query })
                })
                .then(response => response.json())
                .then(data => {
                    updateChatsList(data);
                })
                .catch(error => {
                    console.error('Ошибка поиска:', error);
                });
            }

            // Обновление списка чатов
            function updateChatsList(chats) {
                if (chats.length === 0) {
                    chatsList.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p class="text-sm">Чаты не найдены</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                chatsList.innerHTML = chats.map(chat => `
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600 chat-item" data-chat-id="${chat.id}">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 ${chat.is_online ? 'bg-green-500' : 'bg-blue-500'} rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">${chat.avatar_text}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${chat.title}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${chat.last_message_preview}</p>
                            </div>
                            <div class="flex-shrink-0 flex flex-col items-end">
                                ${chat.last_message_time ? `<span class="text-xs text-gray-400">${new Date(chat.last_message_time).toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'})}</span>` : ''}
                                ${chat.unread_count > 0 ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mt-1">${chat.unread_count}</span>` : ''}
                                ${chat.status === 'active' && !chat.assigned_to ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 mt-1">Новый</span>` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // Обработчик ввода в поле поиска
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length === 0) {
                    // Если поле пустое, загружаем все чаты
                    window.location.reload();
                    return;
                }

                // Задержка поиска для оптимизации
                searchTimeout = setTimeout(() => {
                    searchChats(query);
                }, 300);
            });

            // Обработчик клика по чату
            chatsList.addEventListener('click', function(e) {
                const chatItem = e.target.closest('.chat-item');
                if (chatItem) {
                    const chatId = chatItem.dataset.chatId;
                    // Переходим на страницу с GET параметром
                    window.location.href = `<?php echo e(route('user.chat.index')); ?>?chat=${chatId}`;
                }
            });

            // Инициализация текущего чата при загрузке страницы
            <?php if($currentChat): ?>
                currentChatId = <?php echo e($currentChat['id']); ?>;
                // Показываем кнопку завершения диалога для менеджеров и руководителей
                showEndChatButton();
                
                // Автоматический скролл к последнему сообщению при загрузке
                setTimeout(() => {
                    scrollToBottomSmooth();
                }, 100);
            <?php endif; ?>

            // Переменные для real-time обновлений
            let lastMessageId = null;
            let eventSource = null;
            let reconnectAttempts = 0;
            const maxReconnectAttempts = 5;
            
            // Функция для подключения к SSE
            function connectToSSE() {
                if (!currentChatId) {
                    console.log('currentChatId не установлен');
                    return;
                }
                
                // Закрываем предыдущее соединение если есть
                if (eventSource) {
                    eventSource.close();
                }
                
                console.log('Подключение к SSE для чата:', currentChatId);
                
                // Формируем URL для SSE
                let url = `<?php echo e(url('/user/chat/stream')); ?>/${currentChatId}`;
                if (lastMessageId) {
                    url += `?last_message_id=${lastMessageId}`;
                }
                
                eventSource = new EventSource(url);
                
                eventSource.onopen = function(event) {
                    console.log('SSE соединение установлено');
                    reconnectAttempts = 0;
                };
                
                eventSource.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        console.log('Получено SSE событие:', data.type);
                        
                        switch (data.type) {
                            case 'connected':
                                console.log('Подключен к чату:', data.chat_id);
                                break;
                                
                            case 'new_message':
                                handleNewMessage(data.message);
                                break;
                                
                            case 'chat_updated':
                                handleChatUpdate(data.chat);
                                break;
                                
                            case 'ping':
                                // Просто поддерживаем соединение
                                break;
                                
                            case 'timeout':
                                console.log('SSE соединение завершено по таймауту');
                                eventSource.close();
                                break;
                                
                            case 'error':
                                console.error('SSE ошибка:', data.message);
                                eventSource.close();
                                break;
                        }
                    } catch (error) {
                        console.error('Ошибка парсинга SSE данных:', error);
                    }
                };
                
                eventSource.onerror = function(event) {
                    console.error('SSE ошибка соединения:', event);
                    eventSource.close();
                    
                    // Попытка переподключения
                    if (reconnectAttempts < maxReconnectAttempts) {
                        reconnectAttempts++;
                        console.log(`Попытка переподключения ${reconnectAttempts}/${maxReconnectAttempts}`);
                        setTimeout(() => {
                            connectToSSE();
                        }, 2000 * reconnectAttempts); // Экспоненциальная задержка
                    } else {
                        console.error('Превышено максимальное количество попыток переподключения');
                        // Fallback на polling
                        startPolling();
                    }
                };
            }
            
            // Обработка нового сообщения
            function handleNewMessage(message) {
                const messagesContainer = document.getElementById('messagesContainer');
                const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                const tempMessage = document.querySelector(`[data-temp="true"]`);
                
                if (!existingMessage && !tempMessage) {
                    addMessageToInterface(message);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    console.log('Добавлено новое сообщение через SSE:', message.id, message.sender_name);
                }
                
                lastMessageId = message.id;
            }
            
            // Обработка обновления чата
            function handleChatUpdate(chat) {
                console.log('Чат обновлен:', chat);
                // Здесь можно обновить статус чата, информацию о назначенном менеджере и т.д.
            }
            
            // Fallback на polling если SSE не работает
            function startPolling() {
                console.log('Переключение на polling режим');
                autoRefreshInterval = setInterval(fetchNewMessages, 3000);
                fetchNewMessages();
            }
            
            // Функция для получения новых сообщений (fallback)
            function fetchNewMessages() {
                console.log('fetchNewMessages вызван, currentChatId:', currentChatId, 'lastMessageId:', lastMessageId);
                if (!currentChatId) {
                    console.log('currentChatId не установлен');
                    return;
                }
                
                // Формируем URL с параметром last_message_id
                let url = `<?php echo e(url('/user/chat/messages')); ?>/${currentChatId}`;
                if (lastMessageId) {
                    url += `?last_message_id=${lastMessageId}`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const messagesContainer = document.getElementById('messagesContainer');
                            let hasNewMessages = false;
                            
                            data.messages.forEach(message => {
                                // Проверяем, есть ли уже это сообщение по ID
                                const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                                
                                // Проверяем, есть ли сообщение с таким же текстом и временем (дополнительная проверка)
                                const existingByContent = document.querySelector(`[data-message-content="${message.content}"][data-message-time="${message.created_at}"]`);
                                
                                const tempMessage = document.querySelector(`[data-temp="true"]`);
                                
                                // Не добавляем, если есть временное сообщение или уже есть это сообщение
                                if (!existingMessage && !existingByContent && !tempMessage) {
                                    hasNewMessages = true;
                                    addMessageToInterface(message);
                                    console.log('Добавлено новое сообщение:', message.id, message.sender_name);
                                } else {
                                    console.log('Сообщение уже существует или есть временное:', message.id, message.sender_name);
                                }
                            });
                            
                            if (hasNewMessages) {
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                                console.log('Добавлены новые сообщения:', data.messages.length);
                            }
                            
                            // Обновляем ID последнего сообщения
                            if (data.last_message_id) {
                                lastMessageId = data.last_message_id;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка получения сообщений:', error);
                    });
            }
            
            // Функция для добавления сообщения в интерфейс
            function addMessageToInterface(message) {
                const messagesContainer = document.getElementById('messagesContainer');
                const currentTime = new Date(message.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                
                let messageHtml = '';
                
                if (message.sender_name === 'Система') {
                    // Убираем sticky с предыдущих системных сообщений
                    const existingSystemMessages = messagesContainer.querySelectorAll('.system-message');
                    existingSystemMessages.forEach(msg => {
                        msg.classList.remove('sticky', 'top-10', 'z-10', 'bg-white', 'dark:bg-gray-900', 'py-2', 'border-b', 'border-gray-200', 'dark:border-gray-600');
                    });
                    
                    // Заменяем \n на <br> для правильного отображения переносов строк
                    const formattedContent = message.content.replace(/\n/g, '<br>');
                    messageHtml = `
                        <div class="flex justify-center mb-4 sticky top-10 z-10 bg-white dark:bg-gray-900 py-2 border-b border-gray-200 dark:border-gray-600 system-message">
                            <div class="bg-gray-100 dark:bg-blue-900/30 rounded-lg px-4 py-2 max-w-md border border-gray-200 dark:border-blue-500/30 shadow-sm">
                                <div class="flex items-center space-x-2 mb-1">
                                    <svg class="h-4 w-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-gray-500 dark:text-blue-300">${currentTime}</p>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-blue-200 text-left">${formattedContent}</p>
                            </div>
                        </div>
                    `;
                } else if (message.is_from_client) {
                    messageHtml = `
                        <div class="flex items-start space-x-3 justify-start mb-4" data-message-id="${message.id}" data-message-content="${message.content}" data-message-time="${message.created_at}">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-white">К</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <!-- Имя автора сверху -->
                                <div class="flex items-center space-x-2 mb-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${message.sender_name}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">${message.content}</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    messageHtml = `
                        <div class="flex items-start space-x-3 justify-end mb-4" data-message-id="${message.id}" data-message-content="${message.content}" data-message-time="${message.created_at}">
                            <div class="flex-1">
                                <!-- Имя автора сверху -->
                                <div class="flex items-center space-x-2 mb-1 justify-end">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${message.sender_name}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                                </div>
                                <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
                                    <p class="text-sm text-white whitespace-pre-line">${message.content}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-white">${message.sender_avatar || 'М'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                
                // Автоматический скролл к новому сообщению
                setTimeout(() => {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }, 50);
            }
            
            // Функция для запуска real-time обновлений
            function startAutoRefresh() {
                // Останавливаем предыдущие соединения
                stopAutoRefresh();
                
                // Пытаемся подключиться к SSE
                connectToSSE();
            }
            
            // Функция для остановки real-time обновлений
            function stopAutoRefresh() {
                // Закрываем SSE соединение
                if (eventSource) {
                    eventSource.close();
                    eventSource = null;
                }
                
                // Останавливаем polling если он запущен
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
            }
            
            // Функция удаления сообщения
            function deleteMessage(messageId) {
                if (!confirm('Вы уверены, что хотите удалить это сообщение?')) {
                    return;
                }
                
                fetch(`<?php echo e(url('/user/chat/messages')); ?>/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Удаляем сообщение из интерфейса
                        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                        if (messageElement) {
                            messageElement.remove(); // Удаляем сообщение (подпись теперь внутри)
                        }
                        console.log('Сообщение удалено');
                    } else {
                        console.error('Ошибка удаления:', data.error);
                        alert('Ошибка удаления сообщения: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Ошибка удаления сообщения');
                });
            }

            // Запускаем автообновление если есть текущий чат
            <?php if($currentChat): ?>
                console.log('Запускаем автообновление для чата:', <?php echo e($currentChat['id']); ?>);
                // Устанавливаем ID последнего сообщения при загрузке
                <?php if(count($currentMessages) > 0): ?>
                    lastMessageId = <?php echo e($currentMessages->last()['id']); ?>;
                    console.log('Установлен lastMessageId:', lastMessageId);
                <?php endif; ?>
                startAutoRefresh(); // Запускаем real-time обновления
            <?php else: ?>
                console.log('Текущий чат не найден');
            <?php endif; ?>



            // Отправка сообщения
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let content = messageInput.value.trim();
                if (!content || !currentChatId) {
                    console.log('Пустое сообщение или нет текущего чата');
                    return;
                }
                
                // Очищаем текст от некорректных символов
                content = content.replace(/[\x00-\x1F\x7F]/g, ''); // Удаляем управляющие символы
                content = content.replace(/\uFFFD/g, ''); // Удаляем символы замены UTF-8
                
                console.log('Отправка сообщения:', content, 'в чат:', currentChatId);
                
                // Очищаем поле ввода
                messageInput.value = '';
                
                // Показываем индикатор отправки
                const sendingIndicator = document.createElement('div');
                sendingIndicator.className = 'flex items-center justify-end space-x-2 mb-2';
                sendingIndicator.innerHTML = `
                    <div class="flex items-center space-x-2 text-gray-500 text-sm">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-500"></div>
                        <span>Отправка...</span>
                    </div>
                `;
                messagesContainer.appendChild(sendingIndicator);
                scrollToBottomSmooth();
                
                // Отправляем сообщение на сервер
                fetch(`<?php echo e(url('/user/chat/send')); ?>/${currentChatId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(response => {
                    console.log('Ответ сервера:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Данные ответа:', data);
                    // Удаляем индикатор отправки
                    if (sendingIndicator && sendingIndicator.parentNode) {
                        sendingIndicator.remove();
                    }
                    
                    if (!data.success) {
                        console.error('Ошибка отправки сообщения:', data.error || data.message);
                        alert('Ошибка отправки сообщения: ' + (data.error || data.message));
                        // Возвращаем текст в поле ввода при ошибке
                        messageInput.value = content;
                    } else {
                        console.log('Сообщение отправлено успешно');
                        // SSE получит реальное сообщение и добавит его в интерфейс
                    }
                })
                .catch(error => {
                    console.error('Ошибка отправки сообщения:', error);
                    alert('Ошибка отправки сообщения: ' + error.message);
                    // Удаляем индикатор отправки при ошибке
                    if (sendingIndicator && sendingIndicator.parentNode) {
                        sendingIndicator.remove();
                    }
                    // Возвращаем текст в поле ввода при ошибке
                    messageInput.value = content;
                });
            });

            // Модальное окно
            function openModal() {
                addChatModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                addChatModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                createChatForm.reset();
                if (window.clientSelect2) {
                    window.clientSelect2.destroy();
                    window.clientSelect2 = null;
                }
            }

            // Открытие модального окна
            addChatBtn.addEventListener('click', function() {
                console.log('Кнопка добавления чата нажата');
                openModal();
                // Инициализируем Select2 после открытия модального окна
                setTimeout(() => {
                    if (!window.clientSelect2) {
                        console.log('Инициализация Select2');
                        try {
                            window.clientSelect2 = $(clientSelect).select2({
                                placeholder: 'Начните вводить имя или телефон...',
                                allowClear: true,
                                ajax: {
                                    url: '<?php echo e(route("user.chat.search-clients")); ?>',
                                    dataType: 'json',
                                    type: 'GET',
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            q: params.term,
                                            page: params.page
                                        };
                                    },
                                    processResults: function(data, params) {
                                        params.page = params.page || 1;
                                        return {
                                            results: data.results || data,
                                            pagination: {
                                                more: false
                                            }
                                        };
                                    },
                                    cache: true,
                                    error: function(xhr, status, error) {
                                        console.error('Ошибка AJAX в Select2:', error);
                                        console.error('Ответ сервера:', xhr.responseText);
                                    }
                                }
                            });
                            console.log('Select2 инициализирован успешно');
                        } catch (error) {
                            console.error('Ошибка инициализации Select2:', error);
                        }
                    }
                }, 100);
            });

            // Закрытие модального окна
            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);

            // Закрытие по клику вне модального окна
            addChatModal.addEventListener('click', function(e) {
                if (e.target === addChatModal) {
                    closeModal();
                }
            });

            // Функция для показа кнопки завершения диалога
            function showEndChatButton() {
                // Проверяем, является ли пользователь менеджером или руководителем
                const userRole = '<?php echo e(Auth::user()->role); ?>';
                const userPosition = '<?php echo e(Auth::user()->position); ?>';
                
                const isManager = userRole === 'admin' || 
                                 userRole === 'manager' || 
                                 userPosition.toLowerCase().includes('руководитель') ||
                                 userPosition.toLowerCase().includes('менеджер');
                
                if (isManager) {
                    endChatBtn.style.display = 'block';
                } else {
                    endChatBtn.style.display = 'none';
                }
            }

            // Обработчик кнопки завершения диалога
            endChatBtn.addEventListener('click', function() {
                if (!currentChatId) {
                    alert('Чат не выбран');
                    return;
                }

                // Простое решение - используем confirm
                if (confirm('Вы уверены, что хотите завершить этот диалог?')) {
                    endChat();
                }
            });





            // Функция завершения диалога
            function endChat() {
                const endMessage = 'Спасибо за обращение диалог будет завершен. Для продолжения напишите 1 или 0 для возврата в меню.';
                
                // Отправляем сообщение о завершении
                fetch(`<?php echo e(url('/user/chat/send')); ?>/${currentChatId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        content: endMessage
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем статус чата на завершенный
                        fetch(`<?php echo e(url('/user/chat/end')); ?>/${currentChatId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Диалог успешно завершен');
                                // Перезагружаем страницу для обновления статуса
                                window.location.reload();
                            } else {
                                alert('Ошибка при завершении диалога: ' + (data.error || 'Неизвестная ошибка'));
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка завершения диалога:', error);
                            alert('Ошибка при завершении диалога');
                        });
                    } else {
                        alert('Ошибка при отправке сообщения: ' + (data.error || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка отправки сообщения:', error);
                    alert('Ошибка при отправке сообщения');
                });
            }

            // Создание чата
            createChatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const clientId = clientSelect.value;
                const message = document.getElementById('initialMessage').value;

                if (!clientId) {
                    alert('Пожалуйста, выберите клиента');
                    return;
                }

                // Показываем индикатор загрузки
                const submitBtn = createChatForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Создание...';
                submitBtn.disabled = true;

                fetch('<?php echo e(route("user.chat.create")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        client_id: clientId,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        // Перезагружаем страницу для отображения нового чата
                        window.location.reload();
                    } else {
                        alert('Ошибка при создании чата: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Ошибка при создании чата');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });
        });

        // Обработчик кнопки переключения отдела
        const transferBtn = document.getElementById('transferBtn');
        if (transferBtn) {
            transferBtn.addEventListener('click', function() {
                if (!currentChatId) {
                    alert('Чат не выбран');
                    return;
                }
                showTransferModal();
            });
        }

        // Обработчик кнопки истории
        const historyBtn = document.getElementById('historyBtn');
        if (historyBtn) {
            historyBtn.addEventListener('click', function() {
                if (!currentChatId) {
                    alert('Чат не выбран');
                    return;
                }
                showHistoryModal();
            });
        }

        // Функция показа модального окна истории
        function showHistoryModal() {
            fetch(`<?php echo e(url('/user/chat/history')); ?>/${currentChatId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayHistory(data.history);
                    } else {
                        alert('Ошибка при получении истории: ' + (data.error || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка получения истории:', error);
                    alert('Ошибка при получении истории');
                });
        }

        // Функция отображения истории
        function displayHistory(history) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] modal-overlay';
            modal.id = 'historyModal';
            
            let historyHtml = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden modal-content">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">История чата</h3>
                        <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[60vh]">
            `;
            
            if (history.length === 0) {
                historyHtml += '<p class="text-gray-500 dark:text-gray-400 text-center">История пуста</p>';
            } else {
                historyHtml += '<div class="space-y-4">';
                history.forEach(item => {
                    const actionIcon = getActionIcon(item.action);
                    const actionColor = getActionColor(item.action);
                    
                    historyHtml += `
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 ${actionColor} rounded-full flex items-center justify-center">
                                    ${actionIcon}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${item.description}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${item.created_at}</p>
                                </div>
                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                    ${item.user_name ? `Пользователь: ${item.user_name}` : ''}
                                    ${item.department_name ? ` | Отдел: ${item.department_name}` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                historyHtml += '</div>';
            }
            
            historyHtml += `
                    </div>
                </div>
            `;
            
            modal.innerHTML = historyHtml;
            document.body.appendChild(modal);
        }

        // Функция закрытия модального окна истории
        function closeHistoryModal() {
            const modal = document.getElementById('historyModal');
            if (modal) {
                modal.remove();
            }
        }

        // Функция получения иконки для действия
        function getActionIcon(action) {
            switch(action) {
                case 'department_selected':
                    return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>';
                case 'assigned_to':
                    return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>';
                case 'completed':
                    return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                case 'reset':
                    return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
                default:
                    return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            }
        }

        // Функция получения цвета для действия
        function getActionColor(action) {
            switch(action) {
                case 'department_selected':
                    return 'bg-blue-500';
                case 'assigned_to':
                    return 'bg-green-500';
                case 'completed':
                    return 'bg-red-500';
                case 'reset':
                    return 'bg-yellow-500';
                default:
                    return 'bg-gray-500';
            }
        }

        // Функция показа модального окна переключения отдела
        function showTransferModal() {
            const currentChatId = getCurrentChatId();
            if (!currentChatId) {
                alert('Выберите чат для переключения');
                return;
            }

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] modal-overlay';
            modal.id = 'transferModal';
            
            let modalHtml = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 modal-content">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Переключить отдел</h3>
                        <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Выберите отдел, в который нужно перевести чат:
                        </p>
                        <div class="space-y-3">
                            <button onclick="transferToDepartment(1)" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">Б</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Бухгалтерия</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Финансовые вопросы</p>
                                    </div>
                                </div>
                            </button>
                            <button onclick="transferToDepartment(2)" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">IT</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">IT отдел</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Техническая поддержка</p>
                                    </div>
                                </div>
                            </button>
                            <button onclick="transferToDepartment(3)" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">HR</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">HR отдел</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Кадровые вопросы</p>
                                    </div>
                                </div>
                            </button>
                            <button onclick="transferToDepartment(4)" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-orange-500 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">Т</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Вопросы по товарам</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Товары в аптеке</p>
                                    </div>
                                </div>
                            </button>
                            <button onclick="transferToDepartment(null)" class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">—</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Без отдела</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Сбросить назначение</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            modal.innerHTML = modalHtml;
            document.body.appendChild(modal);
        }

        // Функция закрытия модального окна переключения
        function closeTransferModal() {
            const modal = document.getElementById('transferModal');
            if (modal) {
                modal.remove();
            }
        }

        // Функция переключения чата в отдел
        async function transferToDepartment(departmentId) {
            const currentChatId = getCurrentChatId();
            if (!currentChatId) {
                alert('Ошибка: чат не выбран');
                return;
            }

            try {
                const response = await fetch(`/user/chat/transfer/${currentChatId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        department_id: departmentId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    closeTransferModal();
                    alert('Чат успешно переведен в отдел');
                    // Обновляем страницу для отображения изменений
                    location.reload();
                } else {
                    alert('Ошибка: ' + (data.message || 'Не удалось перевести чат'));
                }
            } catch (error) {
                console.error('Ошибка переключения отдела:', error);
                alert('Ошибка при переключении отдела');
            }
        }

        // Переменные для автоматического обновления
        let lastMessageId = 0;
        let updateInterval;
        let isUpdating = false;

        // Функция инициализации автоматического обновления
        function initAutoUpdate() {
            if (currentChatId) {
                // Получаем ID последнего сообщения
                const messages = document.querySelectorAll('[data-message-id]');
                if (messages.length > 0) {
                    const lastMessage = messages[messages.length - 1];
                    lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
                }
                
                // Запускаем обновление каждые 3 секунды
                updateInterval = setInterval(updateChat, 3000);
            }
        }

        // Функция обновления чата
        async function updateChat() {
            if (!currentChatId || isUpdating) return;
            
            isUpdating = true;
            
            try {
                const response = await fetch(`/user/chat/messages/${currentChatId}?last_id=${lastMessageId}`);
                const data = await response.json();
                
                if (data.success && data.messages.length > 0) {
                    // Добавляем новые сообщения
                    const messagesContainer = document.getElementById('messagesContainer');
                    
                    data.messages.forEach(message => {
                        if (message.id > lastMessageId) {
                            const messageHtml = createMessageHtml(message);
                            messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                            lastMessageId = message.id;
                        }
                    });
                    
                    // Прокручиваем к последнему сообщению
                    scrollToBottom();
                    
                    // Обновляем счетчик непрочитанных в списке чатов
                    updateUnreadCount();
                }
            } catch (error) {
                console.error('Ошибка обновления чата:', error);
            } finally {
                isUpdating = false;
            }
        }

        // Функция создания HTML для сообщения
        function createMessageHtml(message) {
            let messageContent = '';
            
            // Обрабатываем изображения
            if (message.type === 'image' && message.image_data) {
                const imageTitle = message.content && message.content !== 'Изображение' ? message.content : 'Изображение';
                messageContent = `
                    <div class="mb-2">
                        <a href="${message.image_data.url}" 
                           data-lightbox="chat-images" 
                           data-title="${imageTitle}">
                            <img src="${message.image_data.url}" 
                                 alt="Изображение" 
                                 class="max-w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                 style="max-height: 300px;">
                        </a>
                    </div>
                `;
            }
            // Обрабатываем видео
            else if (message.type === 'video' && message.video_data) {
                messageContent = `
                    <div class="mb-2">
                        <video controls 
                               class="max-w-full h-auto rounded-lg"
                               style="max-height: 300px;">
                            <source src="${message.video_data.url}" type="video/${message.video_data.extension}">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                `;
            }
            // Обрабатываем стикеры
            else if (message.type === 'sticker' && message.sticker_data) {
                messageContent = `
                    <div class="mb-2">
                        <img src="${message.sticker_data.url}" 
                             alt="Стикер" 
                             class="max-w-32 h-auto rounded-lg">
                    </div>
                `;
            }
            // Обрабатываем документы
            else if (message.type === 'document' && message.document_data) {
                const bgClass = isFromClient ? 'bg-gray-100 dark:bg-gray-600' : 'bg-blue-400 dark:bg-blue-600';
                const textClass = isFromClient ? 'text-gray-900 dark:text-white' : 'text-white';
                const linkClass = isFromClient ? 'text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300' : 'text-blue-100 hover:text-blue-200';
                
                messageContent = `
                    <div class="mb-2 p-3 ${bgClass} rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-8 w-8 ${isFromClient ? 'text-gray-500' : 'text-white'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium ${textClass}">${message.document_data.name}</p>
                                <a href="${message.document_data.url}" 
                                   target="_blank"
                                   class="text-sm ${linkClass}">
                                    Скачать документ
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }
            // Обрабатываем аудио
            else if (message.type === 'audio' && message.audio_data) {
                messageContent = `
                    <div class="mb-2">
                        <audio controls class="w-full">
                            <source src="${message.audio_data.url}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                    </div>
                `;
            }
            // Обрабатываем локацию
            else if (message.type === 'location' && message.location_data) {
                const bgClass = isFromClient ? 'bg-gray-100 dark:bg-gray-600' : 'bg-blue-400 dark:bg-blue-600';
                const textClass = isFromClient ? 'text-gray-900 dark:text-white' : 'text-white';
                const linkClass = isFromClient ? 'text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300' : 'text-blue-100 hover:text-blue-200';
                const iconClass = isFromClient ? 'text-red-500' : 'text-red-200';
                
                messageContent = `
                    <div class="mb-2 p-3 ${bgClass} rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="h-6 w-6 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium ${textClass}">
                                    ${message.location_data.address || 'Локация'}
                                </p>
                                <a href="https://maps.google.com/?q=${message.location_data.latitude},${message.location_data.longitude}" 
                                   target="_blank"
                                   class="text-sm ${linkClass}">
                                    Открыть на карте
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Добавляем текст, если он есть и не равен стандартным сообщениям
            if (message.content && message.content !== 'Изображение' && message.content !== 'Видео' && message.content !== 'Стикер' && message.content !== 'Аудио сообщение') {
                messageContent += `<p class="text-sm whitespace-pre-line">${message.content}</p>`;
            }
            
            if (message.sender_name === 'Система') {
                // Убираем sticky с предыдущих системных сообщений
                const messagesContainer = document.getElementById('messagesContainer');
                if (messagesContainer) {
                    const existingSystemMessages = messagesContainer.querySelectorAll('.system-message');
                    existingSystemMessages.forEach(msg => {
                        msg.classList.remove('sticky', 'top-10', 'z-10', 'bg-white', 'dark:bg-gray-900', 'py-2', 'border-b', 'border-gray-200', 'dark:border-gray-600');
                    });
                }
                
                return `
                    <div class="flex justify-center mb-4 sticky top-10 z-10 bg-white dark:bg-gray-900 py-2 border-b border-gray-200 dark:border-gray-600 system-message">
                        <div class="bg-gray-100 dark:bg-blue-900/30 rounded-lg px-4 py-2 max-w-md border border-gray-200 dark:border-blue-500/30 shadow-sm">
                            <div class="flex items-center space-x-2 mb-1">
                                <svg class="h-4 w-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-gray-500 dark:text-blue-300">${formatTime(message.created_at)}</p>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-blue-200 text-left">${message.content}</p>
                        </div>
                    </div>
                `;
            } else if (message.is_from_client) {
                return `
                    <div class="flex items-start space-x-3 group mb-4" data-message-id="${message.id}" data-message-content="${message.content}" data-message-time="${message.created_at}">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">К</span>
                            </div>
                        </div>
                        <div class="flex-1 relative">
                            <div class="flex items-center space-x-2 mb-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">${message.sender_name}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${formatTime(message.created_at)}</p>
                            </div>
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                                ${messageContent}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="flex items-start space-x-3 group mb-4 justify-end" data-message-id="${message.id}" data-message-content="${message.content}" data-message-time="${message.created_at}">
                        <div class="flex-1 relative">
                            <div class="flex items-center space-x-2 mb-1 justify-end">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">${message.sender_name}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${formatTime(message.created_at)}</p>
                            </div>
                            <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
                                ${messageContent}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">М</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Функция форматирования времени
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        }

        // Функция прокрутки к последнему сообщению
        function scrollToBottom() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Функция прокрутки к последнему сообщению с учетом sticky элементов
        function scrollToBottomSmooth() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTo({
                top: messagesContainer.scrollHeight,
                behavior: 'smooth'
            });
        }

        // Функция обновления счетчика непрочитанных
        function updateUnreadCount() {
            const currentChatItem = document.querySelector(`[data-chat-id="${currentChatId}"]`);
            if (currentChatItem) {
                // Убираем красный бейдж у текущего чата
                const unreadBadge = currentChatItem.querySelector('.unread-badge');
                if (unreadBadge) {
                    unreadBadge.remove();
                }
            }
        }

        // Функция остановки автоматического обновления
        function stopAutoUpdate() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        // Обработчик изменения чата
        function onChatChange() {
            stopAutoUpdate();
            initAutoUpdate();
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            if (currentChatId) {
                initAutoUpdate();
            }
        });

        // Обработчик клика по чату (обновляем существующий)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.chat-item')) {
                const chatItem = e.target.closest('.chat-item');
                const chatId = chatItem.getAttribute('data-chat-id');
                
                if (chatId && chatId !== currentChatId) {
                    currentChatId = chatId;
                    onChatChange();
                }
            }
        });

        // Закрытие модального окна загрузки изображения при клике вне его
        document.addEventListener('DOMContentLoaded', function() {
            const uploadModal = document.getElementById('imageUploadModal');
            uploadModal.addEventListener('click', function(e) {
                if (e.target === uploadModal) {
                    closeImageUploadModal();
                }
            });

            // Закрытие модального окна загрузки видео при клике вне его
            const videoUploadModal = document.getElementById('videoUploadModal');
            videoUploadModal.addEventListener('click', function(e) {
                if (e.target === videoUploadModal) {
                    closeVideoUploadModal();
                }
            });
        });

        // Функция обработки загрузки изображения
        async function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Проверяем размер файла (максимум 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Размер файла не должен превышать 10MB');
                return;
            }

            // Проверяем тип файла
            if (!file.type.startsWith('image/')) {
                alert('Пожалуйста, выберите изображение');
                return;
            }

            // Показываем модальное окно для ввода подписи
            showImageUploadModal(file);
                
        // Функция обработки загрузки видео
        async function handleVideoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Проверяем размер файла (максимум 50MB)
            if (file.size > 50 * 1024 * 1024) {
                alert('Размер файла не должен превышать 50MB');
                return;
            }

            // Проверяем тип файла
            if (!file.type.startsWith('video/')) {
                alert('Пожалуйста, выберите видео файл');
                return;
            }

            // Показываем модальное окно для ввода подписи
            showVideoUploadModal(file);
        }

        // Функция показа модального окна загрузки изображения
        function showImageUploadModal(file) {
            const modal = document.getElementById('imageUploadModal');
            const preview = document.getElementById('imagePreview');
            const captionInput = document.getElementById('imageCaption');
            
            // Показываем превью изображения
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // Очищаем поле подписи
            captionInput.value = '';
            
            // Сохраняем файл в модальном окне
            modal.dataset.file = JSON.stringify({
                name: file.name,
                size: file.size,
                type: file.type
            });
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Функция закрытия модального окна загрузки изображения
        function closeImageUploadModal() {
            const modal = document.getElementById('imageUploadModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Очищаем поле ввода файла
            document.getElementById('imageUpload').value = '';
        }
        
        // Функция показа модального окна загрузки видео
        function showVideoUploadModal(file) {
            const modal = document.getElementById('videoUploadModal');
            const preview = document.getElementById('videoPreview');
            const captionInput = document.getElementById('videoCaption');
            
            // Показываем превью видео
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // Очищаем поле подписи
            captionInput.value = '';
            
            // Сохраняем файл в модальном окне
            modal.dataset.file = JSON.stringify({
                name: file.name,
                size: file.size,
                type: file.type
            });
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Функция закрытия модального окна загрузки видео
        function closeVideoUploadModal() {
            const modal = document.getElementById('videoUploadModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Очищаем поле ввода файла
            document.getElementById('videoUpload').value = '';
        }

        // Функция отправки изображения с подписью
        async function uploadImageWithCaption() {
            const modal = document.getElementById('imageUploadModal');
            const captionInput = document.getElementById('imageCaption');
            const fileInput = document.getElementById('imageUpload');
            
            const file = fileInput.files[0];
            const caption = captionInput.value.trim();
            
            if (!file) {
                alert('Пожалуйста, выберите изображение');
                return;
            }

            const formData = new FormData();
            formData.append('image', file);
            formData.append('chat_id', currentChatId);
            formData.append('caption', caption);
            formData.append('_token', '<?php echo e(csrf_token()); ?>');

            try {
                // Показываем индикатор загрузки
                const uploadBtn = document.getElementById('uploadImageBtn');
                const originalText = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Загрузка...';
                uploadBtn.disabled = true;

                const response = await fetch('/user/chat/upload-image', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Закрываем модальное окно
                    closeImageUploadModal();
                    
                    // Обновляем чат для отображения нового изображения
                    await updateChat();
                } else {
                    alert('Ошибка загрузки изображения: ' + (result.error || 'Неизвестная ошибка'));
                }
            } catch (error) {
                console.error('Ошибка загрузки изображения:', error);
                alert('Ошибка загрузки изображения');
            } finally {
                // Восстанавливаем кнопку
                const uploadBtn = document.getElementById('uploadImageBtn');
                uploadBtn.innerHTML = originalText;
                uploadBtn.disabled = false;
            }
        }
        
        // Функция отправки видео с подписью
        async function uploadVideoWithCaption() {
            const modal = document.getElementById('videoUploadModal');
            const captionInput = document.getElementById('videoCaption');
            const fileInput = document.getElementById('videoUpload');
            
            const file = fileInput.files[0];
            const caption = captionInput.value.trim();
            
            if (!file) {
                alert('Пожалуйста, выберите видео');
                return;
            }

            const formData = new FormData();
            formData.append('video', file);
            formData.append('chat_id', currentChatId);
            formData.append('caption', caption);
            formData.append('_token', '<?php echo e(csrf_token()); ?>');

            try {
                // Показываем индикатор загрузки
                const uploadBtn = document.getElementById('uploadVideoBtn');
                const originalText = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Загрузка...';
                uploadBtn.disabled = true;

                const response = await fetch('/user/chat/upload-video', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Закрываем модальное окно
                    closeVideoUploadModal();
                    
                    // Обновляем чат для отображения нового видео
                    await updateChat();
                } else {
                    alert('Ошибка загрузки видео: ' + (result.error || 'Неизвестная ошибка'));
                }
            } catch (error) {
                console.error('Ошибка загрузки видео:', error);
                alert('Ошибка загрузки видео');
            } finally {
                // Восстанавливаем кнопку
                const uploadBtn = document.getElementById('uploadVideoBtn');
                uploadBtn.innerHTML = originalText;
                uploadBtn.disabled = false;
            }
        }
        
        // Функция переключения пикера эмодзи
        function toggleEmojiPicker() {
            const picker = document.getElementById('emojiPicker');
            if (picker.classList.contains('hidden')) {
                picker.classList.remove('hidden');
            } else {
                picker.classList.add('hidden');
            }
        }
        
        // Функция вставки эмодзи
        function insertEmoji(emoji) {
            const messageInput = document.getElementById('messageInput');
            const cursorPos = messageInput.selectionStart;
            const textBefore = messageInput.value.substring(0, cursorPos);
            const textAfter = messageInput.value.substring(cursorPos);
            
            messageInput.value = textBefore + emoji + textAfter;
            messageInput.selectionStart = messageInput.selectionEnd = cursorPos + emoji.length;
            messageInput.focus();
            
            // Скрываем пикер
            document.getElementById('emojiPicker').classList.add('hidden');
        }
        
        // Закрытие пикера эмодзи при клике вне его
        document.addEventListener('click', function(e) {
            const picker = document.getElementById('emojiPicker');
            const emojiButton = document.querySelector('[onclick="toggleEmojiPicker()"]');
            
            if (!picker.contains(e.target) && !emojiButton.contains(e.target)) {
                picker.classList.add('hidden');
            }
        });
        
        // Функция переключения пикера шаблонов
        function toggleTemplatesPicker() {
            const picker = document.getElementById('templatesPicker');
            if (picker.classList.contains('hidden')) {
                picker.classList.remove('hidden');
                loadTemplates('all'); // Загружаем все шаблоны по умолчанию
            } else {
                picker.classList.add('hidden');
            }
        }
        
        // Функция загрузки шаблонов
        function loadTemplates(category = 'all') {
            const templatesList = document.getElementById('templatesList');
            
            // Показываем индикатор загрузки
            templatesList.innerHTML = `
                <div class="text-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Загрузка шаблонов...</p>
                </div>
            `;
            
            // Загружаем шаблоны с сервера
            fetch(`/admin/response-templates/category/${category}`)
                .then(response => response.json())
                .then(templates => {
                    if (templates.length === 0) {
                        templatesList.innerHTML = `
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">В этой категории нет шаблонов</p>
                            </div>
                        `;
                    } else {
                        let templatesHtml = '';
                        templates.forEach(template => {
                            templatesHtml += `
                                <div class="template-item p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                                     onclick="insertTemplate('${template.id}', '${template.content.replace(/'/g, "\\'")}')">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">${template.name}</h4>
                                        <button onclick="event.stopPropagation(); copyTemplate('${template.id}', '${template.content.replace(/'/g, "\\'")}')" 
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                            Копировать
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">${template.content}</p>
                                </div>
                            `;
                        });
                        templatesList.innerHTML = templatesHtml;
                    }
                })
                .catch(error => {
                    console.error('Ошибка загрузки шаблонов:', error);
                    templatesList.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-sm text-red-500">Ошибка загрузки шаблонов</p>
                        </div>
                    `;
                });
        }
        
        // Функция фильтрации шаблонов
        function filterTemplates(category) {
            // Обновляем активную кнопку
            document.querySelectorAll('.template-filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
                btn.classList.add('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-200');
            });
            
            event.target.classList.remove('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-200');
            event.target.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
            
            // Загружаем шаблоны для выбранной категории
            loadTemplates(category);
        }
        
        // Функция вставки шаблона
        function insertTemplate(templateId, content) {
            const messageInput = document.getElementById('messageInput');
            const cursorPos = messageInput.selectionStart;
            const textBefore = messageInput.value.substring(0, cursorPos);
            const textAfter = messageInput.value.substring(cursorPos);
            
            messageInput.value = textBefore + content + textAfter;
            messageInput.selectionStart = messageInput.selectionEnd = cursorPos + content.length;
            messageInput.focus();
            
            // Скрываем пикер
            document.getElementById('templatesPicker').classList.add('hidden');
            
            // Увеличиваем счетчик использований
            fetch(`/admin/response-templates/${templateId}/increment-usage`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });
        }
        
        // Закрытие пикера шаблонов при клике вне его
        document.addEventListener('click', function(e) {
            const picker = document.getElementById('templatesPicker');
            const templatesButton = document.querySelector('[onclick="toggleTemplatesPicker()"]');
            
            if (!picker.contains(e.target) && !templatesButton.contains(e.target)) {
                picker.classList.add('hidden');
            }
        });
    </script>



    <!-- Модальное окно для загрузки изображения с подписью -->
    <div id="imageUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Загрузить изображение</h3>
                    <button onclick="closeImageUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Превью изображения -->
                <div class="mb-4">
                    <img id="imagePreview" src="" alt="Превью" class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
                
                <!-- Поле для подписи -->
                <div class="mb-4">
                    <label for="imageCaption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Подпись к изображению (необязательно)
                    </label>
                    <textarea id="imageCaption" 
                              rows="3"
                              placeholder="Введите подпись к изображению..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <button onclick="closeImageUploadModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Отмена
                    </button>
                    <button id="uploadImageBtn"
                            onclick="uploadImageWithCaption()" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span>Загрузить</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для загрузки видео с подписью -->
    <div id="videoUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Загрузить видео</h3>
                    <button onclick="closeVideoUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Превью видео -->
                <div class="mb-4">
                    <video id="videoPreview" controls class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                        Ваш браузер не поддерживает видео.
                    </video>
                </div>
                
                <!-- Поле для подписи -->
                <div class="mb-4">
                    <label for="videoCaption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Подпись к видео (необязательно)
                    </label>
                    <textarea id="videoCaption" 
                              rows="3"
                              placeholder="Введите подпись к видео..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <button onclick="closeVideoUploadModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Отмена
                    </button>
                    <button id="uploadVideoBtn"
                            onclick="uploadVideoWithCaption()" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span>Загрузить</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Пикер эмодзи -->
    <div id="emojiPicker" class="absolute bottom-16 left-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-2 hidden z-[9999]" style="width: 300px;">
        <div class="grid grid-cols-8 gap-1">
            <!-- Популярные эмодзи -->
            <button onclick="insertEmoji('😊')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😊</button>
            <button onclick="insertEmoji('😂')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😂</button>
            <button onclick="insertEmoji('❤️')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">❤️</button>
            <button onclick="insertEmoji('👍')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">👍</button>
            <button onclick="insertEmoji('👎')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">👎</button>
            <button onclick="insertEmoji('😍')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😍</button>
            <button onclick="insertEmoji('😭')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😭</button>
            <button onclick="insertEmoji('🤔')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤔</button>
            
            <button onclick="insertEmoji('😀')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😀</button>
            <button onclick="insertEmoji('😃')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😃</button>
            <button onclick="insertEmoji('😄')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😄</button>
            <button onclick="insertEmoji('😁')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😁</button>
            <button onclick="insertEmoji('😅')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😅</button>
            <button onclick="insertEmoji('😆')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😆</button>
            <button onclick="insertEmoji('😉')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😉</button>
            <button onclick="insertEmoji('😋')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😋</button>
            
            <button onclick="insertEmoji('😎')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😎</button>
            <button onclick="insertEmoji('😍')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😍</button>
            <button onclick="insertEmoji('😘')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😘</button>
            <button onclick="insertEmoji('😗')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😗</button>
            <button onclick="insertEmoji('😙')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😙</button>
            <button onclick="insertEmoji('😚')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😚</button>
            <button onclick="insertEmoji('🙂')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🙂</button>
            <button onclick="insertEmoji('🤗')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤗</button>
            <button onclick="insertEmoji('🤔')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤔</button>
            
            <button onclick="insertEmoji('😐')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😐</button>
            <button onclick="insertEmoji('😑')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😑</button>
            <button onclick="insertEmoji('😶')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😶</button>
            <button onclick="insertEmoji('🙄')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🙄</button>
            <button onclick="insertEmoji('😏')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😏</button>
            <button onclick="insertEmoji('😣')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😣</button>
            <button onclick="insertEmoji('😥')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😥</button>
            <button onclick="insertEmoji('😮')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😮</button>
            
            <button onclick="insertEmoji('😯')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😯</button>
            <button onclick="insertEmoji('😪')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😪</button>
            <button onclick="insertEmoji('😫')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😫</button>
            <button onclick="insertEmoji('😴')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😴</button>
            <button onclick="insertEmoji('😌')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😌</button>
            <button onclick="insertEmoji('😛')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😛</button>
            <button onclick="insertEmoji('😜')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😜</button>
            <button onclick="insertEmoji('😝')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😝</button>
            
            <button onclick="insertEmoji('😒')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😒</button>
            <button onclick="insertEmoji('😓')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😓</button>
            <button onclick="insertEmoji('😔')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😔</button>
            <button onclick="insertEmoji('😕')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😕</button>
            <button onclick="insertEmoji('🙃')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🙃</button>
            <button onclick="insertEmoji('🤑')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤑</button>
            <button onclick="insertEmoji('😲')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😲</button>
            <button onclick="insertEmoji('😷')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😷</button>
            
            <button onclick="insertEmoji('🤒')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤒</button>
            <button onclick="insertEmoji('🤕')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤕</button>
            <button onclick="insertEmoji('🤢')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤢</button>
            <button onclick="insertEmoji('🤧')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">🤧</button>
            <button onclick="insertEmoji('😈')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">😈</button>
            <button onclick="insertEmoji('👿')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">👿</button>
            <button onclick="insertEmoji('👹')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">👹</button>
            <button onclick="insertEmoji('👺')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-2xl">👺</button>
        </div>
    </div>

    <!-- Пикер шаблонов ответов -->
    <div id="templatesPicker" class="absolute bottom-16 left-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-4 hidden z-[9999]" style="width: 400px; max-height: 500px; overflow-y: auto;">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Шаблоны ответов</h3>
            <div class="flex flex-wrap gap-2 mb-3">
                <button onclick="filterTemplates('all')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                        data-category="all">
                    Все
                </button>
                <button onclick="filterTemplates('greeting')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        data-category="greeting">
                    Приветствие
                </button>
                <button onclick="filterTemplates('help')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        data-category="help">
                    Помощь
                </button>
                <button onclick="filterTemplates('support')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        data-category="support">
                    Поддержка
                </button>
                <button onclick="filterTemplates('information')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        data-category="information">
                    Информация
                </button>
                <button onclick="filterTemplates('general')" 
                        class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        data-category="general">
                    Общие
                </button>
            </div>
        </div>
        
        <div id="templatesList" class="space-y-2">
            <!-- Шаблоны будут загружены динамически -->
            <div class="text-center py-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Загрузка шаблонов...</p>
            </div>
        </div>
    </div>

    <!-- Подключаем Lightbox2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    
    <!-- Инициализация Lightbox2 -->
    <script>
        // Инициализация Lightbox2 с кастомными настройками
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Изображение %1 из %2',
            'fadeDuration': 300,
            'imageFadeDuration': 300,
            'positionFromTop': 50
        });
    </script>

    <?php $__env->stopSection(); ?>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a)): ?>
<?php $attributes = $__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a; ?>
<?php unset($__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc113672a4057e9d1a374a45c3d49bb0a)): ?>
<?php $component = $__componentOriginalc113672a4057e9d1a374a45c3d49bb0a; ?>
<?php unset($__componentOriginalc113672a4057e9d1a374a45c3d49bb0a); ?>
<?php endif; ?>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/user/chat/index.blade.php ENDPATH**/ ?>