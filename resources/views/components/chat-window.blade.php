<!-- Окно чата справа -->
<div class="flex-1 flex flex-col min-h-0" id="chatWindow">
    <!-- Заголовок чата -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10" id="chatHeader">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-white" id="chatAvatar">
                    @if($currentClient)
                        {{ strtoupper(substr($currentClient['name'], 0, 1)) }}
                    @elseif($currentChat)
                        К
                    @else
                        К
                    @endif
                </span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="chatTitle">
                    @if($currentClient)
                        {{ $currentClient['name'] }}
                    @elseif($currentChat)
                        {{ $currentChat['title'] }}
                    @else
                        Выберите чат
                    @endif
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" id="chatStatus">
                    @if($currentChat)
                        {{ $currentChat['status'] === 'active' ? 'Онлайн' : 'Оффлайн' }}
                    @else
                        Начните диалог
                    @endif
                </p>
            </div>
                        <div class="flex items-center space-x-2">
                            {{-- <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button> --}}
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
                <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Сообщения -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0 pb-20" id="messagesContainer">
        @if($currentChat && count($currentMessages) > 0)
            @php
                $systemMessages = $currentMessages->where('sender_name', 'Система');
                $lastSystemMessage = $systemMessages->last();
            @endphp
            
            @foreach($currentMessages as $message)
                <x-message-item :message="$message" :lastSystemMessage="$lastSystemMessage" />
            @endforeach
        @elseif($currentChat)
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-500">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">Начните диалог</p>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-500">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">Выберите чат для начала диалога</p>
                </div>
            </div>
        @endif
    </div>
</div>
