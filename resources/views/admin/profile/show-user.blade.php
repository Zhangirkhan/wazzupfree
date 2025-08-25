@extends('layouts.admin')

@section('title', 'Профиль пользователя - ' . $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Профиль пользователя</h1>
                    <p class="text-gray-600 mt-2">{{ $user->name }} ({{ $user->email }})</p>
                </div>
                <div class="flex space-x-2">
                    @if(Auth::user()->role === 'admin')
                        <x-base.button href="{{ route('admin.profile.edit-user', $user) }}" variant="primary" size="sm">
                            Редактировать
                        </x-base.button>
                    @endif
                    <x-base.button href="{{ route('admin.users.index') }}" variant="outline" size="sm">
                        Назад к списку
                    </x-base.button>
                </div>
            </div>
        </div>

        <!-- Основная информация -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start space-x-6">
                <!-- Аватар -->
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Информация о пользователе -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $user->name }}</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($user->role === 'admin') bg-red-100 text-red-800
                            @elseif($user->role === 'manager') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            @switch($user->role)
                                @case('admin')
                                    👑 Администратор
                                    @break
                                @case('manager')
                                    👨‍💼 Менеджер
                                    @break
                                @case('employee')
                                    👷 Сотрудник
                                    @break
                                @default
                                    {{ $user->role }}
                            @endswitch
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Телефон</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?: 'Не указан' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Должность</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->position ?: 'Не указана' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Отдел</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->department->name ?? 'Не назначен' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Дата регистрации</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Последнее обновление</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Всего чатов</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_chats'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Активные чаты</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_chats'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Сообщений</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_messages'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Завершенные</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_chats'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Быстрые действия</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-base.button href="{{ route('user.chat.index') }}" variant="primary" class="w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Мессенджер
                </x-base.button>

                <x-base.button href="{{ route('admin.clients.index') }}" variant="outline" class="w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"></path>
                    </svg>
                    Клиенты
                </x-base.button>

                <x-base.button href="{{ route('admin.dashboard') }}" variant="outline" class="w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    Дашборд
                </x-base.button>
            </div>
        </div>
    </div>
</div>
@endsection
