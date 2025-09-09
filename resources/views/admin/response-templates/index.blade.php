@extends('layouts.admin')

@section('title', 'Шаблоны ответов')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Шаблоны ответов</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Управление шаблонами ответов для быстрого ответа клиентам
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="{{ route('admin.response-templates.create') }}" 
               class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                Создать шаблон
            </a>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                
                <!-- Фильтр по категориям -->
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filterByCategory('all')" 
                                class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                data-category="all">
                            Все категории
                        </button>
                        @foreach($categories as $key => $name)
                            <button onclick="filterByCategory('{{ $key }}')" 
                                    class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    data-category="{{ $key }}">
                                {{ $name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Шаблоны по категориям -->
                @foreach($categories as $categoryKey => $categoryName)
                    <div class="category-section mb-8" data-category="{{ $categoryKey }}">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            {{ $categoryName }}
                        </h3>
                        
                        @if(isset($templates[$categoryKey]) && $templates[$categoryKey]->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($templates[$categoryKey] as $template)
                                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $template->name }}
                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                @if($template->is_active)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Активен
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        Неактивен
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-3">
                                            {{ Str::limit($template->content, 100) }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                            <span>Использований: {{ $template->usage_count }}</span>
                                            <span>Создан: {{ $template->created_at->format('d.m.Y') }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.response-templates.edit', $template) }}" 
                                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                                    Редактировать
                                                </a>
                                                <button onclick="copyTemplate('{{ $template->id }}', '{{ addslashes($template->content) }}')" 
                                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
                                                    Копировать
                                                </button>
                                            </div>
                                            
                                            <form action="{{ route('admin.response-templates.destroy', $template) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот шаблон?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Нет шаблонов</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    В категории "{{ $categoryName }}" пока нет шаблонов ответов.
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.response-templates.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Создать первый шаблон
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function filterByCategory(category) {
    // Обновляем активную кнопку
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
        btn.classList.add('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-200');
    });
    
    event.target.classList.remove('bg-gray-100', 'text-gray-800', 'dark:bg-gray-700', 'dark:text-gray-200');
    event.target.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
    
    // Показываем/скрываем категории
    document.querySelectorAll('.category-section').forEach(section => {
        if (category === 'all' || section.dataset.category === category) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
}

function copyTemplate(templateId, content) {
    // Копируем в буфер обмена
    navigator.clipboard.writeText(content).then(function() {
        // Показываем уведомление
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notification.textContent = 'Шаблон скопирован в буфер обмена';
        document.body.appendChild(notification);
        
        // Удаляем уведомление через 3 секунды
        setTimeout(() => {
            notification.remove();
        }, 3000);
        
        // Увеличиваем счетчик использований
        fetch(`/admin/response-templates/${templateId}/increment-usage`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
    });
}
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

