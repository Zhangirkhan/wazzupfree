<x-navigation.app>
    @section('title', 'Чат')
    @section('content')
    <div class="h-[calc(100vh-120px)] bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex h-full">
            <!-- Контакты слева -->
            <div class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col">
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
                <div class="flex-1 overflow-y-auto" id="chatsList">
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
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mt-1">
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

            <!-- Окно чата справа -->
            <div class="flex-1 flex flex-col" id="chatWindow">
                <!-- Заголовок чата -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700" id="chatHeader">
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
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Сообщения -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
                    @if($currentChat && count($currentMessages) > 0)
                        @foreach($currentMessages as $message)
                            @if($message['sender_name'] === 'Система')
                                <!-- Системное сообщение (по центру) -->
                                <div class="flex justify-center mb-4">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line text-center">{{ $message['content'] }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-center items-center space-x-2 mb-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
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
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $message['sender_name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
                                        </div>
                                        <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $message['content'] }}</p>
                                        </div>
                                        <!-- Кнопка удаления (только для админов) -->
                                        @if(auth()->user()->hasRole('admin'))
                                        <button class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 focus:outline-none" onclick="deleteMessage({{ $message['id'] }})">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- Сообщение от менеджера (справа) -->
                                <div class="flex items-start space-x-3 justify-end group mb-4" data-message-id="{{ $message['id'] }}" data-message-content="{{ $message['content'] }}" data-message-time="{{ $message['created_at'] }}">
                                    <div class="flex-1 flex justify-end relative">
                                        <!-- Имя автора сверху -->
                                        <div class="flex items-center space-x-2 mb-1 justify-end">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $message['sender_name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
                                        </div>
                                        <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs">
                                            <p class="text-sm text-white whitespace-pre-line">{{ $message['content'] }}</p>
                                        </div>
                                        <!-- Кнопка удаления (для своих сообщений или админов) -->
                                        @if(isset($message['user_id']) && (auth()->user()->id == $message['user_id'] || auth()->user()->hasRole('admin')))
                                        <button class="absolute -top-2 -left-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600 focus:outline-none" onclick="deleteMessage({{ $message['id'] }})">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">М</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

                <!-- Поле ввода -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700" id="messageInputContainer" style="display: {{ $currentChat ? 'block' : 'none' }};">
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

    <!-- Модальное окно для создания чата -->
    <div id="addChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
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
            let searchTimeout;
            let currentChatId = null;

            // Функция поиска чатов
            function searchChats(query) {
                fetch('{{ route("user.chat.search") }}', {
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
                    window.location.href = `{{ route('user.chat.index') }}?chat=${chatId}`;
                }
            });

            // Инициализация текущего чата при загрузке страницы
            @if($currentChat)
                currentChatId = {{ $currentChat['id'] }};
            @endif

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
                let url = `{{ url('/user/chat/messages') }}/${currentChatId}`;
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
                    messageHtml = `
                        <div class="flex justify-center mb-4">
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line text-center">${message.content}</p>
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
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">${message.sender_name}</p>
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
                            <div class="flex-1 flex justify-end">
                                <!-- Имя автора сверху -->
                                <div class="flex items-center space-x-2 mb-1 justify-end">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">${message.sender_name}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                                </div>
                                <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs">
                                    <p class="text-sm text-white whitespace-pre-line">${message.content}</p>
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
                
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
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
                
                fetch(`{{ url('/user/chat/messages') }}/${messageId}`, {
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
            @if($currentChat)
                console.log('Запускаем автообновление для чата:', {{ $currentChat['id'] }});
                // Устанавливаем ID последнего сообщения при загрузке
                @if(count($currentMessages) > 0)
                    lastMessageId = {{ $currentMessages->last()['id'] }};
                    console.log('Установлен lastMessageId:', lastMessageId);
                @endif
                // startAutoRefresh(); // Временно отключено автообновление
            @else
                console.log('Текущий чат не найден');
            @endif



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
                        <div class="flex-1 flex justify-end">
                            <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs">
                                <p class="text-sm text-white whitespace-pre-line">${content}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">М</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center space-x-2 mb-4">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Менеджер</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                    </div>
                `;
                
                messagesContainer.insertAdjacentHTML('beforeend', tempMessageHtml);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Отправляем сообщение на сервер
                fetch(`{{ url('/user/chat/send') }}/${currentChatId}`, {
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
                        console.error('Ошибка отправки сообщения:', data.message);
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
                                url: '{{ route("user.chat.search-clients") }}',
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

                fetch('{{ route("user.chat.create") }}', {
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
    </script>
    @endsection
</x-navigation.app>
