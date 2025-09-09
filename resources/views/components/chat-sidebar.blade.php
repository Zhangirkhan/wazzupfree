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
        @forelse($chatsData as $chat)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600 chat-item {{ request('chat') == $chat['id'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}" data-chat-id="{{ $chat['id'] }}">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 {{ $chat['is_online'] ? 'bg-green-500' : 'bg-blue-500' }} rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ $chat['avatar_text'] }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $chat['title'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $chat['last_message_preview'] }}</p>
                    </div>
                    <div class="flex-shrink-0 flex flex-col items-end">
                        @if($chat['last_message_time'])
                            <span class="text-xs text-gray-400">{{ $chat['last_message_time']->format('H:i') }}</span>
                        @endif
                        @if($chat['unread_count'] > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mt-1 unread-badge">
                                {{ $chat['unread_count'] }}
                            </span>
                        @elseif($chat['status'] === 'active' && !$chat['assigned_to'])
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 mt-1">
                                Новый
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <div class="text-gray-400 dark:text-gray-500">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">Чатов пока нет</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
