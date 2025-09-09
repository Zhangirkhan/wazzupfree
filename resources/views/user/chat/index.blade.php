<x-navigation.app>
    @section('title', 'Чат')
    
    <!-- Подключаем CDN ресурсы -->
    <x-chat-cdn />
    
    <!-- Подключаем стили чата -->
    <x-chat-styles />
    
    @section('content')
    <div class="w-full" style="height: 700px;">
        <div class="h-full bg-white dark:bg-gray-800">
            <div class="flex h-full">
                <!-- Левая панель с чатами -->
                <x-chat-sidebar :chatsData="$chatsData" />

                <!-- Основное окно чата -->
                <x-chat-window 
                    :currentClient="$currentClient" 
                    :currentChat="$currentChat" 
                    :currentMessages="$currentMessages" 
                />

                <!-- Поле ввода сообщений -->
                <x-message-input :currentChat="$currentChat" />
            </div>
        </div>
    </div>

    <!-- Модальные окна -->
    <x-add-chat-modal />
    <x-media-upload-modal />
    <x-emoji-picker />
    <x-templates-picker />
    
    <!-- Дополнительные модальные окна -->
    <x-chat-modals />

    <!-- Подключаем JavaScript компоненты -->
    <x-chat-scripts :currentChat="$currentChat" :currentMessages="$currentMessages" />
    <x-chat-functions />
    <x-chat-utils />

    @endsection
</x-navigation.app>