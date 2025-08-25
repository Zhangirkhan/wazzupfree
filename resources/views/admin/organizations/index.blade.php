<x-navigation.app>
    @section('title', 'Организации')
    @section('content')
    <x-slot name="title">Компании</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Компании</h1>
            <x-base.button href="{{ route('admin.organizations.create') }}" variant="primary">
                Добавить компанию
            </x-base.button>
        </div>

        <x-base.card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Домен</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Отделов</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователей</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($organizations as $organization)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $organization->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $organization->domain ?: 'Не указан' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-base.badge variant="{{ $organization->is_active ? 'success' : 'danger' }}">
                                        {{ $organization->is_active ? 'Активна' : 'Неактивна' }}
                                    </x-base.badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $organization->departments->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $organization->users->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <x-base.button href="{{ route('admin.organizations.show', $organization) }}" variant="secondary" size="sm">
                                            Просмотр
                                        </x-base.button>
                                        <x-base.button href="{{ route('admin.organizations.edit', $organization) }}" variant="primary" size="sm">
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
