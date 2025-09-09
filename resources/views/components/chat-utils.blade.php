<script>
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
        const chatsList = document.getElementById('chatsList');
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

    // Функция прокрутки к последнему сообщению
    function scrollToBottom() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Функция прокрутки к последнему сообщению с учетом sticky элементов
    function scrollToBottomSmooth() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTo({
                top: messagesContainer.scrollHeight,
                behavior: 'smooth'
            });
        }
    }

    // Функция форматирования времени
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
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
        if (!currentChatId) return;
        
        try {
            const response = await fetch(`/user/chat/messages/${currentChatId}`);
            const data = await response.json();
            
            if (data.success && data.messages.length > 0) {
                // Добавляем новые сообщения
                const messagesContainer = document.getElementById('messagesContainer');
                
                data.messages.forEach(message => {
                    const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                    if (!existingMessage) {
                        const messageHtml = createMessageHtml(message);
                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                    }
                });
                
                // Прокручиваем к последнему сообщению
                scrollToBottom();
            }
        } catch (error) {
            console.error('Ошибка обновления чата:', error);
        }
    }

    // Функция создания HTML для сообщения
    function createMessageHtml(message) {
        const currentTime = new Date(message.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        
        if (message.sender_name === 'Система') {
            return `
                <div class="flex justify-center mb-4 system-message">
                    <div class="bg-gray-100 dark:bg-blue-900/30 rounded-lg px-4 py-2 max-w-md border border-gray-200 dark:border-blue-500/30 shadow-sm">
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="h-4 w-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-gray-500 dark:text-blue-300">${currentTime}</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 max-w-xs">
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">${message.content}</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">${currentTime}</p>
                        </div>
                        <div class="bg-blue-500 rounded-lg px-4 py-2 max-w-xs ml-auto">
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
    }

    // Обработчик ввода в поле поиска
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const chatsList = document.getElementById('chatsList');
        let searchTimeout;

        if (searchInput) {
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
        }

        // Обработчик клика по чату
        if (chatsList) {
            chatsList.addEventListener('click', function(e) {
                const chatItem = e.target.closest('.chat-item');
                if (chatItem) {
                    const chatId = chatItem.dataset.chatId;
                    // Переходим на страницу с GET параметром
                    window.location.href = `{{ route('user.chat.index') }}?chat=${chatId}`;
                }
            });
        }
    });
</script>
