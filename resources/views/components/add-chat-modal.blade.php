<!-- Модальное окно для создания чата -->
<div id="addChatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
    <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Начать новый чат</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="createChatForm">
                <div class="mb-4">
                    <label for="clientSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Выберите клиента
                    </label>
                    <select id="clientSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">Начните вводить имя или телефон...</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="initialMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Начальное сообщение (необязательно)
                    </label>
                    <textarea id="initialMessage" rows="3" placeholder="Введите сообщение..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Отмена
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Создать чат
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
