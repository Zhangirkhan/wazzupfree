@extends('layouts.admin')

@section('title', 'Передача чата')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Передача чата</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Передача чата "{{ $chat->title }}" в другой отдел или менеджеру
            </p>
        </div>

        <!-- Информация о чате -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Информация о чате</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Название</p>
                    <p class="text-gray-900 dark:text-white">{{ $chat->title }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Телефон клиента</p>
                    <p class="text-gray-900 dark:text-white">{{ $chat->messenger_phone }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Текущий отдел</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $chat->department ? $chat->department->name : 'Не назначен' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Текущий менеджер</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $chat->assignedTo ? $chat->assignedTo->name : 'Не назначен' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус</p>
                    <x-base.badge :variant="$chat->messenger_status === 'active' ? 'success' : ($chat->messenger_status === 'completed' ? 'danger' : 'warning')">
                        @switch($chat->messenger_status)
                            @case('menu')
                                Главное меню
                                @break
                            @case('department_selected')
                                Отдел выбран
                                @break
                            @case('active')
                                Активный
                                @break
                            @case('completed')
                                Завершен
                                @break
                            @default
                                {{ $chat->messenger_status }}
                        @endswitch
                    </x-base.badge>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Последняя активность</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $chat->last_activity_at ? $chat->last_activity_at->format('d.m.Y H:i') : 'Неизвестно' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Формы передачи -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Передача в другой отдел -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Передача в другой отдел
                </h3>
                
                <form action="{{ route('admin.chat-transfer.to-department', $chat->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Выберите отдел
                        </label>
                        <select name="department_id" id="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Выберите отдел...</option>
                            @foreach($availableDepartments as $department)
                                <option value="{{ $department->id }}">
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="reason_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Причина передачи (необязательно)
                        </label>
                        <textarea name="reason" id="reason_department" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Укажите причину передачи чата..."></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Передать в отдел
                    </button>
                </form>
            </div>

            <!-- Передача другому менеджеру -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Передача другому менеджеру
                </h3>
                
                <form action="{{ route('admin.chat-transfer.to-user', $chat->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Выберите менеджера
                        </label>
                        <select name="user_id" id="user_id" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Выберите менеджера...</option>
                            @foreach($availableManagers as $manager)
                                <option value="{{ $manager->id }}">
                                    {{ $manager->name }} 
                                    @if($manager->department)
                                        ({{ $manager->department->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="reason_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Причина передачи (необязательно)
                        </label>
                        <textarea name="reason" id="reason_user" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Укажите причину передачи чата..."></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Передать менеджеру
                    </button>
                </form>
            </div>
        </div>

        <!-- Дополнительные действия -->
        <div class="mt-8 flex flex-wrap gap-4">
                            <a href="{{ route('user.chat.show', $chat->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Вернуться к чату
            </a>
            
            <a href="{{ route('admin.chat-transfer.history', $chat->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                История передач
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX загрузка менеджеров при выборе отдела
    const departmentSelect = document.getElementById('department_id');
    const userSelect = document.getElementById('user_id');
    
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            fetch(`{{ route('admin.chat-transfer.managers') }}?department_id=${departmentId}`)
                .then(response => response.json())
                .then(managers => {
                    userSelect.innerHTML = '<option value="">Выберите менеджера...</option>';
                    managers.forEach(manager => {
                        const option = document.createElement('option');
                        option.value = manager.id;
                        option.textContent = `${manager.name} ${manager.department ? `(${manager.department.name})` : ''}`;
                        userSelect.appendChild(option);
                    });
                });
        }
    });
});
</script>
@endpush
@endsection
