<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($chat->title); ?> - <?php echo e(config('app.name')); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0f1419 0%, #1a1f2e 100%);
            color: #e9edef;
            height: 100vh;
            overflow: hidden;
            font-weight: 400;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .app {
            display: flex;
            height: 100vh;
        }

        /* Левая панель */
        .sidebar {
            width: 400px;
            background: rgba(17, 27, 33, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(42, 57, 66, 0.3);
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            background: linear-gradient(135deg, #202c33 0%, #2a3942 100%);
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-bottom: 1px solid rgba(42, 57, 66, 0.3);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-title {
            color: #e9edef;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }



        .search-container {
            padding: 16px 20px;
            background: rgba(17, 27, 33, 0.8);
            position: relative;
        }

        .search-input {
            width: 100%;
            height: 40px;
            background: rgba(32, 44, 51, 0.8);
            border: 1px solid rgba(42, 57, 66, 0.3);
            border-radius: 12px;
            padding: 0 16px 0 48px;
            color: #e9edef;
            font-size: 14px;
            font-weight: 400;
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-input:focus {
            border-color: #00a884;
            background: rgba(32, 44, 51, 0.95);
            box-shadow: 0 0 0 3px rgba(0, 168, 132, 0.1);
        }

        .search-input::placeholder {
            color: #8696a0;
            font-weight: 400;
        }

        .search-icon {
            position: absolute;
            left: 24px;
            top: 22px;
            width: 16px;
            height: 16px;
            opacity: 0.6;
        }

        .chats-container {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            display: flex;
            padding: 16px 20px;
            cursor: pointer;
            border-bottom: 1px solid rgba(42, 57, 66, 0.2);
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .chat-item:hover {
            background: rgba(24, 34, 41, 0.8);
            transform: translateX(2px);
        }

        .chat-item.active {
            background: linear-gradient(135deg, rgba(42, 57, 66, 0.8) 0%, rgba(0, 168, 132, 0.1) 100%);
            border-left: 3px solid #00a884;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            margin-right: 16px;
            background: linear-gradient(135deg, #00a884 0%, #00d4aa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0, 168, 132, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-info {
            flex: 1;
            min-width: 0;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
        }

        .chat-name {
            font-size: 16px;
            color: #e9edef;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.3px;
        }

        .chat-time {
            font-size: 12px;
            color: #8696a0;
            white-space: nowrap;
            font-weight: 500;
        }

        .chat-preview {
            font-size: 14px;
            color: #8696a0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 400;
            line-height: 1.4;
        }

        .status-icon {
            width: 16px;
            height: 16px;
            opacity: 0.6;
        }

        .unread-badge {
            position: absolute;
            right: 12px;
            bottom: 12px;
            background: #25d366;
            color: #111b21;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        /* Правая панель - чат */
        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #0b141a 0%, #1a1f2e 100%);
            position: relative;
            backdrop-filter: blur(10px);
        }

        .chat-header-panel {
            height: 65px;
            background: linear-gradient(135deg, #202c33 0%, #2a3942 100%);
            border-bottom: 1px solid rgba(42, 57, 66, 0.3);
            display: flex;
            align-items: center;
            padding: 12px 20px;
            gap: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-header-info {
            flex: 1;
        }

        .chat-header-name {
            font-size: 18px;
            color: #e9edef;
            margin-bottom: 3px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .chat-header-status {
            font-size: 13px;
            color: #8696a0;
            font-weight: 500;
        }

        .chat-actions {
            display: flex;
            gap: 12px;
        }

        .chat-action {
            width: 24px;
            height: 24px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .chat-action:hover {
            opacity: 1;
        }

        .messages-container {
            flex: 1;
            background: linear-gradient(135deg, rgba(11, 20, 26, 0.8) 0%, rgba(26, 31, 46, 0.8) 100%);
            padding: 24px;
            overflow-y: auto;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: rgba(42, 57, 66, 0.1);
            border-radius: 3px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: rgba(0, 168, 132, 0.3);
            border-radius: 3px;
        }

        .messages-container::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 168, 132, 0.5);
        }

        .message {
            display: flex;
            margin-bottom: 16px;
            max-width: 70%;
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.sent {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .message.received {
            margin-right: auto;
        }

        .message-bubble {
            background: linear-gradient(135deg, #005c4b 0%, #007a5c 100%);
            border-radius: 18px;
            padding: 12px 16px;
            position: relative;
            max-width: 100%;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0, 92, 75, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .message.received .message-bubble {
            background: linear-gradient(135deg, #202c33 0%, #2a3942 100%);
            box-shadow: 0 2px 8px rgba(32, 44, 51, 0.3);
        }

        .message-sender {
            font-size: 12px;
            color: #00a884;
            margin-bottom: 4px;
            font-weight: 600;
            letter-spacing: -0.2px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .message-text {
            font-size: 14px;
            color: #e9edef;
            line-height: 1.4;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-weight: 400;
            letter-spacing: -0.1px;
        }

        .message-time {
            font-size: 11px;
            color: rgba(241, 241, 242, 0.6);
            margin-top: 6px;
            text-align: right;
            font-weight: 500;
            letter-spacing: -0.1px;
        }

        .temp-message {
            opacity: 0.8;
        }

        .sending-indicator {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .message-input-container {
            background: linear-gradient(135deg, #202c33 0%, #2a3942 100%);
            padding: 16px 20px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            border-top: 1px solid rgba(42, 57, 66, 0.3);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .message-input {
            flex: 1;
            background: rgba(42, 57, 66, 0.8);
            border: 1px solid rgba(42, 57, 66, 0.3);
            border-radius: 24px;
            padding: 12px 16px;
            color: #e9edef;
            font-size: 15px;
            font-weight: 400;
            outline: none;
            resize: none;
            max-height: 120px;
            min-height: 24px;
            line-height: 1.4;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .message-input:focus {
            border-color: #00a884;
            background: rgba(42, 57, 66, 0.95);
            box-shadow: 0 0 0 3px rgba(0, 168, 132, 0.1);
        }

        .message-input::placeholder {
            color: #8696a0;
            font-weight: 400;
        }

        .send-button {
            width: 32px;
            height: 32px;
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.3s ease;
            background: rgba(0, 168, 132, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 168, 132, 0.2);
        }

        .send-button:hover {
            opacity: 1;
            fill: #00a884;
            transform: scale(1.05);
            background: rgba(0, 168, 132, 0.2);
            box-shadow: 0 4px 12px rgba(0, 168, 132, 0.3);
        }

        .send-button:active {
            transform: scale(0.95);
        }

        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #8696a0;
            font-size: 16px;
        }

        .whatsapp-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: absolute;
                z-index: 10;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .chat-panel {
                width: 100%;
            }
        }

        .no-chats {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #8696a0;
            font-size: 16px;
            text-align: center;
            padding: 20px;
        }

        .no-chats-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #8696a0;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #2a3942;
            border-top: 2px solid #00a884;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- Левая панель с чатами -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('user.chat.index')); ?>" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="sidebar-title">Корпоративный чат</h1>
                </div>

            </div>

            <div class="search-container">
                <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" class="search-input" placeholder="Поиск чатов" id="searchInput">
            </div>

            <div class="chats-container">
                <div class="loading">
                    <div class="spinner"></div>
                    Загрузка чатов...
                </div>
            </div>
        </div>

        <!-- Правая панель - активный чат -->
        <div class="chat-panel">
            <div class="chat-header-panel">
                <a href="<?php echo e(route('user.chat.index')); ?>" class="text-gray-400 hover:text-white transition-colors mr-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="avatar" style="background: <?php echo e($chat->messenger_status === 'active' ? '#00a884' : ($chat->messenger_status === 'completed' ? '#ef4444' : '#f59e0b')); ?>;">
                    <?php if($chat->department): ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    <?php else: ?>
                        <?php echo e(strtoupper(substr($chat->messenger_phone ?? 'К', 0, 1))); ?>

                    <?php endif; ?>
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name">
                        <?php echo e($chat->messenger_phone ?? $chat->title); ?>

                        <?php if($chat->department): ?>
                            <span style="font-size: 12px; color: #8696a0;">(<?php echo e($chat->department->name); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div class="chat-header-status">
                        <?php switch($chat->messenger_status):
                            case ('menu'): ?>
                                В главном меню
                                <?php break; ?>
                            <?php case ('department_selected'): ?>
                                Отдел выбран
                                <?php break; ?>
                            <?php case ('active'): ?>
                                <?php if($chat->assigned_to): ?>
                                    Назначен: <?php echo e($chat->assignedTo ? $chat->assignedTo->name : 'Неизвестно'); ?>

                                <?php else: ?>
                                    Ожидает назначения
                                <?php endif; ?>
                                <?php break; ?>
                            <?php case ('completed'): ?>
                                Завершен
                                <?php break; ?>
                            <?php default: ?>
                                <?php echo e($chat->messenger_status); ?>

                        <?php endswitch; ?>
                    </div>
                </div>
                <div class="chat-actions">
                    <?php if($chat->messenger_status === 'active' && !$chat->assigned_to && auth()->user()->role !== 'employee'): ?>
                        <button onclick="acceptChat()" class="chat-action" title="Принять чат">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                    <?php if($chat->messenger_status === 'active' && $chat->assigned_to === auth()->id()): ?>
                        <button onclick="completeChat()" class="chat-action" title="Завершить чат">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                    <svg class="chat-action" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </div>
            </div>

            <div class="messages-container" id="messagesContainer">
                <div class="loading">
                    <div class="spinner"></div>
                    Загрузка сообщений...
                </div>
            </div>

            <div class="message-input-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#8696a0" style="cursor: pointer;">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <textarea class="message-input" placeholder="Введите сообщение" rows="1" onkeydown="handleKeyDown(event)" oninput="autoResize(this)" onkeyup="updateSendButton()"></textarea>
                <svg class="send-button" viewBox="0 0 24 24" fill="#8696a0" onclick="sendMessage()" style="cursor: pointer; transition: fill 0.2s;">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <script>
        const chatId = <?php echo e($chat->id); ?>;
        const currentUserId = <?php echo e(auth()->id()); ?>;
        let messages = [];

        // Загрузка чатов в левую панель
        async function loadChats() {
            try {
                const response = await fetch('<?php echo e(route("user.chat.index")); ?>');
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const chatsContainer = document.querySelector('.chats-container');
                const newChatsContainer = doc.querySelector('.chats-container');
                
                if (newChatsContainer) {
                    chatsContainer.innerHTML = newChatsContainer.innerHTML;
                }
            } catch (error) {
                console.error('Ошибка загрузки чатов:', error);
            }
        }

        // Загрузка сообщений
        async function loadMessages() {
            try {
                const response = await fetch(`/user/chat/${chatId}/messages`);
                const data = await response.json();
                messages = data;
                renderMessages();
                
                // Инициализируем состояние кнопки отправки
                updateSendButton();
            } catch (error) {
                console.error('Ошибка загрузки сообщений:', error);
            }
        }

        // Показать уведомление об ошибке
        function showError(message) {
            const notification = document.createElement('div');
            notification.className = 'error-notification';
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ef4444;
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                z-index: 1000;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            `;
            
            document.body.appendChild(notification);
            
            // Удаляем уведомление через 3 секунды
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        // Форматирование текста сообщения с переносами строк
        function formatMessageText(text) {
            if (!text) return '';
            
            // Заменяем переносы строк на <br>
            let formatted = text.replace(/\n/g, '<br>');
            
            // Заменяем двойные пробелы на неразрывные пробелы
            formatted = formatted.replace(/  /g, '&nbsp;&nbsp;');
            
            // Экранируем HTML-теги для безопасности
            formatted = formatted
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            
            // Восстанавливаем <br> теги
            formatted = formatted.replace(/&lt;br&gt;/g, '<br>');
            
            return formatted;
        }

        // Отображение сообщений
        function renderMessages() {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '';

            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                
                // Определяем направление сообщения
                const isFromClient = message.metadata && message.metadata.direction === 'incoming';
                const isFromSystem = message.type === 'system' || (message.metadata && message.metadata.is_bot_message);
                const isFromCurrentUser = message.user_id === currentUserId;
                
                // Сообщения от клиента слева, от системы/текущего пользователя справа
                const isSent = isFromSystem || isFromCurrentUser;
                messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
                
                const time = new Date(message.created_at).toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });

                // Определяем имя отправителя
                let senderName = '';
                if (isFromClient) {
                    // Для клиента показываем имя из метаданных или номер телефона
                    if (message.metadata && message.metadata.client_name) {
                        senderName = message.metadata.client_name;
                    } else if (message.user && message.user.name) {
                        senderName = message.user.name;
                    } else {
                        // Если нет имени, показываем номер телефона
                        senderName = '<?php echo e($chat->messenger_phone ?? "Клиент"); ?>';
                    }
                } else if (isFromSystem) {
                    senderName = 'Система';
                } else if (isFromCurrentUser) {
                    // Для текущего пользователя показываем его имя
                    senderName = '<?php echo e(auth()->user()->name); ?>';
                } else if (message.user && message.user.name) {
                    // Для других пользователей показываем их имя
                    senderName = message.user.name;
                }

                // Форматируем текст с переносами строк
                const formattedContent = formatMessageText(message.content);
                
                // Проверяем, является ли это временным сообщением
                const isTemp = message.metadata && message.metadata.is_temp;
                const bubbleClass = isTemp ? 'message-bubble temp-message' : 'message-bubble';
                
                messageDiv.innerHTML = `
                    <div class="${bubbleClass}">
                        ${senderName ? `<div class="message-sender">${senderName}</div>` : ''}
                        <div class="message-text">${formattedContent}</div>
                        <div class="message-time">
                            ${isTemp ? '<span class="sending-indicator">⏳</span>' : time}
                        </div>
                    </div>
                `;
                
                container.appendChild(messageDiv);
            });

            // Прокручиваем вниз
            container.scrollTop = container.scrollHeight;
        }

        // Автоматическое изменение размера textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
            updateSendButton();
        }

        // Обновление состояния кнопки отправки
        function updateSendButton() {
            const input = document.querySelector('.message-input');
            const sendButton = document.querySelector('.send-button');
            const text = input.value.trim();
            
            if (text) {
                sendButton.style.opacity = '1';
                sendButton.style.fill = '#00a884';
            } else {
                sendButton.style.opacity = '0.7';
                sendButton.style.fill = '#8696a0';
            }
        }

        // Обработка нажатий клавиш
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        // Отправка сообщения
        async function sendMessage() {
            const input = document.querySelector('.message-input');
            const text = input.value.trim();
            
            if (!text) return;

            // Создаем временное сообщение для мгновенного отображения
            const tempMessage = {
                id: 'temp_' + Date.now(),
                content: text,
                user_id: currentUserId,
                type: 'text',
                created_at: new Date().toISOString(),
                metadata: {
                    direction: 'outgoing',
                    is_manager_message: true,
                    manager_name: '<?php echo e(auth()->user()->name); ?>',
                    is_temp: true // Флаг для временного сообщения
                }
            };

            // Добавляем временное сообщение в массив и отображаем
            messages.push(tempMessage);
            renderMessages();
            
            // Очищаем поле ввода сразу
            input.value = '';
            input.style.height = 'auto';
            updateSendButton();

            // Отправляем сообщение на сервер
            try {
                const response = await fetch(`/user/chat/${chatId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ content: text })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Заменяем временное сообщение на реальное
                    const tempIndex = messages.findIndex(msg => msg.id === tempMessage.id);
                    if (tempIndex !== -1) {
                        messages[tempIndex] = data.message;
                        renderMessages();
                    }
                } else {
                    // Если ошибка, показываем уведомление
                    console.error('Ошибка отправки сообщения:', data.message);
                    showError('Ошибка отправки сообщения: ' + (data.message || 'Неизвестная ошибка'));
                    
                    // Удаляем временное сообщение при ошибке
                    const tempIndex = messages.findIndex(msg => msg.id === tempMessage.id);
                    if (tempIndex !== -1) {
                        messages.splice(tempIndex, 1);
                        renderMessages();
                    }
                }
            } catch (error) {
                console.error('Ошибка отправки сообщения:', error);
                showError('Ошибка отправки сообщения: ' + error.message);
                
                // Удаляем временное сообщение при ошибке
                const tempIndex = messages.findIndex(msg => msg.id === tempMessage.id);
                if (tempIndex !== -1) {
                    messages.splice(tempIndex, 1);
                    renderMessages();
                }
            }
        }

        // Принять чат
        async function acceptChat() {
            try {
                const response = await fetch(`/user/chat/${chatId}/accept`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    alert('Чат принят!');
                    location.reload();
                } else {
                    alert('Ошибка при принятии чата');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при принятии чата');
            }
        }

        // Завершить чат
        async function completeChat() {
            const reason = prompt('Укажите причину завершения чата (необязательно):');
            
            try {
                const response = await fetch(`/user/chat/${chatId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason: reason })
                });
                
                if (response.ok) {
                    alert('Чат завершен!');
                    location.reload();
                } else {
                    alert('Ошибка при завершении чата');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при завершении чата');
            }
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            loadChats();
            loadMessages();
            
            // Обновляем сообщения каждые 5 секунд
            setInterval(loadMessages, 5000);
        });

        // Поиск чатов
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const chatItems = document.querySelectorAll('.chat-item');
            
            chatItems.forEach(item => {
                const chatName = item.querySelector('.chat-name').textContent.toLowerCase();
                const chatPreview = item.querySelector('.chat-preview').textContent.toLowerCase();
                
                if (chatName.includes(searchTerm) || chatPreview.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/user/chat/show.blade.php ENDPATH**/ ?>