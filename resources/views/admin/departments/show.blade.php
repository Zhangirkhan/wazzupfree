<x-navigation.app>
    @section('title', 'Просмотр отдела')
    @section('content')
    <x-slot name="title">Просмотр отдела</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Просмотр отдела</h1>
            <div class="flex space-x-3">
                <x-base.button href="{{ route('admin.departments.edit', $department) }}" variant="primary">
                    Редактировать
                </x-base.button>
                <x-base.button href="{{ route('admin.departments.index') }}" variant="secondary">
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
                                <span class="text-white text-xl font-semibold">{{ $department->name[0] }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $department->name }}</h2>
                                <p class="text-gray-600">{{ $department->organization->name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Slug</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $department->slug }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Статус</label>
                                <div class="mt-1">
                                    <x-base.badge variant="{{ $department->is_active ? 'success' : 'danger' }}">
                                        {{ $department->is_active ? 'Активен' : 'Неактивен' }}
                                    </x-base.badge>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Дата создания</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $department->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Последнее обновление</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $department->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>

                        @if($department->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Описание</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $department->description }}</p>
                            </div>
                        @endif
                    </div>
                </x-base.card>
            </div>

            <!-- Статистика -->
            <div>
                <x-base.card>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Статистика</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $department->users->count() }}</div>
                            <div class="text-sm text-gray-500">Пользователей</div>
                        </div>
                        @if($department->children->count() > 0)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $department->children->count() }}</div>
                                <div class="text-sm text-gray-500">Подотделов</div>
                            </div>
                        @endif
                    </div>
                </x-base.card>
            </div>
        </div>

        <!-- Подотделы -->
        @if($department->children->count() > 0)
            <x-base.card>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Подотделы</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($department->children as $child)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $child->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $child->users->count() }} пользователей</p>
                                </div>
                                <x-base.badge variant="{{ $child->is_active ? 'success' : 'danger' }}">
                                    {{ $child->is_active ? 'Активен' : 'Неактивен' }}
                                </x-base.badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-base.card>
        @endif

        <!-- Пользователи отдела -->
        <x-base.card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Пользователи отдела</h3>
            </div>

            @if($department->users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Пользователь</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Роль</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($department->users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->roles->first()?->name ?: 'Роль не назначена' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-base.badge variant="{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Активен' : 'Неактивен' }}
                                        </x-base.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <x-base.button href="{{ route('admin.users.show', $user) }}" variant="secondary" size="sm">
                                                Просмотр
                                            </x-base.button>
                                            <x-base.button href="{{ route('admin.users.edit', $user) }}" variant="primary" size="sm">
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
                <p class="text-gray-500 text-center py-4">Пользователи не найдены</p>
            @endif
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
