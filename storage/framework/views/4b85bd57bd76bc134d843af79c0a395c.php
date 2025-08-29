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
    <?php $__env->startSection('content'); ?>
    <div class="flex justify-center" style="height: 700px; max-height: 700px;">
        <div class="h-[700px] bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" style="height: 700px; max-height: 700px;">
            <div class="flex h-[700px]" style="height: 700px; max-height: 700px;">
            <!-- Контакты слева -->
            <div class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
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
                <div class="p-4 border-b border-gray-200 dark:border-gray-700" id="chatHeader">
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
                <div class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0" id="messagesContainer">
                    <?php if($currentChat && count($currentMessages) > 0): ?>
                        <?php $__currentLoopData = $currentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($message['sender_name'] === 'Система'): ?>
                                <!-- Системное сообщение (по центру) -->
                                <div class="flex justify-center mb-4">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 text-left"><?php echo nl2br(e($message['content'])); ?></p>
                                    </div>
                                </div>
                                <div class="flex justify-center items-center space-x-2 mb-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(\Carbon\Carbon::parse($message['created_at'])->format('H:i')); ?></p>
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
                                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line"><?php echo e($message['content']); ?></p>
                                        </div>
                                        <!-- Кнопка удаления (только для админов) -->
                                        <?php if(auth()->user()->hasRole('admin')): ?>
                                        <button class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 focus:outline-none" onclick="deleteMessage(<?php echo e($message['id']); ?>)">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <?php endif; ?>
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
                                            <p class="text-sm text-white whitespace-pre-line"><?php echo e($message['content']); ?></p>
                                        </div>
                                        <!-- Кнопка удаления (для своих сообщений или админов) -->
                                        <?php if(isset($message['user_id']) && (auth()->user()->id == $message['user_id'] || auth()->user()->hasRole('admin'))): ?>
                                        <button class="absolute -top-2 -left-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 focus:outline-none" onclick="deleteMessage(<?php echo e($message['id']); ?>)">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <?php endif; ?>
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
                <div class="p-4 border-t border-gray-200 dark:border-gray-700" id="messageInputContainer" style="display: <?php echo e($currentChat ? 'block' : 'none'); ?>;">
                    <form id="messageForm" class="flex items-center space-x-3">
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
    </div>

    <!-- Модальное окно для создания чата -->
    <div id="addChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    
    <!-- Модальное окно подтверждения завершения диалога -->
    <div id="endChatConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center" style="z-index: 9999;">
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800">
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
        <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800">
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
            const searchInput = document.getElementById('searchInput');
            const chatsList = document.getElementById('chatsList');
            const addChatBtn = document.getElementById('addChatBtn');
            const addChatModal = document.getElementById('addChatModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const createChatForm = document.getElementById('createChatForm');
            const clientSelect = document.getElementById('clientSelect');
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
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }, 100);
            <?php endif; ?>

            // Переменные для автообновления
            let lastMessageId = null;
            let autoRefreshInterval = null;
            
            // Функция для получения новых сообщений
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
                                    console.log('Добавлено новое сообщение:', message.id, message.content);
                                } else {
                                    console.log('Сообщение уже существует или есть временное:', message.id, message.content);
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
                    // Заменяем \n на <br> для правильного отображения переносов строк
                    const formattedContent = message.content.replace(/\n/g, '<br>');
                    messageHtml = `
                        <div class="flex justify-center mb-4">
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                                <p class="text-sm text-gray-600 dark:text-gray-400 text-left">${formattedContent}</p>
                            </div>
                        </div>
                        <div class="flex justify-center items-center space-x-2 mb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
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
            
            // Функция для запуска автообновления
            function startAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
                
                // Получаем сообщения каждые 3 секунды
                autoRefreshInterval = setInterval(fetchNewMessages, 3000);
                
                // Первоначальная загрузка
                fetchNewMessages();
            }
            
            // Функция для остановки автообновления
            function stopAutoRefresh() {
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
                // startAutoRefresh(); // Временно отключено автообновление
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
                
                // Добавляем сообщение в интерфейс сразу (временное)
                const tempMessageId = 'temp_' + Date.now();
                const currentTime = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                
                const tempMessageHtml = `
                    <div class="flex items-start space-x-3 justify-end" data-message-id="${tempMessageId}" data-temp="true">
                        <div class="flex-1">
                            <!-- Имя автора сверху -->
                            <div class="flex items-center space-x-2 mb-1 justify-end">
                                <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e(auth()->user()->name); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                            </div>
                            <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
                                <p class="text-sm text-white whitespace-pre-line">${content}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></span>
                            </div>
                        </div>
                    </div>
                `;
                
                messagesContainer.insertAdjacentHTML('beforeend', tempMessageHtml);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
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
                    if (!data.success) {
                        console.error('Ошибка отправки сообщения:', data.error || data.message);
                        alert('Ошибка отправки сообщения: ' + (data.error || data.message));
                        // Удаляем временное сообщение при ошибке
                        const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                        if (tempMessage) {
                            tempMessage.nextElementSibling.remove();
                            tempMessage.remove();
                        }
                    } else {
                        console.log('Сообщение отправлено успешно');
                        // Удаляем временное сообщение и заменяем на реальное
                        const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                        if (tempMessage) {
                            tempMessage.nextElementSibling.remove();
                            tempMessage.remove();
                        }
                        // Добавляем реальное сообщение
                        addMessageToInterface({
                            id: data.message.id,
                            content: data.message.content,
                            created_at: data.message.created_at,
                            is_from_client: false,
                            sender_name: data.message.sender_name,
                            sender_avatar: data.message.sender_avatar,
                            type: data.message.type,
                            user_id: data.message.user_id
                        });
                    }
                })
                .catch(error => {
                    console.error('Ошибка отправки сообщения:', error);
                    alert('Ошибка отправки сообщения: ' + error.message);
                    // Удаляем временное сообщение при ошибке
                    const tempMessage = document.querySelector(`[data-message-id="${tempMessageId}"]`);
                    if (tempMessage) {
                        tempMessage.nextElementSibling.remove();
                        tempMessage.remove();
                    }
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
                openModal();
                // Инициализируем Select2 после открытия модального окна
                setTimeout(() => {
                    if (!window.clientSelect2) {
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
                                cache: true
                            },
                            minimumInputLength: 2,
                            templateResult: function(data) {
                                if (data.loading) return data.text;
                                return $(`<div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="font-medium">${data.name}</div>
                                        <div class="text-sm text-gray-500">${data.phone}</div>
                                    </div>
                                </div>`);
                            },
                            templateSelection: function(data) {
                                if (data.id) {
                                    return data.name + ' (' + data.phone + ')';
                                }
                                return data.text;
                            }
                        });
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
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.id = 'historyModal';
            
            let historyHtml = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
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
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.id = 'transferModal';
            
            let modalHtml = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
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
            if (message.sender_name === 'Система') {
                return `
                    <div class="flex justify-center mb-4">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-left">${message.content}</p>
                        </div>
                    </div>
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">${formatTime(message.created_at)}</p>
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
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-md">
                                <p class="text-sm text-gray-900 dark:text-white">${message.content}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="flex items-start space-x-3 group mb-4 justify-end" data-message-id="${message.id}" data-message-content="${message.content}" data-message-time="${message.created_at}">
                        <div class="flex-1 relative">
                            <div class="flex items-center space-x-2 mb-1 justify-end">
                                <p class="text-xs text-gray-500 dark:text-gray-400">${formatTime(message.created_at)}</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">${message.sender_name}</p>
                            </div>
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 max-w-md ml-auto">
                                <p class="text-sm">${message.content}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">С</span>
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
    </script>
    <?php $__env->stopSection(); ?>
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