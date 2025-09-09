@props(['currentChat', 'currentMessages'])

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
        let lastMessageId = null;
        let eventSource = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 5;
        let autoRefreshInterval;
        let updateInterval;
        let isUpdating = false;

        // Инициализация текущего чата при загрузке страницы
        @if($currentChat)
            currentChatId = {{ $currentChat['id'] }};
            // Показываем кнопку завершения диалога для менеджеров и руководителей
            showEndChatButton();
            
            // Автоматический скролл к последнему сообщению при загрузке
            setTimeout(() => {
                scrollToBottomSmooth();
            }, 100);
        @endif

        // Запускаем автообновление если есть текущий чат
        @if($currentChat)
            console.log('Запускаем автообновление для чата:', {{ $currentChat['id'] }});
            // Устанавливаем ID последнего сообщения при загрузке
            @if(count($currentMessages) > 0)
                lastMessageId = {{ $currentMessages->last()['id'] }};
                console.log('Установлен lastMessageId:', lastMessageId);
            @endif
            startAutoRefresh(); // Запускаем real-time обновления
        @else
            console.log('Текущий чат не найден');
        @endif

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
            let url = `{{ url('/user/chat/stream') }}/${currentChatId}`;
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

        // Функция для показа кнопки завершения диалога
        function showEndChatButton() {
            // Проверяем, является ли пользователь менеджером или руководителем
            const userRole = '{{ Auth::user()->role }}';
            const userPosition = '{{ Auth::user()->position }}';
            
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

            // Показываем модальное окно подтверждения
            showEndChatConfirmModal();
        });

        // Функция завершения диалога
        function endChat() {
            const endMessage = 'Спасибо за обращение диалог будет завершен. Для продолжения напишите 1 или 0 для возврата в меню.';
            
            // Отправляем сообщение о завершении
            fetch(`{{ url('/user/chat/send') }}/${currentChatId}`, {
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
                    fetch(`{{ url('/user/chat/end') }}/${currentChatId}`, {
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

        // Функция показа модального окна подтверждения завершения диалога
        function showEndChatConfirmModal() {
            const modal = document.getElementById('endChatConfirmModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        // Функция закрытия модального окна подтверждения завершения диалога
        function closeEndChatConfirmModal() {
            const modal = document.getElementById('endChatConfirmModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Обработчики для модального окна подтверждения завершения диалога
        const closeEndChatModalBtn = document.getElementById('closeEndChatModalBtn');
        const cancelEndChatBtn = document.getElementById('cancelEndChatBtn');
        const confirmEndChatBtn = document.getElementById('confirmEndChatBtn');

        if (closeEndChatModalBtn) {
            closeEndChatModalBtn.addEventListener('click', closeEndChatConfirmModal);
        }

        if (cancelEndChatBtn) {
            cancelEndChatBtn.addEventListener('click', closeEndChatConfirmModal);
        }

        if (confirmEndChatBtn) {
            confirmEndChatBtn.addEventListener('click', function() {
                closeEndChatConfirmModal();
                endChat();
            });
        }

        // Закрытие модального окна при клике вне его
        const endChatModal = document.getElementById('endChatConfirmModal');
        if (endChatModal) {
            endChatModal.addEventListener('click', function(e) {
                if (e.target === endChatModal) {
                    closeEndChatConfirmModal();
                }
            });
        }

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
            console.log('Кнопка истории найдена, добавляем обработчик');
            historyBtn.addEventListener('click', function() {
                console.log('Кнопка истории нажата, currentChatId:', currentChatId);
                if (!currentChatId) {
                    alert('Чат не выбран');
                    return;
                }
                showHistoryModal();
            });
        } else {
            console.error('Кнопка истории не найдена');
        }

        // Модальное окно для создания чата
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

        // Отправка сообщения
        if (messageForm) {
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
        }
    });
</script>
