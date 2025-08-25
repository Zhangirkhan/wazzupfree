<x-navigation.app>
    @section('title', 'Отделы')
    @section('content')
    <x-slot name="title">Отделы</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Отделы</h1>
            <x-base.button href="{{ route('admin.departments.create') }}" variant="primary">
                Добавить отдел
            </x-base.button>
        </div>

        <x-base.card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Организация</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователей</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $department->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $department->organization->name }}
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
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
