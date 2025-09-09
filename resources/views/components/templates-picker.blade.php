<!-- Пикер шаблонов ответов -->
<div id="templatesPicker" class="absolute bottom-16 left-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-4 hidden z-[9999]" style="width: 400px; max-height: 500px; overflow-y: auto;">
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Шаблоны ответов</h3>
        <div class="flex flex-wrap gap-2 mb-3">
            <button onclick="filterTemplates('all')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                    data-category="all">
                Все
            </button>
            <button onclick="filterTemplates('greeting')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    data-category="greeting">
                Приветствие
            </button>
            <button onclick="filterTemplates('help')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    data-category="help">
                Помощь
            </button>
            <button onclick="filterTemplates('support')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    data-category="support">
                Поддержка
            </button>
            <button onclick="filterTemplates('information')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    data-category="information">
                Информация
            </button>
            <button onclick="filterTemplates('general')" 
                    class="template-filter-btn px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    data-category="general">
                Общие
            </button>
        </div>
    </div>
    
    <div id="templatesList" class="space-y-2">
        <!-- Шаблоны будут загружены динамически -->
        <div class="text-center py-4">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Загрузка шаблонов...</p>
        </div>
    </div>
</div>
