<script>
    // Функция показа модального окна истории
    function showHistoryModal() {
        console.log('showHistoryModal вызвана, currentChatId:', currentChatId);
        if (!currentChatId) {
            alert('Чат не выбран');
            return;
        }
        
        const url = `{{ url('/user/chat/history') }}/${currentChatId}`;
        console.log('Запрашиваем историю по URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Ответ сервера:', response.status, response.statusText);
                return response.json();
            })
            .then(data => {
                console.log('Данные истории:', data);
                if (data.success) {
                    displayHistory(data.history);
                } else {
                    alert('Ошибка при получении истории: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Ошибка получения истории:', error);
                alert('Ошибка при получении истории');
            });
    }

    // Функция отображения истории
    function displayHistory(history) {
        console.log('displayHistory вызвана с данными:', history);
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] modal-overlay';
        modal.id = 'historyModal';
        
        let historyHtml = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden modal-content">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">История чата</h3>
                    <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[60vh]">
        `;
        
        if (history.length === 0) {
            historyHtml += '<p class="text-gray-500 dark:text-gray-400 text-center">История пуста</p>';
        } else {
            historyHtml += '<div class="space-y-4">';
            history.forEach(item => {
                const actionIcon = getActionIcon(item.action);
                const actionColor = getActionColor(item.action);
                
                historyHtml += `
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 ${actionColor} rounded-full flex items-center justify-center">
                                ${actionIcon}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${item.description}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${item.created_at}</p>
                            </div>
                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                ${item.user_name ? `Пользователь: ${item.user_name}` : ''}
                                ${item.department_name ? ` | Отдел: ${item.department_name}` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            historyHtml += '</div>';
        }
        
        historyHtml += `
                </div>
            </div>
        `;
        
        modal.innerHTML = historyHtml;
        document.body.appendChild(modal);
        console.log('Модальное окно истории добавлено в DOM');
    }

    // Функция закрытия модального окна истории
    function closeHistoryModal() {
        const modal = document.getElementById('historyModal');
        if (modal) {
            modal.remove();
        }
    }

    // Функция получения иконки для действия
    function getActionIcon(action) {
        switch(action) {
            case 'department_selected':
                return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>';
            case 'assigned_to':
                return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>';
            case 'completed':
                return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            case 'reset':
                return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
            default:
                return '<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }
    }

    // Функция получения цвета для действия
    function getActionColor(action) {
        switch(action) {
            case 'department_selected':
                return 'bg-blue-500';
            case 'assigned_to':
                return 'bg-green-500';
            case 'completed':
                return 'bg-red-500';
            case 'reset':
                return 'bg-yellow-500';
            default:
                return 'bg-gray-500';
        }
    }

    // Функция показа модального окна переключения отдела
    function showTransferModal() {
        const modal = document.createElement('div');
        modal.id = 'transferModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 max-w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Переключить отдел</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Выберите отдел:</label>
                    <select id="transferDepartment" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Выберите отдел</option>
                        <option value="1">Бухгалтерия</option>
                        <option value="2">IT отдел</option>
                        <option value="3">HR отдел</option>
                        <option value="4">Вопросы по товарам</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeTransferModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Отмена</button>
                    <button onclick="transferToDepartment()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Переключить</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function closeTransferModal() {
        const modal = document.getElementById('transferModal');
        if (modal) {
            modal.remove();
        }
    }

    // Функция переключения отдела
    function transferToDepartment() {
        const departmentId = document.getElementById('transferDepartment').value;
        if (!departmentId) {
            alert('Пожалуйста, выберите отдел');
            return;
        }

        fetch(`{{ url('/user/chat/transfer') }}/${currentChatId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                department_id: departmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Чат успешно переключен в другой отдел');
                closeTransferModal();
                // Перезагружаем страницу для обновления списка чатов
                window.location.reload();
            } else {
                alert('Ошибка при переключении отдела: ' + (data.error || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Ошибка переключения отдела:', error);
            alert('Ошибка при переключении отдела');
        });
    }
</script>
