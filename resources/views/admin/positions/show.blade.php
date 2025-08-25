<x-navigation.app>
    @section('title', 'Просмотр должности')
    @section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Просмотр должности</h1>
            <div class="flex space-x-3">
                <x-base.button href="{{ route('admin.positions.edit', $position) }}" variant="primary">
                    Редактировать
                </x-base.button>
                <x-base.button href="{{ route('admin.positions.index') }}" variant="secondary">
                    ← Назад к списку
                </x-base.button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Основная информация -->
            <div class="lg:col-span-2">
                <x-base.card>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $position->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $position->slug }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Статус</label>
                                <div class="mt-1">
                                    <x-base.badge :variant="$position->is_active ? 'success' : 'danger'">
                                        {{ $position->is_active ? 'Активна' : 'Неактивна' }}
                                    </x-base.badge>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Порядок сортировки</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $position->sort_order }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Пользователей</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $position->users->count() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Дата создания</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $position->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>

                        @if($position->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Описание</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $position->description }}</p>
                            </div>
                        @endif

                        @if($position->permissions)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Права доступа</label>
                                <div class="mt-2 space-y-1">
                                    @foreach($position->permissions as $permission)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            {{ $permission }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </x-base.card>
            </div>

            <!-- Боковая панель -->
            <div class="space-y-6">
                <!-- Статистика -->
                <x-base.card title="Статистика">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Всего пользователей</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $position->users->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Активных</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $position->users->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                </x-base.card>

                <!-- Быстрые действия -->
                <x-base.card title="Действия">
                    <div class="space-y-2">
                        <x-base.button href="{{ route('admin.positions.edit', $position) }}" variant="primary" size="sm" class="w-full">
                            Редактировать
                        </x-base.button>
                        @if($position->users->count() === 0)
                            <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <x-base.button type="submit" variant="danger" size="sm" class="w-full" onclick="return confirm('Вы уверены?')">
                                    Удалить
                                </x-base.button>
                            </form>
                        @endif
                    </div>
                </x-base.card>
            </div>
        </div>

        <!-- Пользователи с этой должностью -->
        @if($position->users->count() > 0)
            <x-base.card title="Пользователи с этой должностью">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Пользователь</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Организация</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Отдел</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Основная</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дата назначения</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($position->users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $user->name[0] }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $user->pivot->organization_id ? $user->organizations->where('id', $user->pivot->organization_id)->first()?->name : 'Не указана' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $user->pivot->department_id ? $user->departments->where('id', $user->pivot->department_id)->first()?->name : 'Не указан' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-base.badge :variant="$user->pivot->is_primary ? 'success' : 'secondary'">
                                            {{ $user->pivot->is_primary ? 'Да' : 'Нет' }}
                                        </x-base.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $user->pivot->assigned_at ? \Carbon\Carbon::parse($user->pivot->assigned_at)->format('d.m.Y') : 'Не указана' }}
                                        </div>
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
