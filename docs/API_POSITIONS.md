# API Эндпоинты - Должности

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

## Должности

### Основные эндпоинты

#### GET `/api/positions` - Получить список должностей

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по названию должности

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Positions retrieved successfully",
    "data": {
        "positions": [
            {
                "id": 1,
                "name": "Разработчик",
                "slug": "razrabotchik",
                "description": "Разработчик программного обеспечения",
                "permissions": [
                    "read_chats",
                    "write_messages",
                    "manage_own_profile"
                ],
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "users_count": 3
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

#### POST `/api/positions` - Создать должность

**Параметры запроса:**
```json
{
    "name": "Менеджер по продажам",
    "slug": "menedzher-po-prodazham",
    "description": "Менеджер по работе с клиентами",
    "permissions": [
        "read_chats",
        "write_messages",
        "manage_clients",
        "view_reports"
    ],
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название должности

**Опциональные поля:**
- `slug` (string, max:255, unique) - URL-слаг должности
- `description` (string) - Описание должности
- `permissions` (array) - Массив разрешений
- `permissions.*` (string) - Отдельное разрешение
- `is_active` (boolean) - Статус активности

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Position created successfully",
    "data": {
        "position": {
            "id": 2,
            "name": "Менеджер по продажам",
            "slug": "menedzher-po-prodazham",
            "description": "Менеджер по работе с клиентами",
            "permissions": [
                "read_chats",
                "write_messages",
                "manage_clients",
                "view_reports"
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### GET `/api/positions/{id}` - Получить должность по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Position retrieved successfully",
    "data": {
        "position": {
            "id": 1,
            "name": "Разработчик",
            "slug": "razrabotchik",
            "description": "Разработчик программного обеспечения",
            "permissions": [
                "read_chats",
                "write_messages",
                "manage_own_profile"
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "users_count": 3
        }
    }
}
```

#### PUT `/api/positions/{id}` - Обновить должность

**Параметры запроса:**
```json
{
    "name": "Старший разработчик",
    "slug": "starshij-razrabotchik",
    "description": "Старший разработчик программного обеспечения",
    "permissions": [
        "read_chats",
        "write_messages",
        "manage_own_profile",
        "manage_team",
        "view_analytics"
    ],
    "is_active": true
}
```

**Опциональные поля:**
- `name` (string, max:255) - Название должности
- `slug` (string, max:255, unique) - URL-слаг должности
- `description` (string) - Описание должности
- `permissions` (array) - Массив разрешений
- `permissions.*` (string) - Отдельное разрешение
- `is_active` (boolean) - Статус активности

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Position updated successfully",
    "data": {
        "position": {
            "id": 1,
            "name": "Старший разработчик",
            "slug": "starshij-razrabotchik",
            "description": "Старший разработчик программного обеспечения",
            "permissions": [
                "read_chats",
                "write_messages",
                "manage_own_profile",
                "manage_team",
                "view_analytics"
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/positions/{id}` - Удалить должность

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Position deleted successfully",
    "data": null
}
```

---

## Доступные разрешения

Система поддерживает следующие типы разрешений:

### Базовые разрешения
- `read_chats` - Чтение чатов
- `write_messages` - Написание сообщений
- `manage_own_profile` - Управление собственным профилем

### Расширенные разрешения
- `manage_clients` - Управление клиентами
- `view_reports` - Просмотр отчетов
- `manage_team` - Управление командой
- `view_analytics` - Просмотр аналитики
- `manage_organizations` - Управление организациями
- `manage_departments` - Управление отделами
- `manage_positions` - Управление должностями
- `manage_users` - Управление пользователями

### Административные разрешения
- `admin_access` - Административный доступ
- `system_settings` - Настройки системы
- `view_logs` - Просмотр логов

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

### Получение списка должностей
```bash
curl -X GET https://back-chat.ap.kz/api/positions \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Поиск должностей
```bash
curl -X GET "https://back-chat.ap.kz/api/positions?search=разработчик" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание новой должности
```bash
curl -X POST https://back-chat.ap.kz/api/positions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Менеджер по продажам",
    "description": "Менеджер по работе с клиентами",
    "permissions": [
      "read_chats",
      "write_messages",
      "manage_clients",
      "view_reports"
    ]
  }'
```

### Обновление должности
```bash
curl -X PUT https://back-chat.ap.kz/api/positions/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Старший разработчик",
    "permissions": [
      "read_chats",
      "write_messages",
      "manage_own_profile",
      "manage_team",
      "view_analytics"
    ]
  }'
```

### Удаление должности
```bash
curl -X DELETE https://back-chat.ap.kz/api/positions/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Должности не привязаны к конкретным организациям - они глобальные
- Разрешения хранятся в виде массива строк
- При создании должности можно указать пустой массив разрешений
- Слаг должности должен быть уникальным в системе
