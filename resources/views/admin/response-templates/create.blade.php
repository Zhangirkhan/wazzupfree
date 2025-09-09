@extends('layouts.admin')

@section('title', 'Создать шаблон ответа')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Создать шаблон ответа</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Создайте новый шаблон для быстрого ответа клиентам
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ route('admin.response-templates.index') }}" 
               class="block rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                Назад к списку
            </a>
        </div>
    </div>

    <div class="mt-8 max-w-2xl">
        <form action="{{ route('admin.response-templates.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Название шаблона -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Название шаблона <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Например: Приветствие клиента"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Категория -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Категория <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            required>
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Содержимое шаблона -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Содержимое шаблона <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="8"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Введите текст шаблона..."
                              required>{{ old('content') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Максимум 5000 символов. Можно использовать переменные: {client_name}, {department_name}
                    </p>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Статус активности -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Шаблон активен
                    </label>
                </div>

                <!-- Предварительный просмотр -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Предварительный просмотр</h3>
                    <div id="preview" class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 min-h-[100px]">
                        <p class="text-gray-500 dark:text-gray-400">Предварительный просмотр появится здесь...</p>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.response-templates.index') }}" 
                       class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                        Отмена
                    </a>
                    <button type="submit" 
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        Создать шаблон
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Предварительный просмотр в реальном времени
document.getElementById('content').addEventListener('input', function() {
    const content = this.value;
    const preview = document.getElementById('preview');
    
    if (content) {
        // Заменяем переменные на примеры
        let previewText = content
            .replace(/{client_name}/g, 'Иван Петров')
            .replace(/{department_name}/g, 'Отдел поддержки');
        
        preview.innerHTML = `<p class="text-gray-900 dark:text-white whitespace-pre-wrap">${previewText}</p>`;
    } else {
        preview.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Предварительный просмотр появится здесь...</p>';
    }
});

// Счетчик символов
document.getElementById('content').addEventListener('input', function() {
    const maxLength = 5000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // Обновляем или создаем счетчик
    let counter = document.getElementById('char-counter');
    if (!counter) {
        counter = document.createElement('p');
        counter.id = 'char-counter';
        counter.className = 'mt-1 text-sm text-gray-500 dark:text-gray-400';
        this.parentNode.appendChild(counter);
    }
    
    if (remaining < 0) {
        counter.textContent = `Превышен лимит на ${Math.abs(remaining)} символов`;
        counter.className = 'mt-1 text-sm text-red-600';
    } else {
        counter.textContent = `Осталось символов: ${remaining}`;
        counter.className = 'mt-1 text-sm text-gray-500 dark:text-gray-400';
    }
});
</script>
@endsection

