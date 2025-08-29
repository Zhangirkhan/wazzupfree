<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Тест уведомлений</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header с уведомлениями -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Логотип -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Тест уведомлений</h1>
                    </div>

                    <!-- Правая часть header -->
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Уведомления -->
                        <x-notifications.notification-bell />

                        <!-- Переключатель темы -->
                        <x-settings.theme-toggle />

                        <!-- Профиль -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">Т</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Тестовый пользователь</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Основной контент -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Демонстрация системы уведомлений
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">
                                Как использовать уведомления:
                            </h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• Нажмите на иконку колокольчика в правом верхнем углу</li>
                                <li>• Просмотрите последние 5 сообщений от клиентов</li>
                                <li>• Нажмите на уведомление, чтобы открыть чат</li>
                                <li>• Используйте кнопку "Отметить все как прочитанные"</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-green-900 dark:text-green-100 mb-2">
                                Функции уведомлений:
                            </h3>
                            <ul class="text-sm text-green-800 dark:text-green-200 space-y-1">
                                <li>• Автоматическое обновление каждые 30 секунд</li>
                                <li>• Индикатор количества непрочитанных сообщений</li>
                                <li>• Превью сообщений с временными метками</li>
                                <li>• Прямой переход к чату при клике</li>
                                <li>• Поддержка темной темы</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-100 mb-2">
                                Тестовые данные:
                            </h3>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                В системе создано несколько тестовых чатов с сообщениями от клиентов. 
                                Попробуйте открыть уведомления, чтобы увидеть их в действии.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
