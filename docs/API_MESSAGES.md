# API Документация - Сообщения

## Базовый URL
```
https://back-chat.ap.kz/api
```

## Аутентификация

Все запросы требуют Bearer Token аутентификации:
```
Authorization: Bearer {token}
```

---

## Отправка сообщения

### POST `/api/chats/{chatId}/send`

Отправить сообщение в чат.

**Заголовки:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
Accept: application/json
```

**Параметры URL:**
- `chatId` (integer) - ID чата

**Параметры запроса:**
```
message: "Текст сообщения"
type: "text" (опционально, по умолчанию: text)
file: файл (опционально)
```

**Типы сообщений:**
- `text` - Текстовое сообщение
- `image` - Изображение
- `video` - Видео
- `file` - Файл

**Поддерживаемые форматы файлов:**
- **Изображения**: jpg, jpeg, png, gif
- **Документы**: pdf, doc, docx, txt
- **Видео**: mp4
- **Аудио**: mp3, wav

**Максимальный размер файла**: 10MB

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "Message sent successfully",
    "data": {
        "id": 5,
        "message": "Текст сообщения",
        "type": "text",
        "is_from_client": false,
        "file_path": null,
        "file_name": null,
        "file_size": null,
        "created_at": "2024-01-01T16:00:00.000000Z",
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        }
    }
}
```

**Ответ с файлом:**
```json
{
    "status": "success",
    "message": "Message sent successfully",
    "data": {
        "id": 6,
        "message": "Отправляю документ",
        "type": "file",
        "is_from_client": false,
        "file_path": "chat_files/1640995200_document.pdf",
        "file_name": "document.pdf",
        "file_size": 1024000,
        "created_at": "2024-01-01T16:00:00.000000Z",
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        }
    }
}
```

**Ошибка валидации (422):**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "message": ["Сообщение обязательно"],
        "file": ["Размер файла не должен превышать 10MB"]
    }
}
```

---

## Получение сообщений чата

### GET `/api/chats/{chatId}/messages`

Получить сообщения конкретного чата с пагинацией.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры URL:**
- `chatId` (integer) - ID чата

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы (по умолчанию: 1)
- `per_page` (integer, optional) - Количество сообщений на странице (по умолчанию: 50)

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Messages retrieved successfully",
    "data": [
        {
            "id": 5,
            "message": "Текст сообщения",
            "type": "text",
            "is_from_client": false,
            "file_path": null,
            "file_name": null,
            "file_size": null,
            "created_at": "2024-01-01T16:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "Test User",
                "email": "test@back-chat.ap.kz"
            }
        },
        {
            "id": 4,
            "message": "Привет! Как дела?",
            "type": "text",
            "is_from_client": true,
            "file_path": null,
            "file_name": null,
            "file_size": null,
            "created_at": "2024-01-01T15:30:00.000000Z",
            "user": null
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 3,
            "per_page": 50,
            "total": 125,
            "from": 1,
            "to": 50,
            "has_more_pages": true,
            "links": {
                "first": "https://back-chat.ap.kz/api/chats/1/messages?page=1",
                "last": "https://back-chat.ap.kz/api/chats/1/messages?page=3",
                "prev": null,
                "next": "https://back-chat.ap.kz/api/chats/1/messages?page=2"
            }
        },
        "timestamp": "2024-01-01T16:00:00.000000Z",
        "version": "1.0.0"
    }
}
```

---

## Отправка системного сообщения

### POST `/api/messages/chats/{chat}/system-message`

Отправить системное сообщение в чат.

**Заголовки:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Параметры URL:**
- `chat` (integer) - ID чата

**Параметры запроса:**
```json
{
    "message": "Чат передан другому оператору"
}
```

**Обязательные поля:**
- `message` (string) - Текст системного сообщения

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "System message sent successfully",
    "data": {
        "id": 7,
        "message": "Чат передан другому оператору",
        "type": "system",
        "is_from_client": false,
        "created_at": "2024-01-01T16:00:00.000000Z",
        "user": {
            "id": 1,
            "name": "Test User"
        }
    }
}
```

---

## Скрытие сообщения

### POST `/api/messages/{message}/hide`

Скрыть сообщение (пометить как удаленное).

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры URL:**
- `message` (integer) - ID сообщения

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Message hidden successfully",
    "data": null
}
```

