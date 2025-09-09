<!-- Модальное окно подтверждения завершения диалога -->
<div id="endChatConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
    <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Завершить диалог</h3>
                <button id="closeEndChatModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Подтверждение завершения</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Это действие нельзя отменить</p>
                    </div>
                </div>
                
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h5 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Что произойдет:</h5>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Клиент получит сообщение о завершении</li>
                                    <li>Диалог будет помечен как завершенный</li>
                                    <li>Клиент сможет продолжить общение, написав 1 или 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button id="cancelEndChatBtn" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Отмена
                </button>
                <button id="confirmEndChatBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Завершить диалог</span>
                </button>
            </div>
        </div>
    </div>
</div>
