window.integrationToggle = function(enabled, integrationName) {
    return {
        enabled: enabled,
        showPasswordModal: false,
        password: '',
        loading: false,
        error: '',
        
        async toggleIntegration() {
            if (this.enabled) {
                // Если включаем - просто включаем
                await this.enableIntegration();
            } else {
                // Если выключаем - запрашиваем пароль
                this.showPasswordModal = true;
            }
        },
        
        async enableIntegration() {
            this.loading = true;
            try {
                const response = await fetch('/admin/settings/toggle-integration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        integration: integrationName,
                        enabled: true
                    })
                });
                
                if (response.ok) {
                    this.enabled = true;
                    // Показываем уведомление об успехе
                    this.showNotification('Интеграция успешно включена', 'success');
                } else {
                    throw new Error('Ошибка при включении интеграции');
                }
            } catch (error) {
                this.error = error.message;
                this.showNotification('Ошибка при включении интеграции', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async disableIntegration() {
            if (!this.password.trim()) {
                this.error = 'Введите пароль';
                return;
            }
            
            this.loading = true;
            this.error = '';
            
            try {
                const response = await fetch('/admin/settings/toggle-integration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        integration: integrationName,
                        enabled: false,
                        password: this.password
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.enabled = false;
                    this.showPasswordModal = false;
                    this.password = '';
                    this.showNotification('Интеграция успешно отключена', 'success');
                } else {
                    if (data.error === 'invalid_password') {
                        this.error = 'Неверный пароль';
                    } else {
                        throw new Error(data.message || 'Ошибка при отключении интеграции');
                    }
                }
            } catch (error) {
                this.error = error.message;
                this.showNotification('Ошибка при отключении интеграции', 'error');
            } finally {
                this.loading = false;
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
}
