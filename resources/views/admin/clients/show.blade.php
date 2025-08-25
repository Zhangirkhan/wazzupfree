<x-navigation.app>
    @section('title', 'Просмотр клиента')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    {{ $client->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Информация о клиенте
                </p>
            </div>
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'admin')
                    <x-base.button href="{{ route('admin.clients.edit', $client) }}" variant="primary">
                        Редактировать
                    </x-base.button>
                @endif
                <x-base.button href="{{ route('admin.clients.index') }}" variant="outline">
                    Назад к списку
                </x-base.button>
            </div>
        </div>

        <!-- Client info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main info -->
            <div class="lg:col-span-2">
                <x-base.card>
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $client->name[0] }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $client->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Клиент</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Телефон</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->phone }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">UUID Wazzup</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($client->uuid_wazzup)
                                    <span class="font-mono">{{ $client->uuid_wazzup }}</span>
                                @else
                                    <span class="text-gray-400">Не указан</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Статус</label>
                            <div class="mt-1">
                                <x-base.badge :variant="$client->is_active ? 'success' : 'danger'">
                                    {{ $client->is_active ? 'Активен' : 'Неактивен' }}
                                </x-base.badge>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата создания</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    @if($client->comment)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Комментарий</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->comment }}</p>
                        </div>
                    @endif
                </x-base.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats -->
                <x-base.card>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Статистика</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Всего чатов</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->chats->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Активных чатов</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->chats->where('status', 'active')->count() }}</span>
                        </div>
                    </div>
                </x-base.card>

                <!-- Actions -->
                <x-base.card>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Действия</h3>
                    <div class="space-y-3">
                        @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" 
                                  onsubmit="return confirm('Вы уверены, что хотите удалить этого клиента?')">
                                @csrf
                                @method('DELETE')
                                <x-base.button type="submit" variant="danger" class="w-full">
                                    Удалить клиента
                                </x-base.button>
                            </form>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">
                                Только администраторы могут удалять клиентов
                            </div>
                        @endif
                    </div>
                </x-base.card>
            </div>
        </div>

        <!-- Recent chats -->
        @if($client->chats->count() > 0)
            <x-base.card>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Последние чаты</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Создан</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($client->chats as $chat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $chat->title ?: 'Без названия' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-base.badge :variant="$chat->status === 'active' ? 'success' : 'warning'">
                                            {{ $chat->status === 'active' ? 'Активен' : 'Закрыт' }}
                                        </x-base.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $chat->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-base.button href="{{ route('admin.chats.show', $chat) }}" variant="secondary" size="sm">
                                            Просмотр
                                        </x-base.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-base.card>
        @endif
    </div>
    @endsection
</x-navigation.app>
