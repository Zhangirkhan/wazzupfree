<x-navigation.app>
    @section('title', 'Просмотр пользователя')
    @section('content')
    <x-slot name="title">Просмотр пользователя</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Просмотр пользователя</h1>
            <div class="flex space-x-3">
                <x-base.button href="{{ route('admin.users.edit', $user) }}" variant="primary">
                    Редактировать
                </x-base.button>
                <x-base.button href="{{ route('admin.users.index') }}" variant="secondary">
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
                                <span class="text-white text-xl font-semibold">{{ $user->name[0] }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                                <p class="text-gray-600">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Телефон</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?: 'Не указан' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Должность</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->position ?: 'Не указана' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Дата регистрации</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Последнее обновление</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </x-base.card>
            </div>

            <!-- Организационная информация -->
            <div>
                <x-base.card>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Организационная информация</h3>
                    
                    @if($user->organizations->count() > 0)
                        @foreach($user->organizations as $org)
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Организация</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $org->name }}</p>
                                </div>
                                
                                @if($org->pivot->department_id)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Отдел</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $org->departments->where('id', $org->pivot->department_id)->first()?->name ?: 'Не найден' }}</p>
                                    </div>
                                @endif
                                
                                @if($org->pivot->role_id)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Роль</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $org->roles->where('id', $org->pivot->role_id)->first()?->name ?: 'Не найдена' }}</p>
                                    </div>
                                @endif
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Статус</label>
                                    <x-base.badge variant="{{ $org->pivot->is_active ? 'success' : 'danger' }}">
                                        {{ $org->pivot->is_active ? 'Активен' : 'Неактивен' }}
                                    </x-base.badge>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">Пользователь не привязан к организациям</p>
                    @endif
                </x-base.card>
            </div>
        </div>

        <!-- Статистика -->
        <x-base.card>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Статистика активности</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $user->chats->count() }}</div>
                    <div class="text-sm text-gray-500">Чатов</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $user->messages->count() }}</div>
                    <div class="text-sm text-gray-500">Сообщений</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $user->messages->where('type', 'system')->count() }}</div>
                    <div class="text-sm text-gray-500">Системных</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $user->organizations->count() }}</div>
                    <div class="text-sm text-gray-500">Организаций</div>
                </div>
            </div>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
