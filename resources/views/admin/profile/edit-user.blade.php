@extends('layouts.admin')

@section('title', 'Редактирование профиля - ' . $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Редактирование профиля</h1>
                    <p class="text-gray-600 mt-2">{{ $user->name }} ({{ $user->email }})</p>
                </div>
                <x-base.button href="{{ route('admin.profile.show-user', $user) }}" variant="outline" size="sm">
                    Назад к профилю
                </x-base.button>
            </div>
        </div>

        <!-- Форма -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.profile.update-user', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Аватар -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Аватар</label>
                    <div class="flex items-center space-x-4">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-lg font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="avatar" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF до 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Имя -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Имя *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Телефон -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Должность -->
                <div class="mb-4">
                    <label for="position" class="block text-sm font-medium text-gray-700">Должность</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Отдел -->
                <div class="mb-4">
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Отдел</label>
                    <select name="department_id" id="department_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                    {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Роль -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700">Роль *</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>
                            👷 Сотрудник
                        </option>
                        <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>
                            👨‍💼 Менеджер
                        </option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                            👑 Администратор
                        </option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.profile.show-user', $user) }}" variant="outline">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Сохранить изменения
                    </x-base.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
