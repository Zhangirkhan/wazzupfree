# API Эндпоинты - Организации

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

## Организации

### Основные эндпоинты

#### GET `/api/organizations` - Получить список организаций

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по названию организации

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Organizations retrieved successfully",
    "data": {
        "organizations": [
            {
                "id": 1,
                "name": "ООО Компания",
                "slug": "ooo-kompaniya",
                "description": "Описание компании",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "departments_count": 5,
                "users_count": 25
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

#### POST `/api/organizations` - Создать организацию

**Параметры запроса:**
```json
{
    "name": "ООО Новая Компания",
    "slug": "ooo-novaya-kompaniya",
    "description": "Описание новой компании",
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название организации

**Опциональные поля:**
- `slug` (string, max:255, unique) - URL-слаг организации
- `description` (string) - Описание организации
- `is_active` (boolean) - Статус активности

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Organization created successfully",
    "data": {
        "organization": {
            "id": 2,
            "name": "ООО Новая Компания",
            "slug": "ooo-novaya-kompaniya",
            "description": "Описание новой компании",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### GET `/api/organizations/{id}` - Получить организацию по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Organization retrieved successfully",
    "data": {
        "organization": {
            "id": 1,
            "name": "ООО Компания",
            "slug": "ooo-kompaniya",
            "description": "Описание компании",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "departments_count": 5,
            "users_count": 25
        }
    }
}
```

#### PUT `/api/organizations/{id}` - Обновить организацию

**Параметры запроса:**
```json
{
    "name": "ООО Обновленная Компания",
    "slug": "ooo-obnovlennaya-kompaniya",
    "description": "Обновленное описание",
    "is_active": false
}
```

**Опциональные поля:**
- `name` (string, max:255) - Название организации
- `slug` (string, max:255, unique) - URL-слаг организации
- `description` (string) - Описание организации
- `is_active` (boolean) - Статус активности

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Organization updated successfully",
    "data": {
        "organization": {
            "id": 1,
            "name": "ООО Обновленная Компания",
            "slug": "ooo-obnovlennaya-kompaniya",
            "description": "Обновленное описание",
            "is_active": false,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/organizations/{id}` - Удалить организацию

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Organization deleted successfully",
    "data": null
}
```

### Дополнительные эндпоинты для организаций
- `GET /api/organizations/{organization}/departments` - Отделы организации
- `GET /api/organizations/{organization}/roles` - Роли организации  
- `GET /api/organizations/{organization}/users` - Пользователи организации

### Wazzup24 настройки для организаций
- `PUT /api/organizations/{organization}/wazzup24/settings` - Обновить настройки
- `POST /api/organizations/{organization}/wazzup24/test-connection` - Тест подключения
- `GET /api/organizations/{organization}/wazzup24/channels` - Получить каналы
- `POST /api/organizations/{organization}/wazzup24/setup-webhooks` - Настроить webhooks
- `GET /api/organizations/{organization}/wazzup24/clients` - Получить клиентов
- `POST /api/organizations/{organization}/wazzup24/send-message` - Отправить сообщение

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

### Получение списка организаций
```bash
curl -X GET https://back-chat.ap.kz/api/organizations \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание новой организации
```bash
curl -X POST https://back-chat.ap.kz/api/organizations \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "ООО Новая Компания",
    "description": "Описание компании",
    "address": "г. Алматы, ул. Абая 1",
    "phone": "+7 727 123 45 67",
    "email": "info@newcompany.kz"
  }'
```

### Получение отделов организации
```bash
curl -X GET https://back-chat.ap.kz/api/organizations/1/departments \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Тест подключения Wazzup24
```bash
curl -X POST https://back-chat.ap.kz/api/organizations/1/wazzup24/test-connection \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Подробная документация по каждому эндпоинту находится в файле `API_ORGANIZATION.md`
