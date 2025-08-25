<x-navigation.app>
    @section('title', 'Клиенты')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    Клиенты
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Управление клиентами системы
                </p>
            </div>
            <div class="flex space-x-2">
                @if(auth()->user()->role === 'admin')
                    <x-base.button variant="outline" href="{{ route('admin.clients.wazzup.preview') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Просмотр из Wazzup24
                    </x-base.button>
                    <x-base.button href="{{ route('admin.clients.create') }}" variant="primary">
                        Добавить клиента
                    </x-base.button>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <x-base.card>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
                <div>
                    <x-base.input 
                        name="search" 
                        placeholder="Поиск по имени, телефону или UUID"
                        value="{{ request('search') }}"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="status" 
                        label="Статус"
                        :options="[
                            '' => 'Все статусы',
                            'active' => 'Активные',
                            'inactive' => 'Неактивные'
                        ]"
                        :selected="request('status')"
                    />
                </div>
                <div class="flex space-x-2">
                    <x-base.button type="submit" variant="outline">
                        Фильтровать
                    </x-base.button>
                    <x-base.button type="button" variant="ghost" href="{{ route('admin.clients.index') }}">
                        Сбросить
                    </x-base.button>
                </div>
            </form>
        </x-base.card>

        <!-- Clients table -->
        <x-base.card class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Список клиентов</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">UUID Wazzup</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Чаты</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ $client->name[0] }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $client->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Создан {{ $client->created_at->format('d.m.Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $client->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($client->uuid_wazzup)
                                        <span class="font-mono text-xs">{{ Str::limit($client->uuid_wazzup, 20) }}</span>
                                    @else
                                        <span class="text-gray-400">Не указан</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-base.badge :variant="$client->is_active ? 'success' : 'danger'">
                                        {{ $client->is_active ? 'Активен' : 'Неактивен' }}
                                    </x-base.badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $client->chats_count ?? 0 }} чатов
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <x-base.button href="{{ route('admin.clients.show', $client) }}" variant="secondary" size="sm">
                                            Просмотр
                                        </x-base.button>
                                        <form method="POST" action="{{ route('admin.clients.start-chat', $client) }}" class="inline">
                                            @csrf
                                            <x-base.button type="submit" variant="success" size="sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                Начать чат
                                            </x-base.button>
                                        </form>
                                        @if(auth()->user()->role === 'admin')
                                            <x-base.button href="{{ route('admin.clients.edit', $client) }}" variant="primary" size="sm">
                                                Редактировать
                                            </x-base.button>
                                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="inline" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этого клиента?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-base.button type="submit" variant="danger" size="sm">
                                                    Удалить
                                                </x-base.button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Клиенты не найдены
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $clients->links() }}
                </div>
            @endif
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
