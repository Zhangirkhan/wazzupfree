@props(['chat'])

<div x-data="wazzup24MessageForm({{ $chat->id }})" class="space-y-4">
    <div class="flex items-start space-x-3">
        <div class="flex-1">
            <textarea 
                x-model="message" 
                @keydown.enter.prevent="sendMessage"
                placeholder="Введите сообщение..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 resize-none"
                rows="3"
                :disabled="sending"
            ></textarea>
        </div>
        <div class="flex flex-col space-y-2">
            <button 
                @click="sendMessage"
                :disabled="sending || !message.trim()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-indigo-500 dark:hover:bg-indigo-600"
            >
                <span x-show="!sending">Отправить</span>
                <span x-show="sending" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Отправка...
                </span>
            </button>
        </div>
    </div>

    <!-- Status messages -->
    <div x-show="statusMessage" x-text="statusMessage" 
         :class="statusType === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
         class="text-sm font-medium"></div>

    <!-- Connection status -->
    <div class="flex items-center space-x-2 text-sm">
        <div class="w-2 h-2 rounded-full" :class="connectionStatus === 'connected' ? 'bg-green-500' : 'bg-red-500'"></div>
        <span x-text="connectionStatus === 'connected' ? 'Wazzup24 подключен' : 'Wazzup24 не подключен'" 
              :class="connectionStatus === 'connected' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"></span>
    </div>
</div>

<script>
function wazzup24MessageForm(chatId) {
    return {
        message: '',
        sending: false,
        statusMessage: '',
        statusType: 'success',
        connectionStatus: 'checking',

        init() {
            this.checkConnection();
        },

        async sendMessage() {
            if (!this.message.trim() || this.sending) return;

            this.sending = true;
            this.statusMessage = '';
            this.statusType = 'success';

            try {
                const response = await fetch(`/api/chats/${chatId}/wazzup24/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.message
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.statusMessage = 'Сообщение отправлено успешно';
                    this.statusType = 'success';
                    this.message = '';
                    
                    // Обновляем список сообщений (если есть)
                    if (window.chatMessages) {
                        window.chatMessages.loadMessages();
                    }
                } else {
                    this.statusMessage = data.message || 'Ошибка отправки сообщения';
                    this.statusType = 'error';
                }

            } catch (error) {
                console.error('Error sending message:', error);
                this.statusMessage = 'Ошибка отправки сообщения';
                this.statusType = 'error';
            } finally {
                this.sending = false;
                
                // Очищаем статус через 5 секунд
                setTimeout(() => {
                    this.statusMessage = '';
                }, 5000);
            }
        },

        async checkConnection() {
            try {
                const response = await fetch('/api/wazzup24/connection', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.connectionStatus = data.success ? 'connected' : 'disconnected';
            } catch (error) {
                console.error('Error checking connection:', error);
                this.connectionStatus = 'disconnected';
            }
        }
    }
}
</script>