---

## Получение сообщений чата (альтернативный эндпоинт)

### GET `/api/messages/chats/{chat}`

Альтернативный способ получения сообщений чата.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры URL:**
- `chat` (integer) - ID чата

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы
- `per_page` (integer, optional) - Количество сообщений на странице

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Messages retrieved successfully",
    "data": [
        {
            "id": 5,
            "message": "Текст сообщения",
            "type": "text",
            "is_from_client": false,
            "created_at": "2024-01-01T16:00:00.000000Z"
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 50,
            "total": 1,
            "from": 1,
            "to": 1,
            "has_more_pages": false
        }
    }
}
```

---

## Коды ошибок

| Код | Описание |
|-----|----------|
| 200 | Успешный запрос |
| 201 | Сообщение отправлено |
| 400 | Неверный запрос |
| 401 | Не авторизован |
| 403 | Доступ запрещен |
| 404 | Чат не найден |
| 422 | Ошибка валидации |
| 429 | Превышен лимит запросов (30 сообщений в минуту) |
| 500 | Внутренняя ошибка сервера |

---

## Rate Limiting

- **Отправка сообщений**: 30 запросов в минуту
- **Получение сообщений**: 60 запросов в минуту

---

## Примеры использования

### JavaScript (Fetch API)
```javascript
// Отправка текстового сообщения
const sendMessage = async (chatId, message) => {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('message', message);
    formData.append('type', 'text');
    
    const response = await fetch(`/api/chats/${chatId}/send`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        body: formData
    });
    
    return await response.json();
};

// Отправка сообщения с файлом
const sendMessageWithFile = async (chatId, message, file) => {
    const token = localStorage.getItem('token');
    const formData = new FormData();
    formData.append('message', message);
    formData.append('type', 'file');
    formData.append('file', file);
    
    const response = await fetch(`/api/chats/${chatId}/send`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        body: formData
    });
    
    return await response.json();
};

// Получение сообщений чата
const getChatMessages = async (chatId, page = 1) => {
    const token = localStorage.getItem('token');
    const response = await fetch(`/api/chats/${chatId}/messages?page=${page}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};

// Отправка системного сообщения
const sendSystemMessage = async (chatId, message) => {
    const token = localStorage.getItem('token');
    const response = await fetch(`/api/messages/chats/${chatId}/system-message`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    });
    
    return await response.json();
};
```

### cURL
```bash
# Отправка текстового сообщения
curl -X POST "https://back-chat.ap.kz/api/chats/1/send" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "message=Привет! Как дела?" \
  -F "type=text"

# Отправка сообщения с файлом
curl -X POST "https://back-chat.ap.kz/api/chats/1/send" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "message=Отправляю документ" \
  -F "type=file" \
  -F "file=@/path/to/document.pdf"

# Получение сообщений чата
curl -X GET "https://back-chat.ap.kz/api/chats/1/messages?page=1&per_page=20" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Отправка системного сообщения
curl -X POST "https://back-chat.ap.kz/api/messages/chats/1/system-message" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"message": "Чат передан другому оператору"}'
```

### PHP (cURL)
```php
// Отправка сообщения
function sendMessage($chatId, $message, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://back-chat.ap.kz/api/chats/{$chatId}/send");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'message' => $message,
        'type' => 'text'
    ]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Отправка сообщения с файлом
function sendMessageWithFile($chatId, $message, $filePath, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://back-chat.ap.kz/api/chats/{$chatId}/send");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'message' => $message,
        'type' => 'file',
        'file' => new CURLFile($filePath)
    ]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

---

## WebSocket / Server-Sent Events

Для real-time обновлений сообщений можно использовать Server-Sent Events:

### GET `/api/chats/{chatId}/stream`

Получить поток обновлений сообщений в реальном времени.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: text/event-stream
Cache-Control: no-cache
```

**Пример использования в JavaScript:**
```javascript
const eventSource = new EventSource(`/api/chats/${chatId}/stream`, {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});

eventSource.onmessage = function(event) {
    const message = JSON.parse(event.data);
    console.log('New message:', message);
    // Обновить UI с новым сообщением
};

eventSource.onerror = function(event) {
    console.error('SSE error:', event);
};
```
