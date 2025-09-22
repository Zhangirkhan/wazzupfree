# API Эндпоинты - Чаты и Сообщения

## Базовый URL
```
https://back-chat.ap.kz/api
```

## Аутентификация
API использует Bearer Token аутентификацию через Laravel Sanctum.

### Заголовки запросов
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Чаты

### Основные эндпоинты

#### GET `/api/chats` - Получить список чатов

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Chats retrieved successfully",
    "data": {
        "chats": [
            {
                "id": 1,
                "client_name": "Иван Иванов",
                "client_phone": "+7 777 123 45 67",
                "client_email": null,
                "status": "active",
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "Петр Петров",
                    "email": "petr@company.kz",
                    "phone": "+7 777 987 65 43",
                    "position": "Менеджер",
                    "avatar": "https://example.com/avatars/petr.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "assigned_user": {
                    "id": 2,
                    "name": "Анна Смирнова",
                    "email": "anna@company.kz",
                    "phone": "+7 777 555 44 33",
                    "position": "Специалист",
                    "avatar": "https://example.com/avatars/anna.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "messages_count": 15,
                "last_message": {
                    "id": 25,
                    "message": "Спасибо за обращение!",
                    "type": "text",
                    "is_from_client": false,
                    "file_path": null,
                    "file_name": null,
                    "file_size": null,
                    "created_at": "2024-01-01T15:30:00.000000Z",
                    "user": {
                        "id": 2,
                        "name": "Анна Смирнова",
                        "email": "anna@company.kz",
                        "phone": "+7 777 555 44 33",
                        "position": "Специалист",
                        "avatar": "https://example.com/avatars/anna.jpg",
                        "role": "employee",
                        "department_id": 1,
                        "is_active": true,
                        "created_at": "2024-01-01T12:00:00.000000Z",
                        "updated_at": "2024-01-01T12:00:00.000000Z"
                    }
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 20,
            "total": 1
        }
    }
}
```

#### POST `/api/chats` - Создать новый чат

**Параметры запроса:**
```json
{
    "title": "Чат с клиентом",
    "description": "Обсуждение заказа",
    "type": "support",
    "assigned_to": 2,
    "department_id": 1
}
```

**Обязательные поля:**
- `title` (string, max:255) - Название чата
- `type` (string, in:support,sales,general) - Тип чата

**Опциональные поля:**
- `description` (string) - Описание чата
- `assigned_to` (integer, exists:users) - ID назначенного пользователя
- `department_id` (integer, exists:departments) - ID отдела

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Chat created successfully",
    "data": {
        "chat": {
            "id": 2,
            "client_name": "Чат с клиентом",
            "client_phone": null,
            "client_email": null,
            "status": "active",
            "created_at": "2024-01-01T16:00:00.000000Z",
            "updated_at": "2024-01-01T16:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "Петр Петров",
                "email": "petr@company.kz",
                "phone": "+7 777 987 65 43",
                "position": "Менеджер",
                "avatar": "https://example.com/avatars/petr.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "assigned_user": {
                "id": 2,
                "name": "Анна Смирнова",
                "email": "anna@company.kz",
                "phone": "+7 777 555 44 33",
                "position": "Специалист",
                "avatar": "https://example.com/avatars/anna.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        }
    }
}
```

#### GET `/api/chats/{id}` - Получить конкретный чат

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Chat retrieved successfully",
    "data": {
        "chat": {
            "id": 1,
            "client_name": "Иван Иванов",
            "client_phone": "+7 777 123 45 67",
            "client_email": null,
            "status": "active",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "Петр Петров",
                "email": "petr@company.kz",
                "phone": "+7 777 987 65 43",
                "position": "Менеджер",
                "avatar": "https://example.com/avatars/petr.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "assigned_user": {
                "id": 2,
                "name": "Анна Смирнова",
                "email": "anna@company.kz",
                "phone": "+7 777 555 44 33",
                "position": "Специалист",
                "avatar": "https://example.com/avatars/anna.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "messages": [
                {
                    "id": 1,
                    "message": "Здравствуйте! У меня вопрос по заказу",
                    "type": "text",
                    "is_from_client": true,
                    "file_path": null,
                    "file_name": null,
                    "file_size": null,
                    "created_at": "2024-01-01T12:05:00.000000Z",
                    "user": null
                },
                {
                    "id": 2,
                    "message": "Здравствуйте! Слушаю вас",
                    "type": "text",
                    "is_from_client": false,
                    "file_path": null,
                    "file_name": null,
                    "file_size": null,
                    "created_at": "2024-01-01T12:06:00.000000Z",
                    "user": {
                        "id": 2,
                        "name": "Анна Смирнова",
                        "email": "anna@company.kz",
                        "phone": "+7 777 555 44 33",
                        "position": "Специалист",
                        "avatar": "https://example.com/avatars/anna.jpg",
                        "role": "employee",
                        "department_id": 1,
                        "is_active": true,
                        "created_at": "2024-01-01T12:00:00.000000Z",
                        "updated_at": "2024-01-01T12:00:00.000000Z"
                    }
                }
            ]
        }
    }
}
```

#### GET `/api/chats/search` - Поиск чатов

**Параметры запроса:**
- `query` (string, required) - Поисковый запрос (минимум 2 символа)
- `status` (string, optional) - Фильтр по статусу (active, closed, transferred)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Search results retrieved successfully",
    "data": {
        "chats": [
            {
                "id": 1,
                "client_name": "Иван Иванов",
                "client_phone": "+7 777 123 45 67",
                "client_email": null,
                "status": "active",
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "user": {
                    "id": 1,
                    "name": "Петр Петров",
                    "email": "petr@company.kz",
                    "phone": "+7 777 987 65 43",
                    "position": "Менеджер",
                    "avatar": "https://example.com/avatars/petr.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "assigned_user": {
                    "id": 2,
                    "name": "Анна Смирнова",
                    "email": "anna@company.kz",
                    "phone": "+7 777 555 44 33",
                    "position": "Специалист",
                    "avatar": "https://example.com/avatars/anna.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "messages_count": 15
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 20,
            "total": 1
        }
    }
}
```

