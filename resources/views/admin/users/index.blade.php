<x-navigation.app>
    @section('title', 'Пользователи')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    Пользователи
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Управление пользователями системы
                </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <x-base.button href="{{ route('admin.users.create') }}">
                    Добавить пользователя
                </x-base.button>
            </div>
        </div>

        <!-- Filters -->
        <x-base.card>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-base.input 
                        name="search" 
                        placeholder="Поиск по имени, email или должности"
                        value="{{ request('search') }}"
                    />
                </div>
                <div>
                    <x-base.select 
                        name="organization" 
                        placeholder="Все организации"
                        :options="$organizations->pluck('name', 'id')->toArray()"
                        :selected="request('organization')"
                    />
                </div>
                <div class="flex space-x-2">
                    <x-base.button type="submit" variant="outline" size="sm">
                        Фильтровать
                    </x-base.button>
                    <x-base.button type="button" variant="ghost" size="sm" href="{{ route('admin.users.index') }}">
                        Сбросить
                    </x-base.button>
                </div>
            </form>
        </x-base.card>

        <!-- Users table -->
        <x-base.card class="w-full">

            <x-data.table :headers="[
                'name' => ['label' => 'Имя', 'sortable' => true],
                'email' => ['label' => 'Email', 'sortable' => true],
                'position' => ['label' => 'Должность'],
                'organization' => ['label' => 'Организация'],
                'department' => ['label' => 'Отдел'],
                'role' => ['label' => 'Роль'],
                'actions' => ['label' => 'Действия']
            ]">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <span class="text-sm font-medium text-indigo-600 dark:text-indigo-300">{{ $user->name[0] }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->phone ?: 'Телефон не указан' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $user->email }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $user->position ?: 'Не указана' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            @if($user->organizations->count() > 0)
                                {{ $user->organizations->first()->name }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">Не назначен</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            @if($user->departments->count() > 0)
                                {{ $user->departments->first()->name }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">Не назначен</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                            @if($user->roles->count() > 0)
                                <x-base.badge :variant="$user->roles->first()->slug === 'admin' ? 'danger' : ($user->roles->first()->slug === 'manager' ? 'warning' : 'default')">
                                    {{ $user->roles->first()->name }}
                                </x-base.badge>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">Не назначена</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <x-base.button variant="ghost" size="sm" href="{{ route('admin.users.edit', $user) }}">
                                    Редактировать
                                </x-base.button>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-base.button type="submit" variant="danger" size="sm">
                                        Удалить
                                    </x-base.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Пользователи не найдены
                        </td>
                    </tr>
                @endforelse
            </x-data.table>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
