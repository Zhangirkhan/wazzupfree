<x-navigation.app>
    @section('title', 'Чаты')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    Чаты
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Управление всеми чатами и диалогами
                </p>
            </div>
            <div class="flex space-x-2">
                <x-base.button variant="outline" href="{{ route('admin.chats.export', request()->query()) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Экспорт
                </x-base.button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 gap-4 xs:grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">
            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Активные</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Новые</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Переданные</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['transferred'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Закрытые</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['closed'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Отклоненные</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['rejected'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['messenger'] }}</div>
                    </div>
                </div>
            </x-base.card>

            <x-base.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp активные</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['messenger_active'] }}</div>
                    </div>
                </div>
            </x-base.card>
        </div>

        <!-- Filters -->
        <x-base.card>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                <div>
                    <x-base.input 
                        name="search" 
                        placeholder="Поиск по названию, телефону, ID"
                        value="{{ request('search') }}"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="status" 
                        label="Статус"
                        :options="[
                            '' => 'Все статусы',
                            'pending' => 'Новые',
                            'active' => 'Активные',
                            'closed' => 'Закрытые',
                            'transferred' => 'Переданные',
                            'rejected' => 'Отклоненные'
                        ]"
                        :selected="request('status')"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="type" 
                        label="Тип"
                        :options="[
                            '' => 'Все типы',
                            'private' => 'Приватные',
                            'group' => 'Групповые',
                            'department' => 'Отделы',
                            'organization' => 'Организации'
                        ]"
                        :selected="request('type')"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="messenger" 
                        label="Мессенджер"
                        :options="[
                            '' => 'Все чаты',
                            'true' => 'Только WhatsApp',
                            'false' => 'Без WhatsApp'
                        ]"
                        :selected="request('messenger')"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="organization_id" 
                        label="Организация"
                        :options="['' => 'Все организации'] + $organizations->pluck('name', 'id')->toArray()"
                        :selected="request('organization_id')"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="assigned_to" 
                        label="Назначен"
                        :options="['' => 'Все пользователи'] + $users->pluck('name', 'id')->toArray()"
                        :selected="request('assigned_to')"
                    />
                </div>
                <div>
                    <x-base.input 
                        name="date_from" 
                        type="date"
                        label="Дата с"
                        value="{{ request('date_from') }}"
                    />
                </div>
                <div>
                    <x-base.input 
                        name="date_to" 
                        type="date"
                        label="Дата по"
                        value="{{ request('date_to') }}"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="sort_by" 
                        label="Сортировка"
                        :options="[
                            'created_at' => 'По дате создания',
                            'updated_at' => 'По дате обновления',
                            'title' => 'По названию',
                            'status' => 'По статусу'
                        ]"
                        :selected="request('sort_by', 'created_at')"
                    />
                </div>
                <div class="flex space-x-2">
                    <x-base.button type="submit" variant="outline">
                        Фильтровать
                    </x-base.button>
                    <x-base.button type="button" variant="ghost" href="{{ route('admin.chats.index') }}">
                        Сбросить
                    </x-base.button>
                </div>
            </form>
        </x-base.card>

        <!-- Chats table -->
        <x-base.card class="w-full">
            @if($stats['pending'] > 0)
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border-b border-orange-200 dark:border-orange-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-orange-800 dark:text-orange-200">
                                У вас {{ $stats['pending'] }} новых чатов, ожидающих принятия
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.chats.bulk-accept') }}" class="inline" id="bulk-accept-form">
                            @csrf
                            <input type="hidden" name="chat_ids" id="selected-chat-ids">
                            <x-base.button type="submit" variant="success" size="sm" onclick="submitBulkAccept()">
                                Принять выбранные
                            </x-base.button>
                        </form>
                    </div>
                </div>
            @endif

            <x-data.table :headers="[
                'select' => ['label' => ''],
                'title' => ['label' => 'Название'],
                'creator' => ['label' => 'Создатель'],
                'organization' => ['label' => 'Организация'],
                'assigned_to' => ['label' => 'Назначен'],
                'participants' => ['label' => 'Участники'],
                'messages' => ['label' => 'Сообщения'],
                'status' => ['label' => 'Статус'],
                'created_at' => ['label' => 'Создан'],
                'actions' => ['label' => 'Действия']
            ]">
                @forelse($chats as $chat)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($chat->status === 'pending')
                                <input type="checkbox" name="chat_ids[]" value="{{ $chat->id }}" class="chat-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $chat->title ?: 'Без названия' }}
                                        @if($chat->wazzup_chat_id)
                                            <span class="ml-1 text-xs text-blue-600 dark:text-blue-400">(WhatsApp)</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $chat->type }}
                                        @if($chat->description)
                                            <span class="ml-1">• {{ Str::limit($chat->description, 30) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $chat->creator->name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $chat->organization->name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            @if($chat->assignedTo)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                            {{ substr($chat->assignedTo->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <span class="ml-2">{{ $chat->assignedTo->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400">Не назначен</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center space-x-1">
                                <span>{{ $chat->participants->count() }} участников</span>
                                @if($chat->phone)
                                    <span class="text-xs text-gray-500">• {{ $chat->phone }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center space-x-1">
                                <span>{{ $chat->messages->count() }}</span>
                                @if($chat->messages->first())
                                    <span class="text-xs text-gray-500">
                                        • {{ $chat->messages->first()->created_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <x-base.badge :variant="$chat->status === 'active' ? 'success' : ($chat->status === 'closed' ? 'danger' : ($chat->status === 'pending' ? 'warning' : ($chat->status === 'rejected' ? 'danger' : 'warning')))">
                                @switch($chat->status)
                                    @case('active')
                                        Активен
                                        @break
                                    @case('pending')
                                        Новый
                                        @break
                                    @case('closed')
                                        Закрыт
                                        @break
                                    @case('transferred')
                                        Передан
                                        @break
                                    @case('rejected')
                                        Отклонен
                                        @break
                                    @default
                                        {{ $chat->status }}
                                @endswitch
                            </x-base.badge>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $chat->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <x-base.button variant="ghost" size="sm" href="{{ route('admin.chats.show', $chat) }}">
                                    Просмотр
                                </x-base.button>
                                @if($chat->status === 'pending')
                                    <x-base.button variant="success" size="sm" href="{{ route('admin.chats.show', $chat) }}#accept">
                                        Принять
                                    </x-base.button>
                                    <x-base.button variant="danger" size="sm" href="{{ route('admin.chats.show', $chat) }}#reject">
                                        Отклонить
                                    </x-base.button>
                                @elseif($chat->status === 'active')
                                    <x-base.button variant="warning" size="sm" href="{{ route('admin.chats.show', $chat) }}#transfer">
                                        Передать
                                    </x-base.button>
                                    <x-base.button variant="danger" size="sm" href="{{ route('admin.chats.show', $chat) }}#close">
                                        Закрыть
                                    </x-base.button>
                                @endif
                                @if($chat->wazzup_chat_id)
                                    <x-base.badge variant="info" size="sm">
                                        WhatsApp
                                    </x-base.badge>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Чаты не найдены
                        </td>
                    </tr>
                @endforelse
            </x-data.table>

            @if($chats->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $chats->links() }}
                </div>
            @endif
        </x-base.card>
    </div>

    <script>
        function submitBulkAccept() {
            const checkboxes = document.querySelectorAll('.chat-checkbox:checked');
            const chatIds = Array.from(checkboxes).map(cb => cb.value);
            
            if (chatIds.length === 0) {
                alert('Выберите чаты для принятия');
                return false;
            }
            
            document.getElementById('selected-chat-ids').value = JSON.stringify(chatIds);
            return true;
        }

        // Обработчик для выбора всех чатов
        function selectAllChats() {
            const checkboxes = document.querySelectorAll('.chat-checkbox');
            const selectAllCheckbox = document.getElementById('select-all-chats');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }
    </script>
    @endsection
</x-navigation.app>
