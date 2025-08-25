<x-navigation.app>
    @section('title', 'Настройки')
    @section('content')
    <x-slot name="title">Настройки</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Настройки системы</h1>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Wazzup24 Settings -->
            <x-base.card>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25s-9-3.694-9-8.25S7.444 3.75 12 3.75s9 3.694 9 8.25z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Wazzup24 Integration</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Настройки интеграции с WhatsApp</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-base.input 
                        name="wazzup24[api_key]" 
                        label="API Key" 
                        value="{{ $settings['wazzup24']['api_key'] }}"
                        placeholder="Введите API ключ Wazzup24"
                        help="Получите API ключ в личном кабинете Wazzup24"
                    />
                    
                    <x-base.input 
                        name="wazzup24[channel_id]" 
                        label="Channel ID" 
                        value="{{ $settings['wazzup24']['channel_id'] }}"
                        placeholder="ID канала WhatsApp"
                        help="ID канала для отправки сообщений"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-base.input 
                        name="wazzup24[webhook_url]" 
                        label="Webhook URL" 
                        value="{{ $settings['wazzup24']['webhook_url'] }}"
                        placeholder="https://your-domain.com/api/webhook"
                        help="URL для получения входящих сообщений"
                    />
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Интеграция Wazzup24</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Активировать интеграцию с Wazzup24</p>
                        </div>
                        <x-settings.integration-toggle 
                            :enabled="$settings['wazzup24']['enabled']"
                            integration-name="wazzup24"
                        />
                    </div>
                </div>

                <!-- Test connection button -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Проверка подключения</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Проверить подключение и получить список каналов</p>
                        </div>
                        <x-base.button 
                            type="button" 
                            variant="outline" 
                            onclick="openTestConnectionModal()"
                        >
                            Проверить подключение
                        </x-base.button>
                    </div>
                </div>
            </x-base.card>

            <!-- Chat Settings -->
            <x-base.card>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25s-9-3.694-9-8.25S7.444 3.75 12 3.75s9 3.694 9 8.25z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Настройки чата</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Параметры работы чат-системы</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-base.input 
                        type="number"
                        name="chat[inactive_days]" 
                        label="Дни неактивности" 
                        value="{{ $settings['chat']['inactive_days'] }}"
                        help="Количество дней для автоматического закрытия чата"
                    />
                    
                    <x-base.input 
                        type="number"
                        name="chat[max_message_length]" 
                        label="Максимальная длина сообщения" 
                        value="{{ $settings['chat']['max_message_length'] }}"
                        help="Максимальное количество символов в сообщении"
                    />
                    
                    <x-base.checkbox 
                        name="chat[auto_close_enabled]" 
                        label="Автозакрытие чатов"
                        checked="{{ $settings['chat']['auto_close_enabled'] }}"
                        help="Автоматически закрывать неактивные чаты"
                    />
                </div>
            </x-base.card>

            <!-- App Settings -->
            <x-base.card>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Настройки приложения</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Основные параметры системы</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-base.input 
                        name="app[name]" 
                        label="Название приложения" 
                        value="{{ $settings['app']['name'] }}"
                        readonly
                        help="Название отображается в заголовках страниц"
                    />
                    
                    <x-base.input 
                        name="app[url]" 
                        label="URL приложения" 
                        value="{{ $settings['app']['url'] }}"
                        readonly
                        help="Основной URL системы"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-base.input 
                        name="app[timezone]" 
                        label="Часовой пояс" 
                        value="{{ $settings['app']['timezone'] ?? 'Asia/Almaty (UTC+5)' }}"
                        readonly
                        help="Часовой пояс для отображения времени"
                    />
                    
                    <x-base.input 
                        name="app[locale]" 
                        label="Язык" 
                        value="{{ $settings['app']['locale'] ?? 'ru (Русский)' }}"
                        readonly
                        help="Язык интерфейса"
                    />
                </div>
            </x-base.card>

            <div class="flex justify-end">
                <x-base.button type="submit" variant="primary">
                    Сохранить настройки
                </x-base.button>
            </div>
        </form>


    </div>
    @endsection
</x-navigation.app>
