<x-navigation.app>
    @section('title', 'Должности')
    @section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Должности</h1>
            <x-base.button href="{{ route('admin.positions.create') }}" variant="primary">
                Добавить должность
            </x-base.button>
        </div>

        <!-- Search and filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form method="GET" action="{{ route('admin.positions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-base.input 
                            name="search" 
                            label="Поиск"
                            value="{{ request('search') }}"
                            placeholder="Поиск по названию..."
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
                    
                    <div class="flex items-end">
                        <x-base.button type="submit" variant="primary" class="w-full">
                            Применить фильтры
                        </x-base.button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Positions list -->
        <x-base.card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Пользователей</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Порядок</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($positions as $position)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $position->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $position->slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ Str::limit($position->description, 50) ?: 'Описание не указано' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $position->users_count }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-base.badge :variant="$position->is_active ? 'success' : 'danger'">
                                        {{ $position->is_active ? 'Активна' : 'Неактивна' }}
                                    </x-base.badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $position->sort_order }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <x-base.button href="{{ route('admin.positions.show', $position) }}" variant="secondary" size="sm">
                                            Просмотр
                                        </x-base.button>
                                        <x-base.button href="{{ route('admin.positions.edit', $position) }}" variant="primary" size="sm">
                                            Редактировать
                                        </x-base.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Должности не найдены
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($positions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $positions->links() }}
                </div>
            @endif
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
