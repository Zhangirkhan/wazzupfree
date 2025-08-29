@props(['class' => ''])

<div x-data="notificationBell()" class="{{ $class }}">
    <!-- Кнопка уведомлений -->
    <button 
        @click="toggleNotifications()"
        type="button" 
        class="relative p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
    >
        <span class="sr-only">Уведомления</span>
        
        <!-- Иконка колокольчика -->
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 00-6 6v3.75a6 6 0 006 6h3a6 6 0 006-6V9.75a6 6 0 00-6-6h-3z" />
        </svg>
        
        <!-- Индикатор новых уведомлений -->
        <div 
            x-show="unreadCount > 0"
            class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full flex items-center justify-center"
        >
            <span class="text-xs font-medium text-white" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
        </div>
    </button>

    <!-- Выпадающее меню уведомлений -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
        @click.away="closeNotifications()"
        style="display: none;"
    >
        <!-- Заголовок -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Уведомления</h3>
                <button 
                    @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Отметить все как прочитанные
                </button>
            </div>
        </div>

        <!-- Список уведомлений -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 00-6 6v3.75a6 6 0 006 6h3a6 6 0 006-6V9.75a6 6 0 00-6-6h-3z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Нет новых уведомлений</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div 
                    @click="openChat(notification.chat_id)"
                    class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                    :class="notification.is_read ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20'"
                >
                    <div class="flex items-start space-x-3">
                        <!-- Аватар клиента -->
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-gray-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white" x-text="notification.client_name ? notification.client_name.charAt(0).toUpperCase() : 'К'"></span>
                            </div>
                        </div>
                        
                        <!-- Содержимое уведомления -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.client_name || notification.client_phone"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTime(notification.created_at)"></p>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1" x-text="notification.message_preview"></p>
                            <div class="flex items-center mt-1 space-x-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="notification.department_name"></span>
                                <template x-if="!notification.is_read">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Новое
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Футер -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <a 
                href="{{ route('user.chat.index') }}" 
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            >
                Посмотреть все чаты →
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        init() {
            this.loadNotifications();
            // Обновляем уведомления каждые 30 секунд
            setInterval(() => {
                this.loadNotifications();
            }, 30000);
        },
        toggleNotifications() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },
        closeNotifications() {
            this.isOpen = false;
        },
        async loadNotifications() {
            try {
                const response = await fetch('{{ route("user.notifications") }}');
                const data = await response.json();
                
                if (data.success) {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Ошибка загрузки уведомлений:', error);
            }
        },
        async markAllAsRead() {
            try {
                const response = await fetch('{{ route("user.notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    this.unreadCount = 0;
                    this.notifications.forEach(n => n.is_read = true);
                }
            } catch (error) {
                console.error('Ошибка отметки уведомлений:', error);
            }
        },
        openChat(chatId) {
            // Открываем чат в новой вкладке или переходим на страницу чата
            window.location.href = `{{ route('user.chat.index') }}?chat=${chatId}`;
        },
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            // Если меньше минуты
            if (diff < 60000) {
                return 'Только что';
            }
            
            // Если меньше часа
            if (diff < 3600000) {
                const minutes = Math.floor(diff / 60000);
                return `${minutes} мин назад`;
            }
            
            // Если меньше дня
            if (diff < 86400000) {
                const hours = Math.floor(diff / 3600000);
                return `${hours} ч назад`;
            }
            
            // Если больше дня
            const days = Math.floor(diff / 86400000);
            return `${days} дн назад`;
        }
    }
}
</script>
