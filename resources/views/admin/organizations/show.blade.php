<x-navigation.app>
    @section('title', 'Просмотр организации')
    @section('content')
    <x-slot name="title">Просмотр организации</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Просмотр организации</h1>
            <div class="flex space-x-3">
                <x-base.button href="{{ route('admin.organizations.edit', $organization) }}" variant="primary">
                    Редактировать
                </x-base.button>
                <x-base.button href="{{ route('admin.organizations.index') }}" variant="secondary">
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
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-semibold">{{ $organization->name[0] }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $organization->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $organization->domain ?: 'Домен не указан' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Slug</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $organization->slug }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Статус</label>
                                <div class="mt-1">
                                    <x-base.badge variant="{{ $organization->is_active ? 'success' : 'danger' }}">
                                        {{ $organization->is_active ? 'Активна' : 'Неактивна' }}
                                    </x-base.badge>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Дата создания</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $organization->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Последнее обновление</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $organization->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>

                        @if($organization->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Описание</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $organization->description }}</p>
                            </div>
                        @endif
                    </div>
                </x-base.card>
            </div>

            <!-- Статистика -->
            <div>
                <x-base.card>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Статистика</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $organization->departments->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Отделов</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $organization->users->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Пользователей</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $organization->chats->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Чатов</div>
                        </div>
                    </div>
                </x-base.card>
            </div>
        </div>

        <!-- Отделы -->
        <x-base.card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Отделы организации</h3>
                <x-base.button href="{{ route('admin.departments.create') }}?organization_id={{ $organization->id }}" variant="primary">
                    Добавить отдел
                </x-base.button>
            </div>

            @if($organization->departments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Описание</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Пользователей</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($organization->departments as $department)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $department->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($department->description, 50) ?: 'Нет описания' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-base.badge variant="{{ $department->is_active ? 'success' : 'danger' }}">
                                            {{ $department->is_active ? 'Активен' : 'Неактивен' }}
                                        </x-base.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $department->users->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <x-base.button href="{{ route('admin.departments.show', $department) }}" variant="secondary" size="sm">
                                                Просмотр
                                            </x-base.button>
                                            <x-base.button href="{{ route('admin.departments.edit', $department) }}" variant="primary" size="sm">
                                                Редактировать
                                            </x-base.button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Отделы не найдены</p>
            @endif
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