#### POST `/api/chats/{chatId}/end` - Завершить чат

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Chat ended successfully",
    "data": {
        "chat": {
            "id": 1,
            "client_name": "Иван Иванов",
            "client_phone": "+7 777 123 45 67",
            "client_email": null,
            "status": "closed",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T16:30:00.000000Z",
            "user": {
                "id": 1,
                "name": "Петр Петров",
                "email": "petr@company.kz",
                "phone": "+7 777 987 65 43",
                "position": "Менеджер",
                "avatar": "https://example.com/avatars/petr.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "assigned_user": {
                "id": 2,
                "name": "Анна Смирнова",
                "email": "anna@company.kz",
                "phone": "+7 777 555 44 33",
                "position": "Специалист",
                "avatar": "https://example.com/avatars/anna.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        }
    }
}
```

#### POST `/api/chats/{chatId}/transfer` - Передать чат

**Параметры запроса:**
```json
{
    "assigned_to": 3,
    "note": "Передача чата специалисту по техническим вопросам"
}
```

**Обязательные поля:**
- `assigned_to` (integer, exists:users) - ID нового назначенного пользователя

**Опциональные поля:**
- `note` (string) - Примечание к передаче

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Chat transferred successfully",
    "data": {
        "chat": {
            "id": 1,
            "client_name": "Иван Иванов",
            "client_phone": "+7 777 123 45 67",
            "client_email": null,
            "status": "active",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T17:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "Петр Петров",
                "email": "petr@company.kz",
                "phone": "+7 777 987 65 43",
                "position": "Менеджер",
                "avatar": "https://example.com/avatars/petr.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "assigned_user": {
                "id": 3,
                "name": "Сергей Козлов",
                "email": "sergey@company.kz",
                "phone": "+7 777 111 22 33",
                "position": "Технический специалист",
                "avatar": "https://example.com/avatars/sergey.jpg",
                "role": "employee",
                "department_id": 2,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        }
    }
}
```

---

## Сообщения

### Основные эндпоинты

#### GET `/api/chats/{chatId}/messages` - Получить сообщения чата

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 50)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Messages retrieved successfully",
    "data": {
        "messages": [
            {
                "id": 1,
                "message": "Здравствуйте! У меня вопрос по заказу",
                "type": "text",
                "is_from_client": true,
                "file_path": null,
                "file_name": null,
                "file_size": null,
                "created_at": "2024-01-01T12:05:00.000000Z",
                "user": null
            },
            {
                "id": 2,
                "message": "Здравствуйте! Слушаю вас",
                "type": "text",
                "is_from_client": false,
                "file_path": null,
                "file_name": null,
                "file_size": null,
                "created_at": "2024-01-01T12:06:00.000000Z",
                "user": {
                    "id": 2,
                    "name": "Анна Смирнова",
                    "email": "anna@company.kz",
                    "phone": "+7 777 555 44 33",
                    "position": "Специалист",
                    "avatar": "https://example.com/avatars/anna.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            },
            {
                "id": 3,
                "message": "Вот документ с техническими характеристиками",
                "type": "file",
                "is_from_client": false,
                "file_path": "/uploads/documents/tech_specs.pdf",
                "file_name": "tech_specs.pdf",
                "file_size": 1024000,
                "created_at": "2024-01-01T12:10:00.000000Z",
                "user": {
                    "id": 2,
                    "name": "Анна Смирнова",
                    "email": "anna@company.kz",
                    "phone": "+7 777 555 44 33",
                    "position": "Специалист",
                    "avatar": "https://example.com/avatars/anna.jpg",
                    "role": "employee",
                    "department_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 50,
            "total": 3
        }
    }
}
```

#### POST `/api/chats/{chatId}/send` - Отправить сообщение

**Параметры запроса:**
```json
{
    "message": "Спасибо за обращение! Мы решим ваш вопрос",
    "type": "text"
}
```

**Обязательные поля:**
- `message` (string, max:1000) - Текст сообщения

**Опциональные поля:**
- `type` (string, in:text,file,image,system) - Тип сообщения (по умолчанию: text)
- `file` (file) - Файл для отправки (если type = file или image)

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Message sent successfully",
    "data": {
        "message": {
            "id": 4,
            "message": "Спасибо за обращение! Мы решим ваш вопрос",
            "type": "text",
            "is_from_client": false,
            "file_path": null,
            "file_name": null,
            "file_size": null,
            "created_at": "2024-01-01T12:15:00.000000Z",
            "user": {
                "id": 2,
                "name": "Анна Смирнова",
                "email": "anna@company.kz",
                "phone": "+7 777 555 44 33",
                "position": "Специалист",
                "avatar": "https://example.com/avatars/anna.jpg",
                "role": "employee",
                "department_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        }
    }
}
```

### Дополнительные эндпоинты для сообщений

#### GET `/api/messages/chats/{chat}` - Получить сообщения чата (альтернативный эндпоинт)

**Ответ (200):**
```json
{
    "messages": {
        "data": [
            {
                "id": 1,
                "chat_id": 1,
                "user_id": null,
                "content": "Здравствуйте! У меня вопрос по заказу",
                "type": "text",
                "metadata": null,
                "is_hidden": false,
                "hidden_by": null,
                "hidden_at": null,
                "wazzup_message_id": null,
                "direction": "in",
                "status": "delivered",
                "created_at": "2024-01-01T12:05:00.000000Z",
                "updated_at": "2024-01-01T12:05:00.000000Z",
                "user": null
            }
        ],
        "current_page": 1,
        "last_page": 1,
        "per_page": 50,
        "total": 1
    }
}
```

#### POST `/api/messages/chats/{chat}` - Отправить сообщение (альтернативный эндпоинт)

**Параметры запроса:**
```json
{
    "content": "Понял, спасибо за информацию",
    "type": "text",
    "metadata": {
        "priority": "normal"
    }
}
```

**Обязательные поля:**
- `content` (string, max:1000) - Содержимое сообщения

**Опциональные поля:**
- `type` (string, in:text,system,file,image) - Тип сообщения
- `metadata` (array) - Дополнительные данные

**Ответ (201):**
```json
{
    "message": {
        "id": 5,
        "chat_id": 1,
        "user_id": 2,
        "content": "Понял, спасибо за информацию",
        "type": "text",
        "metadata": {
            "priority": "normal"
        },
        "is_hidden": false,
        "hidden_by": null,
        "hidden_at": null,
        "wazzup_message_id": null,
        "direction": "out",
        "status": "sent",
        "created_at": "2024-01-01T12:20:00.000000Z",
        "updated_at": "2024-01-01T12:20:00.000000Z",
        "user": {
            "id": 2,
            "name": "Анна Смирнова",
            "email": "anna@company.kz",
            "phone": "+7 777 555 44 33",
            "position": "Специалист",
            "avatar": "https://example.com/avatars/anna.jpg",
            "role": "employee",
            "department_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    },
    "status": "Message sent successfully"
}
```

#### POST `/api/messages/{message}/hide` - Скрыть сообщение

**Ответ (200):**
```json
{
    "message": "Message hidden successfully"
}
```

#### POST `/api/messages/chats/{chat}/system-message` - Отправить системное сообщение

**Параметры запроса:**
```json
{
    "content": "Чат передан другому специалисту"
}
```

**Обязательные поля:**
- `content` (string, max:500) - Содержимое системного сообщения

**Ответ (201):**
```json
{
    "message": {
        "id": 6,
        "chat_id": 1,
        "user_id": 1,
        "content": "Чат передан другому специалисту",
        "type": "system",
        "metadata": null,
        "is_hidden": false,
        "hidden_by": null,
        "hidden_at": null,
        "wazzup_message_id": null,
        "direction": "out",
        "status": "sent",
        "created_at": "2024-01-01T12:25:00.000000Z",
        "updated_at": "2024-01-01T12:25:00.000000Z",
        "user": {
            "id": 1,
            "name": "Петр Петров",
            "email": "petr@company.kz",
            "phone": "+7 777 987 65 43",
            "position": "Менеджер",
            "avatar": "https://example.com/avatars/petr.jpg",
            "role": "employee",
            "department_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    },
    "status": "System message sent successfully"
}
```

---

## Статусы чатов

- `active` - Активный чат
- `closed` - Закрытый чат
- `transferred` - Переданный чат
- `pending` - Ожидающий чат

## Типы сообщений

- `text` - Текстовое сообщение
- `file` - Файл
- `image` - Изображение
- `system` - Системное сообщение

## Направления сообщений

- `in` - Входящее сообщение (от клиента)
- `out` - Исходящее сообщение (от сотрудника)

---

## Коды ответов

| Код | Описание |
|-----|----------|
| 200 | Успешный запрос |
| 201 | Ресурс создан |
| 400 | Неверный запрос |
| 401 | Не авторизован |
| 403 | Доступ запрещен |
| 404 | Ресурс не найден |
| 422 | Ошибка валидации |
| 429 | Превышен лимит запросов |
| 500 | Внутренняя ошибка сервера |

---

## Примеры использования

### Получение списка чатов
```bash
curl -X GET https://back-chat.ap.kz/api/chats \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание нового чата
```bash
curl -X POST https://back-chat.ap.kz/api/chats \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Чат с клиентом",
    "description": "Обсуждение заказа",
    "type": "support",
    "assigned_to": 2,
    "department_id": 1
  }'
```

### Получение сообщений чата
```bash
curl -X GET https://back-chat.ap.kz/api/chats/1/messages \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Отправка сообщения
```bash
curl -X POST https://back-chat.ap.kz/api/chats/1/send \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "message": "Спасибо за обращение!",
    "type": "text"
  }'
```

### Поиск чатов
```bash
curl -X GET "https://back-chat.ap.kz/api/chats/search?query=Иван&status=active" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Завершение чата
```bash
curl -X POST https://back-chat.ap.kz/api/chats/1/end \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Передача чата
```bash
curl -X POST https://back-chat.ap.kz/api/chats/1/transfer \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "assigned_to": 3,
    "note": "Передача чата специалисту"
  }'
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Отправка сообщений ограничена 30 запросами в минуту (throttle:30,1)
- Пользователь может видеть только чаты, в которых он является участником
- Системные сообщения могут отправлять только администраторы чата
- Скрывать сообщения могут только пользователи с правами выше автора сообщения
