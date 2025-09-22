# API Документация - Чаты

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

## Получение списка чатов

### GET `/api/chats`

Получить список чатов пользователя с пагинацией.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы (по умолчанию: 1)
- `per_page` (integer, optional) - Количество элементов на странице (по умолчанию: 20)

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Chats retrieved successfully",
    "data": [
        {
            "id": 1,
            "client_name": "Иван Петров",
            "client_phone": "+7 777 123 45 67",
            "client_email": "ivan@example.com",
            "status": "active",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T15:30:00.000000Z",
            "user": {
                "id": 1,
                "name": "Test User",
                "email": "test@back-chat.ap.kz"
            },
            "assigned_user": {
                "id": 1,
                "name": "Test User",
                "email": "test@back-chat.ap.kz"
            },
            "messages": [
                {
                    "id": 1,
                    "message": "Привет! Как дела?",
                    "type": "text",
                    "is_from_client": true,
                    "created_at": "2024-01-01T12:00:00.000000Z"
                }
            ]
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 100,
            "from": 1,
            "to": 20,
            "has_more_pages": true,
            "links": {
                "first": "https://back-chat.ap.kz/api/chats?page=1",
                "last": "https://back-chat.ap.kz/api/chats?page=5",
                "prev": null,
                "next": "https://back-chat.ap.kz/api/chats?page=2"
            }
        },
        "timestamp": "2024-01-01T16:00:00.000000Z",
        "version": "1.0.0"
    }
}
```

---

## Создание нового чата

### POST `/api/chats`

Создать новый чат с клиентом.

**Заголовки:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Параметры запроса:**
```json
{
    "client_name": "Анна Смирнова",
    "client_phone": "+7 777 987 65 43",
    "client_email": "anna@example.com",
    "message": "Здравствуйте! У меня вопрос по услугам.",
    "department_id": 1
}
```

**Обязательные поля:**
- `client_name` (string, max:255) - Имя клиента
- `client_phone` (string, max:20) - Телефон клиента
- `message` (string) - Первое сообщение

**Опциональные поля:**
- `client_email` (string, email, max:255) - Email клиента
- `department_id` (integer, exists:departments) - ID отдела

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "Chat created successfully",
    "data": {
        "id": 2,
        "client_name": "Анна Смирнова",
        "client_phone": "+7 777 987 65 43",
        "client_email": "anna@example.com",
        "status": "active",
        "created_at": "2024-01-01T16:00:00.000000Z",
        "updated_at": "2024-01-01T16:00:00.000000Z",
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        },
        "assigned_user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        },
        "messages": [
            {
                "id": 2,
                "message": "Здравствуйте! У меня вопрос по услугам.",
                "type": "text",
                "is_from_client": false,
                "created_at": "2024-01-01T16:00:00.000000Z"
            }
        ]
    }
}
```

---

## Получение конкретного чата

### GET `/api/chats/{id}`

Получить информацию о конкретном чате.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры URL:**
- `id` (integer) - ID чата

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Chat retrieved successfully",
    "data": {
        "id": 1,
        "client_name": "Иван Петров",
        "client_phone": "+7 777 123 45 67",
        "client_email": "ivan@example.com",
        "status": "active",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T15:30:00.000000Z",
        "user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        },
        "assigned_user": {
            "id": 1,
            "name": "Test User",
            "email": "test@back-chat.ap.kz"
        },
        "messages": [
            {
                "id": 1,
                "message": "Привет! Как дела?",
                "type": "text",
                "is_from_client": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "Test User"
                }
            }
        ]
    }
}
```

**Ошибка (404):**
```json
{
    "status": "error",
    "message": "Chat not found",
    "code": 404
}
```

---

## Поиск чатов

### GET `/api/chats/search`

Поиск чатов по клиентам.

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры запроса:**
- `query` (string, required, min:2) - Поисковый запрос
- `status` (string, optional) - Фильтр по статусу (active, closed, transferred)
- `page` (integer, optional) - Номер страницы
- `per_page` (integer, optional) - Количество элементов на странице

**Пример запроса:**
```
GET /api/chats/search?query=Иван&status=active&page=1&per_page=10
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Search results retrieved successfully",
    "data": [
        {
            "id": 1,
            "client_name": "Иван Петров",
            "client_phone": "+7 777 123 45 67",
            "status": "active",
            "created_at": "2024-01-01T12:00:00.000000Z"
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 10,
            "total": 1,
            "from": 1,
            "to": 1,
            "has_more_pages": false
        }
    }
}
```

---

## Завершение чата

### POST `/api/chats/{id}/end`

Завершить чат (изменить статус на "closed").

**Заголовки:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Параметры URL:**
- `id` (integer) - ID чата

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Chat ended successfully",
    "data": {
        "id": 1,
        "client_name": "Иван Петров",
        "status": "closed",
        "updated_at": "2024-01-01T16:00:00.000000Z"
    }
}
```

---

## Передача чата

### POST `/api/chats/{id}/transfer`

Передать чат другому пользователю.

**Заголовки:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Параметры URL:**
- `id` (integer) - ID чата

**Параметры запроса:**
```json
{
    "assigned_to": 2,
    "note": "Передаю чат для консультации по техническим вопросам"
}
```

**Обязательные поля:**
- `assigned_to` (integer, exists:users) - ID пользователя-получателя

**Опциональные поля:**
- `note` (string, max:500) - Примечание к передаче

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Chat transferred successfully",
    "data": {
        "id": 1,
        "client_name": "Иван Петров",
        "status": "transferred",
        "assigned_user": {
            "id": 2,
            "name": "Другой Пользователь",
            "email": "other@example.com"
        },
        "updated_at": "2024-01-01T16:00:00.000000Z"
    }
}
```

---

## Коды ошибок

| Код | Описание |
|-----|----------|
| 200 | Успешный запрос |
| 201 | Ресурс создан |
| 400 | Неверный запрос |
| 401 | Не авторизован |
| 403 | Доступ запрещен |
| 404 | Чат не найден |
| 422 | Ошибка валидации |
| 429 | Превышен лимит запросов |
| 500 | Внутренняя ошибка сервера |

---

## Примеры использования

### JavaScript (Fetch API)
```javascript
// Получение списка чатов
const getChats = async (page = 1) => {
    const token = localStorage.getItem('token');
    const response = await fetch(`/api/chats?page=${page}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};

// Создание чата
const createChat = async (chatData) => {
    const token = localStorage.getItem('token');
    const response = await fetch('/api/chats', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(chatData)
    });
    
    return await response.json();
};

// Поиск чатов
const searchChats = async (query, status = null) => {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams({ query });
    if (status) params.append('status', status);
    
    const response = await fetch(`/api/chats/search?${params}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};
```

### cURL
```bash
# Получение списка чатов
curl -X GET "https://back-chat.ap.kz/api/chats" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Создание чата
curl -X POST "https://back-chat.ap.kz/api/chats" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "client_name": "Анна Смирнова",
    "client_phone": "+7 777 987 65 43",
    "message": "Здравствуйте! У меня вопрос."
  }'

# Поиск чатов
curl -X GET "https://back-chat.ap.kz/api/chats/search?query=Анна&status=active" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```
