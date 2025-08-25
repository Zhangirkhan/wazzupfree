<x-navigation.app>
    @section('title', 'Клиенты из Wazzup24')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    Клиенты из Wazzup24
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Предварительный просмотр клиентов для импорта
                </p>
            </div>
            <div class="flex space-x-2">
                <x-base.button variant="outline" href="{{ route('admin.clients.index') }}">
                    Назад к клиентам
                </x-base.button>
                <form method="POST" action="{{ route('admin.clients.wazzup.import') }}" class="inline">
                    @csrf
                    <input type="hidden" name="limit" value="{{ request('limit', 50) }}">
                    <input type="hidden" name="from_preview" value="1">
                    <x-base.button type="submit" variant="success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Импортировать всех
                    </x-base.button>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <x-base.card>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
                <div>
                    <x-base.input 
                        name="limit" 
                        type="number"
                        label="Количество клиентов"
                        value="{{ request('limit', 50) }}"
                        min="1"
                        max="500"
                    />
                </div>
                <div class="flex space-x-2">
                    <x-base.button type="submit" variant="outline">
                        Обновить
                    </x-base.button>
                    <x-base.button type="button" variant="ghost" href="{{ route('admin.clients.wazzup.preview') }}">
                        Сбросить
                    </x-base.button>
                </div>
            </form>
        </x-base.card>

        <!-- Clients preview -->
        <x-base.card class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Найдено клиентов: {{ $total }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">UUID Wazzup</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Последнее сообщение</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                                {{ $client['name'][0] }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $client['name'] }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Из Wazzup24
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $client['phone'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($client['uuid_wazzup'])
                                        <span class="font-mono text-xs">{{ Str::limit($client['uuid_wazzup'], 20) }}</span>
                                    @else
                                        <span class="text-gray-400">Не указан</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div class="max-w-xs">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ Str::limit($client['last_message'], 50) }}
                                        </div>
                                        @if($client['last_message_date'])
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($client['last_message_date'])->format('d.m.Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $existingClient = \App\Models\Client::where('phone', $client['phone'])->first();
                                    @endphp
                                    @if($existingClient)
                                        <x-base.badge variant="warning">
                                            Существует
                                        </x-base.badge>
                                    @else
                                        <x-base.badge variant="success">
                                            Новый
                                        </x-base.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if($existingClient)
                                            <x-base.button href="{{ route('admin.clients.edit', $existingClient) }}" variant="primary" size="sm">
                                                Редактировать
                                            </x-base.button>
                                        @else
                                            <form method="POST" action="{{ route('admin.clients.wazzup.import') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="limit" value="1">
                                                <input type="hidden" name="phone" value="{{ $client['phone'] }}">
                                                <input type="hidden" name="from_preview" value="1">
                                                <x-base.button type="submit" variant="success" size="sm">
                                                    Импортировать
                                                </x-base.button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Клиенты не найдены в Wazzup24
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
