# API Эндпоинты - Контрагенты

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

## Контрагенты

### Основные эндпоинты

#### GET `/api/contractors` - Получить список контрагентов

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по названию или ИНН
- `type` (string, optional) - Фильтр по типу (legal, individual)
- `organization_id` (integer, optional) - Фильтр по организации

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Contractors retrieved successfully",
    "data": {
        "contractors": [
            {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012",
                "kpp": "123456789",
                "ogrn": "1234567890123",
                "legal_address": "г. Алматы, ул. Абая 1",
                "actual_address": "г. Алматы, ул. Абая 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz",
                "website": "https://roga-kopyta.kz",
                "contact_person": "Иван Иванов",
                "contact_phone": "+7 777 123 45 67",
                "contact_email": "ivan@roga-kopyta.kz",
                "bank_name": "АО Банк ЦентрКредит",
                "bank_account": "KZ123456789012345678",
                "bik": "123456789",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "clients_count": 5
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

#### POST `/api/contractors` - Создать контрагента

**Параметры запроса:**
```json
{
    "name": "ООО Новая Компания",
    "type": "legal",
    "inn": "987654321098",
    "kpp": "987654321",
    "ogrn": "9876543210987",
    "legal_address": "г. Алматы, ул. Абая 2",
    "actual_address": "г. Алматы, ул. Абая 2",
    "phone": "+7 727 987 65 43",
    "email": "info@newcompany.kz",
    "website": "https://newcompany.kz",
    "contact_person": "Петр Петров",
    "contact_phone": "+7 777 987 65 43",
    "contact_email": "petr@newcompany.kz",
    "bank_name": "АО Народный Банк",
    "bank_account": "KZ987654321098765432",
    "bik": "987654321",
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название контрагента
- `type` (string, in:legal,individual) - Тип контрагента

**Опциональные поля для юридических лиц:**
- `inn` (string, max:12) - ИНН
- `kpp` (string, max:9) - КПП
- `ogrn` (string, max:13) - ОГРН
- `legal_address` (string, max:500) - Юридический адрес
- `actual_address` (string, max:500) - Фактический адрес
- `phone` (string, max:20) - Телефон
- `email` (string, email) - Email
- `website` (string, url) - Веб-сайт
- `contact_person` (string, max:255) - Контактное лицо
- `contact_phone` (string, max:20) - Телефон контактного лица
- `contact_email` (string, email) - Email контактного лица
- `bank_name` (string, max:255) - Название банка
- `bank_account` (string, max:20) - Банковский счет
- `bik` (string, max:9) - БИК
- `is_active` (boolean) - Статус активности

**Опциональные поля для физических лиц:**
- `inn` (string, max:12) - ИНН
- `passport_series` (string, max:4) - Серия паспорта
- `passport_number` (string, max:6) - Номер паспорта
- `passport_issued_by` (string, max:255) - Кем выдан паспорт
- `passport_issued_date` (date) - Дата выдачи паспорта
- `address` (string, max:500) - Адрес
- `phone` (string, max:20) - Телефон
- `email` (string, email) - Email
- `is_active` (boolean) - Статус активности

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Contractor created successfully",
    "data": {
        "contractor": {
            "id": 2,
            "name": "ООО Новая Компания",
            "type": "legal",
            "inn": "987654321098",
            "kpp": "987654321",
            "ogrn": "9876543210987",
            "legal_address": "г. Алматы, ул. Абая 2",
            "actual_address": "г. Алматы, ул. Абая 2",
            "phone": "+7 727 987 65 43",
            "email": "info@newcompany.kz",
            "website": "https://newcompany.kz",
            "contact_person": "Петр Петров",
            "contact_phone": "+7 777 987 65 43",
            "contact_email": "petr@newcompany.kz",
            "bank_name": "АО Народный Банк",
            "bank_account": "KZ987654321098765432",
            "bik": "987654321",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### GET `/api/contractors/{id}` - Получить контрагента по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Contractor retrieved successfully",
    "data": {
        "contractor": {
            "id": 1,
            "name": "ООО Рога и Копыта",
            "type": "legal",
            "inn": "123456789012",
            "kpp": "123456789",
            "ogrn": "1234567890123",
            "legal_address": "г. Алматы, ул. Абая 1",
            "actual_address": "г. Алматы, ул. Абая 1",
            "phone": "+7 727 123 45 67",
            "email": "info@roga-kopyta.kz",
            "website": "https://roga-kopyta.kz",
            "contact_person": "Иван Иванов",
            "contact_phone": "+7 777 123 45 67",
            "contact_email": "ivan@roga-kopyta.kz",
            "bank_name": "АО Банк ЦентрКредит",
            "bank_account": "KZ123456789012345678",
            "bik": "123456789",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "clients": [
                {
                    "id": 1,
                    "name": "Иван Иванов",
                    "phone": "+7 777 123 45 67",
                    "email": "ivan@roga-kopyta.kz",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z"
                }
            ]
        }
    }
}
```

#### PUT `/api/contractors/{id}` - Обновить контрагента

**Параметры запроса:**
```json
{
    "name": "ООО Обновленная Компания",
    "type": "legal",
    "inn": "111111111111",
    "kpp": "111111111",
    "ogrn": "1111111111111",
    "legal_address": "г. Алматы, ул. Абая 3",
    "actual_address": "г. Алматы, ул. Абая 3",
    "phone": "+7 727 555 44 33",
    "email": "info@updated.kz",
    "website": "https://updated.kz",
    "contact_person": "Сидор Сидоров",
    "contact_phone": "+7 777 555 44 33",
    "contact_email": "sidor@updated.kz",
    "bank_name": "АО Каспий Банк",
    "bank_account": "KZ111111111111111111",
    "bik": "111111111",
    "is_active": false
}
```

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Contractor updated successfully",
    "data": {
        "contractor": {
            "id": 1,
            "name": "ООО Обновленная Компания",
            "type": "legal",
            "inn": "111111111111",
            "kpp": "111111111",
            "ogrn": "1111111111111",
            "legal_address": "г. Алматы, ул. Абая 3",
            "actual_address": "г. Алматы, ул. Абая 3",
            "phone": "+7 727 555 44 33",
            "email": "info@updated.kz",
            "website": "https://updated.kz",
            "contact_person": "Сидор Сидоров",
            "contact_phone": "+7 777 555 44 33",
            "contact_email": "sidor@updated.kz",
            "bank_name": "АО Каспий Банк",
            "bank_account": "KZ111111111111111111",
            "bik": "111111111",
            "is_active": false,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/contractors/{id}` - Удалить контрагента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Contractor deleted successfully",
    "data": null
}
```

### Дополнительные эндпоинты для контрагентов

#### GET `/api/contractors/{id}/clients` - Получить клиентов контрагента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Contractor clients retrieved successfully",
    "data": {
        "clients": [
            {
                "id": 1,
                "name": "Иван Иванов",
                "phone": "+7 777 123 45 67",
                "email": "ivan@roga-kopyta.kz",
                "uuid_wazzup": "wazzup-uuid-123",
                "comment": "Контактное лицо компании",
                "avatar": "https://example.com/avatars/ivan.jpg",
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        ]
    }
}
```

#### POST `/api/contractors/{id}/clients` - Добавить клиента к контрагенту

**Параметры запроса:**
```json
{
    "client_id": 1
}
```

**Обязательные поля:**
- `client_id` (integer, exists:clients) - ID клиента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client added to contractor successfully",
    "data": {
        "contractor_client": {
            "contractor_id": 1,
            "client_id": 1,
            "created_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/contractors/{contractor_id}/clients/{client_id}` - Удалить клиента из контрагента

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client removed from contractor successfully",
    "data": null
}
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
                    "inn": "123456789012"
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

#### POST `/api/clients` - Создать клиента

**Параметры запроса:**
```json
{
    "name": "Петр Петров",
    "phone": "+7 777 987 65 43",
    "email": "petr@example.com",
    "comment": "Новый клиент",
    "contractor_id": 1,
    "is_active": true
}
```

**Обязательные поля:**
- `name` (string, max:255) - Имя клиента
- `phone` (string, max:20) - Телефон

**Опциональные поля:**
- `email` (string, email) - Email
- `uuid_wazzup` (string) - UUID в Wazzup
- `comment` (string) - Комментарий
- `avatar` (string, url) - Аватар
- `contractor_id` (integer, exists:contractors) - ID контрагента
- `is_active` (boolean) - Статус активности

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Client created successfully",
    "data": {
        "client": {
            "id": 2,
            "name": "Петр Петров",
            "phone": "+7 777 987 65 43",
            "email": "petr@example.com",
            "uuid_wazzup": null,
            "comment": "Новый клиент",
            "avatar": null,
            "is_active": true,
            "contractor_id": 1,
            "contractor": {
                "id": 1,
                "name": "ООО Рога и Копыта",
                "type": "legal",
                "inn": "123456789012"
            },
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
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
                "legal_address": "г. Алматы, ул. Абая 1",
                "phone": "+7 727 123 45 67",
                "email": "info@roga-kopyta.kz"
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
    "name": "Иван Петров",
    "phone": "+7 777 555 44 33",
    "email": "ivan.petrov@example.com",
    "comment": "Обновленный комментарий",
    "contractor_id": 2,
    "is_active": false
}
```

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Client updated successfully",
    "data": {
        "client": {
            "id": 1,
            "name": "Иван Петров",
            "phone": "+7 777 555 44 33",
            "email": "ivan.petrov@example.com",
            "uuid_wazzup": "wazzup-uuid-123",
            "comment": "Обновленный комментарий",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "is_active": false,
            "contractor_id": 2,
            "contractor": {
                "id": 2,
                "name": "ООО Новая Компания",
                "type": "legal",
                "inn": "987654321098"
            },
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
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

## Типы контрагентов

### Юридические лица (legal)
- Полные реквизиты организации
- ИНН, КПП, ОГРН
- Юридический и фактический адреса
- Банковские реквизиты
- Контактное лицо

### Физические лица (individual)
- ИНН (при наличии)
- Паспортные данные
- Адрес регистрации
- Контактная информация

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

### Получение списка контрагентов
```bash
curl -X GET https://back-chat.ap.kz/api/contractors \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание юридического лица
```bash
curl -X POST https://back-chat.ap.kz/api/contractors \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "ООО Новая Компания",
    "type": "legal",
    "inn": "987654321098",
    "kpp": "987654321",
    "ogrn": "9876543210987",
    "legal_address": "г. Алматы, ул. Абая 2",
    "phone": "+7 727 987 65 43",
    "email": "info@newcompany.kz",
    "contact_person": "Петр Петров",
    "contact_phone": "+7 777 987 65 43"
  }'
```

### Создание физического лица
```bash
curl -X POST https://back-chat.ap.kz/api/contractors \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Иванов Иван Иванович",
    "type": "individual",
    "inn": "123456789012",
    "passport_series": "1234",
    "passport_number": "567890",
    "passport_issued_by": "УВД г. Алматы",
    "passport_issued_date": "2020-01-01",
    "address": "г. Алматы, ул. Абая 1",
    "phone": "+7 777 123 45 67",
    "email": "ivan@example.com"
  }'
```

### Получение клиентов контрагента
```bash
curl -X GET https://back-chat.ap.kz/api/contractors/1/clients \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание клиента с привязкой к контрагенту
```bash
curl -X POST https://back-chat.ap.kz/api/clients \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Петр Петров",
    "phone": "+7 777 987 65 43",
    "email": "petr@example.com",
    "comment": "Контактное лицо",
    "contractor_id": 1
  }'
```

### Создание клиента без контрагента (физ.лицо)
```bash
curl -X POST https://back-chat.ap.kz/api/clients \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Сидор Сидоров",
    "phone": "+7 777 555 44 33",
    "email": "sidor@example.com",
    "comment": "Частный клиент"
  }'
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Контрагенты могут быть юридическими или физическими лицами
- Клиенты могут быть привязаны к контрагентам (юр.лица) или существовать независимо (физ.лица)
- При создании контрагента-юр.лица обязательно указывать ИНН
- При создании контрагента-физ.лица обязательно указывать паспортные данные
- Клиенты связаны с контрагентами через поле `contractor_id`
- В ответах включена информация о связанных сущностях (если загружены)
