# API Эндпоинты - Профиль пользователя

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

## Профиль пользователя

### Основные эндпоинты

#### GET `/api/auth/me` - Получить информацию о текущем пользователе

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User information retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Петр Петров",
            "email": "petr@company.kz",
            "phone": "+7 777 987 65 43",
            "position": "Менеджер",
            "avatar": "https://example.com/avatars/petr.jpg",
            "role": "employee",
            "department_id": 1,
            "organization_id": 1,
            "is_active": true,
            "email_verified_at": "2024-01-01T12:00:00.000000Z",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "organizations": [
                {
                    "id": 1,
                    "name": "ООО Рога и Копыта",
                    "inn": "123456789012",
                    "kpp": "123456789",
                    "ogrn": "1234567890123",
                    "legal_address": "г. Алматы, ул. Абая, 1",
                    "actual_address": "г. Алматы, ул. Абая, 1",
                    "phone": "+7 727 123 45 67",
                    "email": "info@roga-kopyta.kz",
                    "website": "https://roga-kopyta.kz",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            ],
            "roles": [
                {
                    "id": 1,
                    "name": "employee",
                    "display_name": "Сотрудник",
                    "description": "Обычный сотрудник компании",
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            ],
            "department": {
                "id": 1,
                "name": "Отдел продаж",
                "description": "Отдел по работе с клиентами и продажам",
                "organization_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        },
        "permissions": [
            "dashboard",
            "clients",
            "messenger"
        ],
        "roles": [
            "employee"
        ]
    }
}
```

#### PUT `/api/auth/profile` - Обновить профиль пользователя

**Параметры запроса:**
```json
{
    "name": "Петр Иванович Петров",
    "phone": "+7 777 987 65 43",
    "position": "Старший менеджер"
}
```

**Опциональные поля:**
- `name` (string, max:255) - Имя пользователя
- `phone` (string, max:20) - Телефон
- `position` (string, max:255) - Должность
- `avatar` (file) - Аватар (изображение, максимум 2MB)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "Петр Иванович Петров",
        "email": "petr@company.kz",
        "phone": "+7 777 987 65 43",
        "position": "Старший менеджер",
        "avatar": "https://example.com/avatars/petr.jpg",
        "role": "employee",
        "department_id": 1,
        "organization_id": 1,
        "is_active": true,
        "email_verified_at": "2024-01-01T12:00:00.000000Z",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T15:30:00.000000Z",
        "organizations": [
            {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая, 1",
                "actual_address": "г. Алматы, ул. Абая, 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        ],
        "roles": [
            {
                "id": 1,
                "name": "employee",
                "display_name": "Сотрудник",
                "description": "Обычный сотрудник компании",
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        ],
        "department": {
            "id": 1,
            "name": "Отдел продаж",
            "description": "Отдел по работе с клиентами и продажам",
            "organization_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        }
    }
}
```

#### PUT `/api/auth/password` - Изменить пароль

**Параметры запроса:**
```json
{
    "current_password": "old_password123",
    "new_password": "new_password123",
    "new_password_confirmation": "new_password123"
}
```

**Обязательные поля:**
- `current_password` (string) - Текущий пароль
- `new_password` (string, min:8) - Новый пароль
- `new_password_confirmation` (string) - Подтверждение нового пароля

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Password changed successfully",
    "data": null
}
```

**Ответ при неверном текущем пароле (400):**
```json
{
    "status": "error",
    "message": "Current password is incorrect",
    "data": null
}
```

#### GET `/api/auth/stats` - Получить статистику пользователя

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User statistics retrieved successfully",
    "data": {
        "total_chats": 25,
        "active_chats": 8,
        "closed_chats": 17,
        "total_messages": 156,
        "messages_today": 12,
        "avg_response_time": "2.5 minutes",
        "client_satisfaction": 4.8,
        "work_hours": {
            "today": "7.5 hours",
            "this_week": "37.5 hours",
            "this_month": "150 hours"
        },
        "performance": {
            "chats_handled": 25,
            "response_rate": 98.5,
            "resolution_rate": 92.0
        },
        "recent_activity": [
            {
                "type": "chat_created",
                "description": "Создан новый чат с клиентом",
                "timestamp": "2024-01-01T14:30:00.000000Z"
            },
            {
                "type": "message_sent",
                "description": "Отправлено сообщение клиенту",
                "timestamp": "2024-01-01T14:25:00.000000Z"
            },
            {
                "type": "chat_closed",
                "description": "Закрыт чат с клиентом",
                "timestamp": "2024-01-01T14:20:00.000000Z"
            }
        ]
    }
}
```

