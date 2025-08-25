<x-navigation.app>
    @section('title', 'Профиль')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                Профиль
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Информация о вашем аккаунте
            </p>
        </div>

        <!-- Profile info -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Personal info -->
            <x-base.card title="Личная информация">
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Имя</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Телефон</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->phone ?: 'Не указан' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Должность</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->position ?: 'Не указана' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Дата регистрации</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </div>
            </x-base.card>

            <!-- Organizations -->
            <x-base.card title="Организации">
                <div class="space-y-4">
                    @forelse(auth()->user()->organizations as $org)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $org->name }}</h4>
                            <div class="mt-2 space-y-1">
                                @if($org->pivot->department)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Отдел:</span> {{ $org->pivot->department->name }}
                                    </p>
                                @endif
                                @if($org->pivot->role)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Роль:</span> {{ $org->pivot->role->name }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Дата присоединения:</span> {{ $org->pivot->joined_at->format('d.m.Y') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Вы не состоите ни в одной организации</p>
                    @endforelse
                </div>
            </x-base.card>
        </div>

        <!-- Statistics -->
        <x-base.card title="Статистика">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Участие в чатах</dt>
                    <dd class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ auth()->user()->chats->count() }}</dd>
                </div>
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Отправлено сообщений</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">{{ auth()->user()->messages->count() }}</dd>
                </div>
                <div class="text-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Создано чатов</dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-600 dark:text-blue-400">{{ auth()->user()->createdChats->count() }}</dd>
                </div>
            </div>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
