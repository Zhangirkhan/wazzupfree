# API Документация - Webhooks

## Базовый URL
```
https://back-chat.ap.kz/api
```

## Webhooks

Webhooks позволяют внешним сервисам отправлять данные в систему в реальном времени.

---

## Wazzup24 Webhook

### POST `/api/webhooks/wazzup24`

Webhook для получения сообщений от Wazzup24.

**Параметры запроса:**
```json
{
    "event": "message",
    "data": {
        "id": "message_123",
        "chatId": "chat_456",
        "text": "Привет! Как дела?",
        "type": "text",
        "from": "+7 777 123 45 67",
        "timestamp": 1640995200,
        "contact": {
            "name": "Иван Петров",
            "phone": "+7 777 123 45 67"
        }
    }
}
```

**Поддерживаемые события:**
- `message` - Новое сообщение
- `status` - Изменение статуса сообщения
- `contact` - Обновление контакта

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Webhook processed successfully"
}
```

**Ошибка (400):**
```json
{
    "status": "error",
    "message": "Invalid webhook data",
    "errors": {
        "event": ["Event is required"],
        "data": ["Data is required"]
    }
}
```

---

## Настройка Webhook

### URL для настройки в Wazzup24:
```
https://back-chat.ap.kz/api/webhooks/wazzup24
```

### Методы HTTP:
- `GET` - Проверка доступности webhook
- `POST` - Обработка данных от Wazzup24

### Заголовки (опционально):
```
Content-Type: application/json
User-Agent: Wazzup24/1.0
```

---

## Примеры событий

### Новое сообщение
```json
{
    "event": "message",
    "data": {
        "id": "msg_123456",
        "chatId": "chat_789",
        "text": "Здравствуйте! У меня вопрос по услугам.",
        "type": "text",
        "from": "+7 777 123 45 67",
        "timestamp": 1640995200,
        "contact": {
            "name": "Анна Смирнова",
            "phone": "+7 777 123 45 67"
        }
    }
}
```

### Сообщение с файлом
```json
{
    "event": "message",
    "data": {
        "id": "msg_123457",
        "chatId": "chat_789",
        "text": "Отправляю документ",
        "type": "file",
        "from": "+7 777 123 45 67",
        "timestamp": 1640995260,
        "file": {
            "url": "https://wazzup24.com/files/document.pdf",
            "name": "document.pdf",
            "size": 1024000,
            "mimeType": "application/pdf"
        },
        "contact": {
            "name": "Анна Смирнова",
            "phone": "+7 777 123 45 67"
        }
    }
}
```

### Изменение статуса сообщения
```json
{
    "event": "status",
    "data": {
        "messageId": "msg_123456",
        "status": "delivered",
        "timestamp": 1640995300
    }
}
```

### Обновление контакта
```json
{
    "event": "contact",
    "data": {
        "phone": "+7 777 123 45 67",
        "name": "Анна Смирнова (обновлено)",
        "avatar": "https://wazzup24.com/avatars/avatar.jpg",
        "timestamp": 1640995400
    }
}
```

---

## Обработка ошибок

### Неверный формат данных (400)
```json
{
    "status": "error",
    "message": "Invalid webhook data",
    "errors": {
        "event": ["Event field is required"],
        "data": ["Data field is required"]
    }
}
```

### Внутренняя ошибка сервера (500)
```json
{
    "status": "error",
    "message": "Internal server error",
    "code": 500
}
```

---

## Тестирование Webhook

### Проверка доступности (GET)
```bash
curl -X GET "https://back-chat.ap.kz/api/webhooks/wazzup24"
```

**Ответ:**
```json
{
    "status": "ok",
    "message": "Webhook endpoint is available"
}
```

### Тестовая отправка (POST)
```bash
curl -X POST "https://back-chat.ap.kz/api/webhooks/wazzup24" \
  -H "Content-Type: application/json" \
  -d '{
    "event": "message",
    "data": {
        "id": "test_123",
        "chatId": "test_chat",
        "text": "Тестовое сообщение",
        "type": "text",
        "from": "+7 777 000 00 00",
        "timestamp": 1640995200,
        "contact": {
            "name": "Test User",
            "phone": "+7 777 000 00 00"
        }
    }
  }'
```

---

## Безопасность

### IP Whitelist
Рекомендуется настроить whitelist IP-адресов Wazzup24 для дополнительной безопасности.

### Подпись запроса
Можно добавить проверку подписи запроса для верификации источника:

```json
{
    "event": "message",
    "data": { ... },
    "signature": "sha256=abc123..."
}
```

---

## Логирование

Все webhook запросы логируются с информацией:
- IP адрес отправителя
- User-Agent
- Время запроса
- Размер данных
- Статус обработки

---

## Примеры интеграции

### PHP
```php
// Обработка webhook в вашем приложении
function handleWazzup24Webhook($data) {
    $event = $data['event'] ?? null;
    $webhookData = $data['data'] ?? null;
    
    switch ($event) {
        case 'message':
            // Создать или обновить чат
            $chat = createOrUpdateChat($webhookData['contact']);
            
            // Создать сообщение
            $message = createMessage($chat, $webhookData);
            
            // Отправить уведомление операторам
            notifyOperators($chat, $message);
            break;
            
        case 'status':
            // Обновить статус сообщения
            updateMessageStatus($webhookData['messageId'], $webhookData['status']);
            break;
            
        case 'contact':
            // Обновить информацию о контакте
            updateContact($webhookData);
            break;
    }
}
```

### JavaScript (Node.js)
```javascript
// Express.js обработчик webhook
app.post('/api/webhooks/wazzup24', (req, res) => {
    const { event, data } = req.body;
    
    try {
        switch (event) {
            case 'message':
                handleNewMessage(data);
                break;
            case 'status':
                handleMessageStatus(data);
                break;
            case 'contact':
                handleContactUpdate(data);
                break;
            default:
                console.log('Unknown event:', event);
        }
        
        res.json({ status: 'success', message: 'Webhook processed' });
    } catch (error) {
        console.error('Webhook error:', error);
        res.status(500).json({ status: 'error', message: 'Processing failed' });
    }
});

function handleNewMessage(data) {
    // Логика обработки нового сообщения
    console.log('New message from:', data.from);
    console.log('Text:', data.text);
}
```

---

## Мониторинг

### Метрики для отслеживания:
- Количество входящих webhook запросов
- Время обработки запросов
- Количество ошибок
- Успешность обработки

### Алерты:
- Превышение времени обработки
- Высокий процент ошибок
- Отсутствие запросов (возможная проблема с интеграцией)

---

## Troubleshooting

### Частые проблемы:

1. **Webhook не получает данные**
   - Проверить URL в настройках Wazzup24
   - Убедиться в доступности сервера
   - Проверить логи сервера

2. **Ошибки валидации**
   - Проверить формат отправляемых данных
   - Убедиться в наличии обязательных полей
   - Проверить типы данных

3. **Медленная обработка**
   - Оптимизировать код обработки
   - Использовать очереди для тяжелых операций
   - Мониторить производительность базы данных
