<!-- Модальное окно для загрузки изображения с подписью -->
<div id="imageUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
    <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Загрузить изображение</h3>
                <button onclick="closeImageUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Превью изображения -->
            <div class="mb-4">
                <img id="imagePreview" src="" alt="Превью" class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
            </div>
            
            <!-- Поле для подписи -->
            <div class="mb-4">
                <label for="imageCaption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Подпись к изображению (необязательно)
                </label>
                <textarea id="imageCaption" 
                          rows="3"
                          placeholder="Введите подпись к изображению..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end space-x-3">
                <button onclick="closeImageUploadModal()" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Отмена
                </button>
                <button id="uploadImageBtn"
                        onclick="uploadImageWithCaption()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Загрузить</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для загрузки видео с подписью -->
<div id="videoUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999] flex items-center justify-center modal-overlay">
    <div class="relative mx-auto p-6 border w-96 max-w-md shadow-xl rounded-lg bg-white dark:bg-gray-800 modal-content">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Загрузить видео</h3>
                <button onclick="closeVideoUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Превью видео -->
            <div class="mb-4">
                <video id="videoPreview" controls class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                    Ваш браузер не поддерживает видео.
                </video>
            </div>
            
            <!-- Поле для подписи -->
            <div class="mb-4">
                <label for="videoCaption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Подпись к видео (необязательно)
                </label>
                <textarea id="videoCaption" 
                          rows="3"
                          placeholder="Введите подпись к видео..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end space-x-3">
                <button onclick="closeVideoUploadModal()" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Отмена
                </button>
                <button id="uploadVideoBtn"
                        onclick="uploadVideoWithCaption()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Загрузить</span>
                </button>
            </div>
        </div>
    </div>
</div>
