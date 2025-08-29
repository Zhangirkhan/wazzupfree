<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Тест переключения отделов</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Тест переключения отделов</h1>
                    </div>
                </div>
            </div>
        </header>

        <!-- Основной контент -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Демонстрация переключения чатов между отделами
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Информация о функционале -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">
                                Как работает переключение отделов:
                            </h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• Нажмите на иконку переключения (↔️) в заголовке чата</li>
                                <li>• Выберите нужный отдел из списка</li>
                                <li>• Чат будет переведен в выбранный отдел</li>
                                <li>• Назначение менеджера сбрасывается</li>
                                <li>• Действие записывается в историю чата</li>
                            </ul>
                        </div>

                        <!-- Доступные отделы -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-green-900 dark:text-green-100 mb-2">
                                Доступные отделы для переключения:
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="flex items-center space-x-3 p-2 bg-white dark:bg-gray-700 rounded">
                                    <div class="h-6 w-6 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">Б</span>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">Бухгалтерия</span>
                                </div>
                                <div class="flex items-center space-x-3 p-2 bg-white dark:bg-gray-700 rounded">
                                    <div class="h-6 w-6 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">IT</span>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">IT отдел</span>
                                </div>
                                <div class="flex items-center space-x-3 p-2 bg-white dark:bg-gray-700 rounded">
                                    <div class="h-6 w-6 bg-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">HR</span>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">HR отдел</span>
                                </div>
                                <div class="flex items-center space-x-3 p-2 bg-white dark:bg-gray-700 rounded">
                                    <div class="h-6 w-6 bg-orange-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">Т</span>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">Вопросы по товарам</span>
                                </div>
                            </div>
                        </div>

                        <!-- Права доступа -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-100 mb-2">
                                Права доступа:
                            </h3>
                            <ul class="text-sm text-yellow-800 dark:text-yellow-200 space-y-1">
                                <li>• <strong>Админы:</strong> могут переключать любые чаты</li>
                                <li>• <strong>Руководители отделов:</strong> могут переключать чаты в своем отделе</li>
                                <li>• <strong>Обычные сотрудники:</strong> могут переключать назначенные им чаты</li>
                                <li>• <strong>Все пользователи:</strong> могут сбрасывать назначение отдела</li>
                            </ul>
                        </div>

                        <!-- Тестовые данные -->
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Тестовые данные:
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                В системе создано несколько тестовых чатов. Перейдите в раздел чатов, 
                                выберите любой чат и попробуйте переключить его в другой отдел, 
                                используя кнопку переключения в заголовке чата.
                            </p>
                        </div>

                        <!-- Ссылка на чаты -->
                        <div class="text-center">
                            <a href="{{ route('user.chat.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Перейти к чатам
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
