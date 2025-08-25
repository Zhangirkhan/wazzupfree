window.testConnectionModal = function() {
    console.log('testConnectionModal function called');
    return {
        isOpen: false,
        isLoading: false,
        hasError: false,
        hasSuccess: false,
        errorMessage: '',
        channels: [],

        init() {
            console.log('Modal init called');
            // Инициализация компонента
            this.resetState();
        },

        openModal() {
            this.isOpen = true;
            this.resetState();
        },

        closeModal() {
            this.isOpen = false;
            this.resetState();
        },

        resetState() {
            this.isLoading = false;
            this.hasError = false;
            this.hasSuccess = false;
            this.errorMessage = '';
            this.channels = [];
        },

        async testConnection() {
            this.isLoading = true;
            this.hasError = false;
            this.hasSuccess = false;
            this.errorMessage = '';
            this.channels = [];

            try {
                const response = await fetch('/admin/settings/test-wazzup-connection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.hasSuccess = true;
                    this.channels = data.channels || [];
                } else {
                    this.hasError = true;
                    this.errorMessage = data.message || 'Ошибка при проверке подключения';
                }
            } catch (error) {
                this.hasError = true;
                this.errorMessage = 'Ошибка сети: ' + error.message;
            } finally {
                this.isLoading = false;
            }
        },

        selectChannel(channel) {
            // Находим поле Channel ID в форме и заполняем его
            const channelIdField = document.querySelector('input[name="wazzup24[channel_id]"]');
            if (channelIdField) {
                channelIdField.value = channel.id;
                
                // Показываем уведомление об успехе
                this.showNotification(`Канал "${channel.name}" выбран`, 'success');
            }
        },

        showNotification(message, type) {
            // Создаем уведомление
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
    };
};
