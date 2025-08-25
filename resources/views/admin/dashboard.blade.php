<x-navigation.app>
    @section('title', 'Dashboard')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                Добро пожаловать, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Ваша персональная панель управления
            </p>
        </div>

        <!-- Моя статистика -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Моя активность</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Мои сообщения</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $userStats['my_messages'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Мои чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $userStats['my_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Активные чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $userStats['my_active_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Мессенджер чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $userStats['my_messenger_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>
            </div>
        </div>

        @if(!empty($departmentStats))
        <!-- Статистика отдела -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Отдел: {{ $departmentStats['department_name'] }}</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Сотрудники</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $departmentStats['department_users'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Чаты отдела</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $departmentStats['department_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Активные чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $departmentStats['department_active_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Мессенджер чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $departmentStats['department_messenger_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Сообщения</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $departmentStats['department_messages'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>
            </div>
        </div>
        @endif

        <!-- Recent activity -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Мои недавние чаты -->
            <x-base.card title="Мои недавние чаты" subtitle="Чаты, которые вы ведете">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentChats as $chat)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $chat->title ?: 'Без названия' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $chat->organization->name ?? 'Без организации' }}
                                        </p>
                                    </div>
                                    <div>
                                        <x-base.badge :variant="$chat->status === 'active' ? 'success' : 'warning'">
                                            {{ $chat->status === 'active' ? 'Активен' : 'Закрыт' }}
                                        </x-base.badge>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500 dark:text-gray-400">
                                У вас пока нет активных чатов
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="mt-6">
                    <x-base.button variant="outline" size="sm" href="{{ route('user.chat.index') }}">
                        Перейти к чатам
                    </x-base.button>
                </div>
            </x-base.card>

            @if(!empty($colleagues))
            <!-- Коллеги из отдела -->
            <x-base.card title="Коллеги из отдела" subtitle="Сотрудники вашего отдела">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($colleagues as $colleague)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">{{ $colleague->name[0] }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $colleague->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $colleague->position ?: 'Должность не указана' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $colleague->messages_count }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">сообщений</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </x-base.card>
            @else
            <!-- Недавние сообщения -->
            <x-base.card title="Мои недавние сообщения" subtitle="Последние отправленные сообщения">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentMessages as $message)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ Str::limit($message->content, 50) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $message->created_at->format('d.m.Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500 dark:text-gray-400">
                                У вас пока нет отправленных сообщений
                            </li>
                        @endforelse
                    </ul>
                </div>
            </x-base.card>
            @endif
        </div>

        @if(auth()->user()->role === 'admin' && !empty($adminStats))
        <!-- Админская статистика -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Общая статистика системы</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Всего пользователей</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $adminStats['total_users'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Организации</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $adminStats['total_organizations'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Всего сообщений</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $adminStats['total_messages'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Активные чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $adminStats['total_active_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>

                <x-base.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Мессенджер чаты</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $adminStats['total_messenger_chats'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </x-base.card>
            </div>
        </div>
        @endif
    </div>
    @endsection
</x-navigation.app>
