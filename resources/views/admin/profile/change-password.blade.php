@extends('layouts.admin')

@section('title', 'Смена пароля')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Смена пароля</h1>
            <p class="text-gray-600 mt-2">Обновите ваш пароль для безопасности</p>
        </div>

        <!-- Форма -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Текущий пароль -->
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Текущий пароль *</label>
                    <input type="password" name="current_password" id="current_password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Новый пароль -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Новый пароль *</label>
                    <input type="password" name="password" id="password" required minlength="8"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Минимум 8 символов</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Подтверждение пароля -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Подтвердите новый пароль *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Рекомендации по безопасности -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Рекомендации по безопасности:</h3>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>• Используйте минимум 8 символов</li>
                        <li>• Включите буквы, цифры и специальные символы</li>
                        <li>• Не используйте личную информацию</li>
                        <li>• Не используйте один пароль для разных сервисов</li>
                    </ul>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.profile.show') }}" variant="outline">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Сменить пароль
                    </x-base.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