#### POST `/api/auth/logout` - Выход из системы

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Logout successful",
    "data": null
}
```

#### POST `/api/auth/refresh` - Обновить токен

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Token refreshed successfully",
    "data": {
        "access_token": "new_access_token_here",
        "token_type": "Bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "name": "Петр Петров",
            "email": "petr@company.kz",
            "phone": "+7 777 987 65 43",
            "position": "Менеджер",
            "avatar": "https://example.com/avatars/petr.jpg",
            "role": "employee",
            "department_id": 1,
            "organization_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

---

## Роли и права доступа

### Роли пользователей

| Роль | Описание | Права доступа |
|------|----------|---------------|
| `admin` | Администратор | dashboard, users, departments, chats, organizations, positions, clients, settings |
| `manager` | Менеджер | dashboard, clients, messenger |
| `employee` | Сотрудник | dashboard, clients, messenger |
| `user` | Пользователь | dashboard, chats, messenger |

### Права доступа по ролям

#### Администратор
- Полный доступ ко всем разделам системы
- Управление пользователями, отделами, организациями
- Настройки системы
- Просмотр всех чатов и сообщений

#### Менеджер
- Управление клиентами
- Работа с мессенджером
- Просмотр дашборда
- Ограниченный доступ к настройкам

#### Сотрудник
- Работа с клиентами
- Использование мессенджера
- Просмотр дашборда
- Базовые функции системы

#### Пользователь
- Просмотр чатов
- Использование мессенджера
- Просмотр дашборда
- Минимальные права доступа

---

## Коды ответов

| Код | Описание |
|-----|----------|
| 200 | Успешный запрос |
| 400 | Неверный запрос |
| 401 | Не авторизован |
| 403 | Доступ запрещен |
| 404 | Ресурс не найден |
| 422 | Ошибка валидации |
| 500 | Внутренняя ошибка сервера |

---

## Примеры использования

### Получение информации о пользователе
```bash
curl -X GET https://back-chat.ap.kz/api/auth/me \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Обновление профиля
```bash
curl -X PUT https://back-chat.ap.kz/api/auth/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Петр Иванович Петров",
    "phone": "+7 777 987 65 43",
    "position": "Старший менеджер"
  }'
```

### Загрузка аватара
```bash
curl -X PUT https://back-chat.ap.kz/api/auth/profile \
  -H "Authorization: Bearer {token}" \
  -F "avatar=@/path/to/avatar.jpg" \
  -F "name=Петр Петров"
```

### Изменение пароля
```bash
curl -X PUT https://back-chat.ap.kz/api/auth/password \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_password": "old_password123",
    "new_password": "new_password123",
    "new_password_confirmation": "new_password123"
  }'
```

### Получение статистики
```bash
curl -X GET https://back-chat.ap.kz/api/auth/stats \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Выход из системы
```bash
curl -X POST https://back-chat.ap.kz/api/auth/logout \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Обновление токена
```bash
curl -X POST https://back-chat.ap.kz/api/auth/refresh \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для обновления профиля используется Content-Type: application/json
- Для загрузки аватара используется multipart/form-data
- Пароль должен содержать минимум 8 символов
- Аватар должен быть изображением размером не более 2MB
- Токен обновляется автоматически при каждом запросе
- Статистика обновляется в реальном времени
- Права доступа зависят от роли пользователя
- Email пользователя нельзя изменить через API профиля
