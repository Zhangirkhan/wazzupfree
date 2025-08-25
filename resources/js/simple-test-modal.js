// Простая версия модального окна без Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing simple modal');
    
    // Создаем модальное окно
    const modal = document.createElement('div');
    modal.id = 'test-connection-modal';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto hidden';
    modal.innerHTML = `
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6">
                
                <!-- Modal header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25s-9-3.694-9-8.25S7.444 3.75 12 3.75s9 3.694 9 8.25z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Проверка подключения Wazzup24
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Проверка API ключа и получение каналов
                            </p>
                        </div>
                    </div>
                    <button 
                        id="close-modal"
                        type="button" 
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    >
                        <span class="sr-only">Закрыть</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div id="modal-content" class="space-y-6">
                    <!-- Initial state -->
                    <div id="initial-state" class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Нажмите кнопку "Проверить" для тестирования подключения к Wazzup24 API
                        </p>
                    </div>

                    <!-- Loading state -->
                    <div id="loading-state" class="text-center py-8 hidden">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="animate-spin w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Проверяем подключение к Wazzup24...
                        </p>
                    </div>

                    <!-- Error state -->
                    <div id="error-state" class="text-center py-8 hidden">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Ошибка подключения</h4>
                        <p id="error-message" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>
                        <div class="text-left bg-red-50 dark:bg-red-900/10 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Возможные причины:</h5>
                            <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                <li>• Неверный API ключ</li>
                                <li>• Отсутствует подключение к интернету</li>
                                <li>• Сервис Wazzup24 недоступен</li>
                                <li>• Истек срок действия API ключа</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Success state -->
                    <div id="success-state" class="space-y-6 hidden">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Подключение успешно!</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                API ключ работает корректно. Найдено каналов: <span id="channels-count" class="font-medium">0</span>
                            </p>
                        </div>

                        <!-- Channels list -->
                        <div id="channels-list" class="hidden">
                            <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Доступные каналы:</h5>
                            <div id="channels-container" class="space-y-2 max-h-64 overflow-y-auto">
                            </div>
                        </div>

                        <!-- No channels -->
                        <div id="no-channels" class="text-center py-4 hidden">
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Каналы не найдены. Проверьте настройки в личном кабинете Wazzup24.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        id="close-modal-btn"
                        type="button" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Закрыть
                    </button>
                    <button 
                        id="test-connection-btn"
                        type="button" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Проверить подключение
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Состояние модального окна
    let modalState = {
        isLoading: false,
        hasError: false,
        hasSuccess: false,
        channels: []
    };
    
    // Функции управления состоянием
    function showState(stateName) {
        // Скрываем все состояния
        document.getElementById('initial-state').classList.add('hidden');
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.add('hidden');
        document.getElementById('success-state').classList.add('hidden');
        
        // Показываем нужное состояние
        if (stateName === 'initial') {
            document.getElementById('initial-state').classList.remove('hidden');
        } else if (stateName === 'loading') {
            document.getElementById('loading-state').classList.remove('hidden');
        } else if (stateName === 'error') {
            document.getElementById('error-state').classList.remove('hidden');
        } else if (stateName === 'success') {
            document.getElementById('success-state').classList.remove('hidden');
        }
    }
    
    function updateButtonState() {
        const testBtn = document.getElementById('test-connection-btn');
        if (modalState.isLoading) {
            testBtn.disabled = true;
            testBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Проверка...
            `;
        } else {
            testBtn.disabled = false;
            testBtn.innerHTML = 'Проверить подключение';
        }
    }
    
    // Функция открытия модального окна
    window.openTestConnectionModal = function() {
        console.log('Opening modal');
        modal.classList.remove('hidden');
        showState('initial');
        modalState = {
            isLoading: false,
            hasError: false,
            hasSuccess: false,
            channels: []
        };
        updateButtonState();
    };
    
    // Функция закрытия модального окна
    function closeModal() {
        console.log('Closing modal');
        modal.classList.add('hidden');
    }
    
    // Функция тестирования подключения
    async function testConnection() {
        console.log('Testing connection');
        modalState.isLoading = true;
        showState('loading');
        updateButtonState();
        
        try {
            const response = await fetch('/admin/settings/test-wazzup-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            console.log('Response:', data);

            if (response.ok) {
                modalState.hasSuccess = true;
                modalState.channels = data.channels || [];
                showState('success');
                
                // Обновляем количество каналов
                document.getElementById('channels-count').textContent = modalState.channels.length;
                
                // Показываем список каналов или сообщение об отсутствии
                if (modalState.channels.length > 0) {
                    document.getElementById('channels-list').classList.remove('hidden');
                    document.getElementById('no-channels').classList.add('hidden');
                    
                    // Заполняем список каналов
                    const container = document.getElementById('channels-container');
                    container.innerHTML = '';
                    
                    modalState.channels.forEach(channel => {
                        const channelElement = document.createElement('div');
                        channelElement.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
                        channelElement.innerHTML = `
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25s-9-3.694-9-8.25S7.444 3.75 12 3.75s9 3.694 9 8.25z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${channel.name || channel.channelId}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">${channel.channelId || channel.id}</p>
                                </div>
                            </div>
                            <button 
                                onclick="selectChannel('${channel.channelId || channel.id}', '${channel.name || channel.channelId}')"
                                type="button"
                                class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700"
                            >
                                Выбрать
                            </button>
                        `;
                        container.appendChild(channelElement);
                    });
                } else {
                    document.getElementById('channels-list').classList.add('hidden');
                    document.getElementById('no-channels').classList.remove('hidden');
                }
            } else {
                modalState.hasError = true;
                document.getElementById('error-message').textContent = data.message || 'Ошибка при проверке подключения';
                showState('error');
            }
        } catch (error) {
            console.error('Error:', error);
            modalState.hasError = true;
            document.getElementById('error-message').textContent = 'Ошибка сети: ' + error.message;
            showState('error');
        } finally {
            modalState.isLoading = false;
            updateButtonState();
        }
    }
    
    // Функция выбора канала
    window.selectChannel = function(channelId, channelName) {
        console.log('Selecting channel:', channelId, channelName);
        
        // Находим поле Channel ID в форме и заполняем его
        const channelIdField = document.querySelector('input[name="wazzup24[channel_id]"]');
        if (channelIdField) {
            channelIdField.value = channelId;
            
            // Показываем уведомление об успехе
            showNotification(`Канал "${channelName}" выбран`, 'success');
        }
    };
    
    // Функция показа уведомлений
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Анимация появления
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Автоматическое скрытие
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Обработчики событий
    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    document.getElementById('test-connection-btn').addEventListener('click', testConnection);
    
    // Закрытие по клику вне модального окна
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    console.log('Simple modal initialized');
});
