# API Эндпоинты - Отделы

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

## Отделы

### Основные эндпоинты

#### GET `/api/departments` - Получить список отделов

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по названию отдела
- `organization_id` (integer, optional) - Фильтр по организации

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Departments retrieved successfully",
    "data": {
        "departments": [
            {
                "id": 1,
                "name": "IT отдел",
                "slug": "it-otdel",
                "description": "Отдел информационных технологий",
                "organization_id": 1,
                "organization": {
                    "id": 1,
                    "name": "ООО Компания",
                    "slug": "ooo-kompaniya",
                    "description": "Описание компании",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "users_count": 8
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

#### POST `/api/departments` - Создать отдел

**Параметры запроса:**
```json
{
    "name": "HR отдел",
    "slug": "hr-otdel",
    "description": "Отдел кадров",
    "organization_id": 1,
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название отдела
- `organization_id` (integer, exists:organizations) - ID организации

**Опциональные поля:**
- `slug` (string, max:255, unique) - URL-слаг отдела
- `description` (string) - Описание отдела
- `is_active` (boolean) - Статус активности

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Department created successfully",
    "data": {
        "department": {
            "id": 2,
            "name": "HR отдел",
            "slug": "hr-otdel",
            "description": "Отдел кадров",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания",
                "slug": "ooo-kompaniya",
                "description": "Описание компании",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### GET `/api/departments/{id}` - Получить отдел по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Department retrieved successfully",
    "data": {
        "department": {
            "id": 1,
            "name": "IT отдел",
            "slug": "it-otdel",
            "description": "Отдел информационных технологий",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания",
                "slug": "ooo-kompaniya",
                "description": "Описание компании",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "users_count": 8
        }
    }
}
```

#### PUT `/api/departments/{id}` - Обновить отдел

**Параметры запроса:**
```json
{
    "name": "IT и Разработка",
    "slug": "it-i-razrabotka",
    "description": "Отдел информационных технологий и разработки",
    "organization_id": 1,
    "is_active": false
}
```

**Опциональные поля:**
- `name` (string, max:255) - Название отдела
- `slug` (string, max:255, unique) - URL-слаг отдела
- `description` (string) - Описание отдела
- `organization_id` (integer, exists:organizations) - ID организации
- `is_active` (boolean) - Статус активности

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Department updated successfully",
    "data": {
        "department": {
            "id": 1,
            "name": "IT и Разработка",
            "slug": "it-i-razrabotka",
            "description": "Отдел информационных технологий и разработки",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания",
                "slug": "ooo-kompaniya",
                "description": "Описание компании",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "is_active": false,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/departments/{id}` - Удалить отдел

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Department deleted successfully",
    "data": null
}
```

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

### Получение списка отделов
```bash
curl -X GET https://back-chat.ap.kz/api/departments \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение отделов конкретной организации
```bash
curl -X GET "https://back-chat.ap.kz/api/departments?organization_id=1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание нового отдела
```bash
curl -X POST https://back-chat.ap.kz/api/departments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "HR отдел",
    "description": "Отдел кадров",
    "organization_id": 1
  }'
```

### Обновление отдела
```bash
curl -X PUT https://back-chat.ap.kz/api/departments/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "IT и Разработка",
    "description": "Отдел информационных технологий и разработки"
  }'
```

### Удаление отдела
```bash
curl -X DELETE https://back-chat.ap.kz/api/departments/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- При создании отдела обязательно указывать `organization_id`
- Отдел всегда принадлежит конкретной организации
- В ответах включена информация об организации (если загружена)
