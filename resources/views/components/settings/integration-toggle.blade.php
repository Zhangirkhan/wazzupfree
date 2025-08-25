@props(['enabled' => false, 'integrationName' => ''])

<div x-data="integrationToggle(@js($enabled), '{{ $integrationName }}')">

    <!-- Toggle Switch -->
    <button 
        @click="toggleIntegration()"
        type="button" 
        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        :class="enabled ? 'bg-green-600' : 'bg-gray-200'"
        :disabled="loading"
    >
        <span class="sr-only">Переключить интеграцию</span>
        <span 
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
            :class="enabled ? 'translate-x-6' : 'translate-x-1'"
        ></span>
    </button>

    <!-- Loading indicator -->
    <div x-show="loading" class="ml-2">
        <svg class="animate-spin h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Password Modal -->
    <div 
        x-show="showPasswordModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.away="showPasswordModal = false"
    >
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                
                <!-- Modal header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Подтверждение отключения
                    </h3>
                    <button 
                        @click="showPasswordModal = false"
                        type="button" 
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    >
                        <span class="sr-only">Закрыть</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                Отключение интеграции Wazzup24
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Для отключения интеграции введите ваш пароль
                            </p>
                        </div>
                    </div>

                    <!-- Password input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Пароль
                        </label>
                        <input 
                            type="password" 
                            id="password"
                            x-model="password"
                            @keyup.enter="disableIntegration()"
                            class="block w-full rounded-md border-2 border-gray-300 dark:border-gray-600 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 sm:text-sm"
                            placeholder="Введите ваш пароль"
                            :disabled="loading"
                        />
                        <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        @click="showPasswordModal = false"
                        type="button" 
                        class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        :disabled="loading"
                    >
                        Отмена
                    </button>
                    <button 
                        @click="disableIntegration()"
                        type="button" 
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading || !password.trim()"
                    >
                        <span x-show="!loading">Отключить</span>
                        <span x-show="loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Отключение...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
