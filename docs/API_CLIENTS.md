# API Эндпоинты - Клиенты

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

## Клиенты

### Основные эндпоинты

#### GET `/api/clients` - Получить список клиентов

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по имени или телефону
- `contractor_id` (integer, optional) - Фильтр по контрагенту
- `is_individual` (boolean, optional) - Фильтр по типу (true - физ.лица, false - юр.лица)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Clients retrieved successfully",
    "data": {
        "clients": [
            {
                "id": 1,
                "name": "Иван Иванов",
                "phone": "+7 777 123 45 67",
                "email": "ivan@example.com",
                "uuid_wazzup": "wazzup-uuid-123",
                "comment": "Клиент компании",
                "avatar": "https://example.com/avatars/ivan.jpg",
                "is_active": true,
                "contractor_id": 1,
                "contractor": {
                    "id": 1,
                    "name": "ООО Рога и Копыта",
                    "type": "legal",
                    "inn": "123456789012",
                    "kpp": "123456789",
                    "ogrn": "1234567890123",
                    "legal_address": "г. Алматы, ул. Абая, 1",
                    "actual_address": "г. Алматы, ул. Абая, 1",
                    "phone": "+7 727 123 45 67",
                    "email": "info@roga-kopyta.kz",
                    "website": "https://roga-kopyta.kz",
                    "contact_person": "Петр Петров",
                    "contact_phone": "+7 777 987 65 43",
                    "contact_email": "petr@roga-kopyta.kz",
                    "bank_name": "АО Казкоммерцбанк",
                    "bank_account": "12345678901234567890",
                    "bik": "123456789",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            {
                "id": 2,
                "name": "Анна Смирнова",
                "phone": "+7 777 555 44 33",
                "email": "anna@example.com",
                "uuid_wazzup": "wazzup-uuid-456",
                "comment": "Частный клиент",
                "avatar": "https://example.com/avatars/anna.jpg",
                "is_active": true,
                "contractor_id": null,
                "contractor": null,
                "created_at": "2024-01-01T13:00:00.000000Z",
                "updated_at": "2024-01-01T13:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 20,
            "total": 2
        }
    }
}
```

#### POST `/api/clients` - Создать клиента

**Параметры запроса:**
```json
{
    "name": "Петр Петров",
    "phone": "+7 777 987 65 43",
    "email": "petr@example.com",
    "uuid_wazzup": "wazzup-uuid-789",
    "comment": "Новый клиент",
    "avatar": "https://example.com/avatars/petr.jpg",
    "contractor_id": 1,
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Имя клиента
- `phone` (string, max:20) - Телефон

**Опциональные поля:**
- `email` (string, email) - Email
- `uuid_wazzup` (string) - UUID в системе Wazzup
- `comment` (string) - Комментарий
- `avatar` (string, url) - URL аватара
- `contractor_id` (integer, exists:contractors) - ID контрагента
- `is_active` (boolean) - Активность клиента

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Client created successfully",
    "data": {
        "client": {
            "id": 3,
            "name": "Петр Петров",
            "phone": "+7 777 987 65 43",
            "email": "petr@example.com",
            "uuid_wazzup": "wazzup-uuid-789",
            "comment": "Новый клиент",
            "avatar": "https://example.com/avatars/petr.jpg",
            "is_active": true,
            "contractor_id": 1,
            "contractor": {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая, 1",
                "actual_address": "г. Алматы, ул. Абая, 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "contact_person": "Петр Петров",
                "contact_phone": "+7 777 987 65 43",
                "contact_email": "petr@roga-kopyta.kz",
                "bank_name": "АО Казкоммерцбанк",
                "bank_account": "12345678901234567890",
                "bik": "123456789",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "created_at": "2024-01-01T14:00:00.000000Z",
            "updated_at": "2024-01-01T14:00:00.000000Z"
        }
    }
}
```

#### GET `/api/clients/{id}` - Получить клиента по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client retrieved successfully",
    "data": {
        "client": {
            "id": 1,
            "name": "Иван Иванов",
            "phone": "+7 777 123 45 67",
            "email": "ivan@example.com",
            "uuid_wazzup": "wazzup-uuid-123",
            "comment": "Клиент компании",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "is_active": true,
            "contractor_id": 1,
            "contractor": {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая, 1",
                "actual_address": "г. Алматы, ул. Абая, 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "contact_person": "Петр Петров",
                "contact_phone": "+7 777 987 65 43",
                "contact_email": "petr@roga-kopyta.kz",
                "bank_name": "АО Казкоммерцбанк",
                "bank_account": "12345678901234567890",
                "bik": "123456789",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### PUT `/api/clients/{id}` - Обновить клиента

**Параметры запроса:**
```json
{
    "name": "Иван Иванович Иванов",
    "phone": "+7 777 123 45 67",
    "email": "ivan.ivanov@example.com",
    "comment": "Обновленный комментарий",
    "is_active": true
}
```

**Опциональные поля:**
- `name` (string, max:255) - Имя клиента
- `phone` (string, max:20) - Телефон
- `email` (string, email) - Email
- `uuid_wazzup` (string) - UUID в системе Wazzup
- `comment` (string) - Комментарий
- `avatar` (string, url) - URL аватара
- `contractor_id` (integer, exists:contractors) - ID контрагента
- `is_active` (boolean) - Активность клиента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client updated successfully",
    "data": {
        "client": {
            "id": 1,
            "name": "Иван Иванович Иванов",
            "phone": "+7 777 123 45 67",
            "email": "ivan.ivanov@example.com",
            "uuid_wazzup": "wazzup-uuid-123",
            "comment": "Обновленный комментарий",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "is_active": true,
            "contractor_id": 1,
            "contractor": {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая, 1",
                "actual_address": "г. Алматы, ул. Абая, 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "contact_person": "Петр Петров",
                "contact_phone": "+7 777 987 65 43",
                "contact_email": "petr@roga-kopyta.kz",
                "bank_name": "АО Казкоммерцбанк",
                "bank_account": "12345678901234567890",
                "bik": "123456789",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T15:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/clients/{id}` - Удалить клиента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client deleted successfully",
    "data": null
}
```

---

## Специализированные эндпоинты

#### GET `/api/clients/individuals` - Получить физических лиц (без контрагентов)

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по имени или телефону

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Individual clients retrieved successfully",
    "data": {
        "clients": [
            {
                "id": 2,
                "name": "Анна Смирнова",
                "phone": "+7 777 555 44 33",
                "email": "anna@example.com",
                "uuid_wazzup": "wazzup-uuid-456",
                "comment": "Частный клиент",
                "avatar": "https://example.com/avatars/anna.jpg",
                "is_active": true,
                "contractor_id": null,
                "contractor": null,
                "created_at": "2024-01-01T13:00:00.000000Z",
                "updated_at": "2024-01-01T13:00:00.000000Z"
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

#### GET `/api/clients/corporate` - Получить юридических лиц (с контрагентами)

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по имени или телефону

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Corporate clients retrieved successfully",
    "data": {
        "clients": [
            {
                "id": 1,
                "name": "Иван Иванов",
                "phone": "+7 777 123 45 67",
                "email": "ivan@example.com",
                "uuid_wazzup": "wazzup-uuid-123",
                "comment": "Клиент компании",
                "avatar": "https://example.com/avatars/ivan.jpg",
                "is_active": true,
                "contractor_id": 1,
                "contractor": {
                    "id": 1,
                    "name": "ООО Рога и Копыта",
                    "type": "legal",
                    "inn": "123456789012",
                    "kpp": "123456789",
                    "ogrn": "1234567890123",
                    "legal_address": "г. Алматы, ул. Абая, 1",
                    "actual_address": "г. Алматы, ул. Абая, 1",
                    "phone": "+7 727 123 45 67",
                    "email": "info@roga-kopyta.kz",
                    "website": "https://roga-kopyta.kz",
                    "contact_person": "Петр Петров",
                    "contact_phone": "+7 777 987 65 43",
                    "contact_email": "petr@roga-kopyta.kz",
                    "bank_name": "АО Казкоммерцбанк",
                    "bank_account": "12345678901234567890",
                    "bik": "123456789",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
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

---

## Управление связями с контрагентами

#### POST `/api/clients/{id}/attach-contractor` - Привязать клиента к контрагенту

**Параметры запроса:**
```json
{
    "contractor_id": 1
}
```

**Обязательные поля:**
- `contractor_id` (integer, exists:contractors) - ID контрагента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client attached to contractor successfully",
    "data": {
        "client": {
            "id": 2,
            "name": "Анна Смирнова",
            "phone": "+7 777 555 44 33",
            "email": "anna@example.com",
            "uuid_wazzup": "wazzup-uuid-456",
            "comment": "Частный клиент",
            "avatar": "https://example.com/avatars/anna.jpg",
            "is_active": true,
            "contractor_id": 1,
            "contractor": {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая, 1",
                "actual_address": "г. Алматы, ул. Абая, 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "contact_person": "Петр Петров",
                "contact_phone": "+7 777 987 65 43",
                "contact_email": "petr@roga-kopyta.kz",
                "bank_name": "АО Казкоммерцбанк",
                "bank_account": "12345678901234567890",
                "bik": "123456789",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "created_at": "2024-01-01T13:00:00.000000Z",
            "updated_at": "2024-01-01T16:00:00.000000Z"
        }
    }
}
```

#### POST `/api/clients/{id}/detach-contractor` - Отвязать клиента от контрагента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client detached from contractor successfully",
    "data": {
        "client": {
            "id": 2,
            "name": "Анна Смирнова",
            "phone": "+7 777 555 44 33",
            "email": "anna@example.com",
            "uuid_wazzup": "wazzup-uuid-456",
            "comment": "Частный клиент",
            "avatar": "https://example.com/avatars/anna.jpg",
            "is_active": true,
            "contractor_id": null,
            "contractor": null,
            "created_at": "2024-01-01T13:00:00.000000Z",
            "updated_at": "2024-01-01T17:00:00.000000Z"
        }
    }
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
| 500 | Внутренняя ошибка сервера |

---

## Примеры использования

### Получение списка клиентов
```bash
curl -X GET https://back-chat.ap.kz/api/clients \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение физических лиц
```bash
curl -X GET https://back-chat.ap.kz/api/clients/individuals \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение юридических лиц
```bash
curl -X GET https://back-chat.ap.kz/api/clients/corporate \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Поиск клиентов
```bash
curl -X GET "https://back-chat.ap.kz/api/clients?search=Иван&is_individual=true" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание клиента
```bash
curl -X POST https://back-chat.ap.kz/api/clients \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Петр Петров",
    "phone": "+7 777 987 65 43",
    "email": "petr@example.com",
    "comment": "Новый клиент",
    "is_active": true
  }'
```

### Обновление клиента
```bash
curl -X PUT https://back-chat.ap.kz/api/clients/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Иван Иванович Иванов",
    "email": "ivan.ivanov@example.com",
    "comment": "Обновленный комментарий"
  }'
```

### Привязка к контрагенту
```bash
curl -X POST https://back-chat.ap.kz/api/clients/2/attach-contractor \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "contractor_id": 1
  }'
```

### Отвязка от контрагента
```bash
curl -X POST https://back-chat.ap.kz/api/clients/2/detach-contractor \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Удаление клиента
```bash
curl -X DELETE https://back-chat.ap.kz/api/clients/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Клиенты могут быть как физическими лицами (без контрагента), так и юридическими лицами (с контрагентом)
- Поиск работает по имени и телефону клиента
- Фильтрация по типу клиента доступна через параметр `is_individual`
- Связь с контрагентами управляется через отдельные эндпоинты
