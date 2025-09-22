# API Эндпоинты - Шаблоны

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

## Шаблоны

### Основные эндпоинты

#### GET `/api/templates` - Получить список шаблонов

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по названию или содержимому
- `type` (string, optional) - Фильтр по типу (message, email, sms, notification)
- `category` (string, optional) - Фильтр по категории (greeting, farewell, support, sales, technical, general)
- `language` (string, optional) - Фильтр по языку (ru, en, kk)
- `is_active` (boolean, optional) - Фильтр по активности
- `is_system` (boolean, optional) - Фильтр по типу шаблона (системный/пользовательский)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Templates retrieved successfully",
    "data": {
        "templates": [
            {
                "id": 1,
                "name": "Приветствие клиента",
                "content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию. Мы рады помочь вам с {{service_type}}.",
                "type": "message",
                "category": "greeting",
                "variables": {
                    "client_name": "Имя клиента",
                    "service_type": "Тип услуги"
                },
                "language": "ru",
                "is_active": true,
                "is_system": false,
                "usage_count": 15,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "creator": {
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
                "organization": {
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
            },
            {
                "id": 2,
                "name": "System Welcome Template",
                "content": "Welcome to our service, {{client_name}}! We are here to help you with {{service_type}}.",
                "type": "message",
                "category": "greeting",
                "variables": {
                    "client_name": "Client name",
                    "service_type": "Service type"
                },
                "language": "en",
                "is_active": true,
                "is_system": true,
                "usage_count": 45,
                "created_at": "2024-01-01T10:00:00.000000Z",
                "updated_at": "2024-01-01T10:00:00.000000Z",
                "creator": {
                    "id": 1,
                    "name": "System Admin",
                    "email": "admin@system.kz",
                    "phone": "+7 777 000 00 00",
                    "position": "Системный администратор",
                    "avatar": "https://example.com/avatars/admin.jpg",
                    "role": "admin",
                    "department_id": null,
                    "is_active": true,
                    "created_at": "2024-01-01T10:00:00.000000Z",
                    "updated_at": "2024-01-01T10:00:00.000000Z"
                },
                "organization": null
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

#### POST `/api/templates` - Создать шаблон

**Параметры запроса:**
```json
{
    "name": "Прощание с клиентом",
    "content": "Спасибо за обращение, {{client_name}}! Если у вас возникнут вопросы, обращайтесь к нам. Хорошего дня!",
    "type": "message",
    "category": "farewell",
    "variables": {
        "client_name": "Имя клиента"
    },
    "language": "ru",
    "is_active": true,
    "organization_id": 1
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название шаблона
- `content` (string) - Содержимое шаблона
- `type` (string, in:message,email,sms,notification) - Тип шаблона
- `category` (string, in:greeting,farewell,support,sales,technical,general) - Категория
- `language` (string, in:ru,en,kk) - Язык шаблона

**Опциональные поля:**
- `variables` (array) - Переменные для подстановки
- `is_active` (boolean) - Активность шаблона
- `organization_id` (integer, exists:organizations) - ID организации

**Ответ (201):**
```json
{
    "status": "success",
    "message": "Template created successfully",
    "data": {
        "template": {
            "id": 3,
            "name": "Прощание с клиентом",
            "content": "Спасибо за обращение, {{client_name}}! Если у вас возникнут вопросы, обращайтесь к нам. Хорошего дня!",
            "type": "message",
            "category": "farewell",
            "variables": {
                "client_name": "Имя клиента"
            },
            "language": "ru",
            "is_active": true,
            "is_system": false,
            "usage_count": 0,
            "created_at": "2024-01-01T14:00:00.000000Z",
            "updated_at": "2024-01-01T14:00:00.000000Z",
            "creator": {
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
            "organization": {
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
        }
    }
}
```

#### GET `/api/templates/{id}` - Получить шаблон по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template retrieved successfully",
    "data": {
        "template": {
            "id": 1,
            "name": "Приветствие клиента",
            "content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию. Мы рады помочь вам с {{service_type}}.",
            "type": "message",
            "category": "greeting",
            "variables": {
                "client_name": "Имя клиента",
                "service_type": "Тип услуги"
            },
            "language": "ru",
            "is_active": true,
            "is_system": false,
            "usage_count": 15,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "creator": {
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
            "organization": {
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
        }
    }
}
```

#### PUT `/api/templates/{id}` - Обновить шаблон

**Параметры запроса:**
```json
{
    "name": "Обновленное приветствие клиента",
    "content": "Добро пожаловать, {{client_name}}! Мы готовы помочь вам с {{service_type}}. Обращайтесь в любое время!",
    "variables": {
        "client_name": "Имя клиента",
        "service_type": "Тип услуги",
        "contact_phone": "Телефон для связи"
    },
    "is_active": true
}
```

**Опциональные поля:**
- `name` (string, max:255) - Название шаблона
- `content` (string) - Содержимое шаблона
- `type` (string, in:message,email,sms,notification) - Тип шаблона
- `category` (string, in:greeting,farewell,support,sales,technical,general) - Категория
- `variables` (array) - Переменные для подстановки
- `language` (string, in:ru,en,kk) - Язык шаблона
- `is_active` (boolean) - Активность шаблона

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template updated successfully",
    "data": {
        "template": {
            "id": 1,
            "name": "Обновленное приветствие клиента",
            "content": "Добро пожаловать, {{client_name}}! Мы готовы помочь вам с {{service_type}}. Обращайтесь в любое время!",
            "type": "message",
            "category": "greeting",
            "variables": {
                "client_name": "Имя клиента",
                "service_type": "Тип услуги",
                "contact_phone": "Телефон для связи"
            },
            "language": "ru",
            "is_active": true,
            "is_system": false,
            "usage_count": 15,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T15:00:00.000000Z",
            "creator": {
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
            "organization": {
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
        }
    }
}
```

#### DELETE `/api/templates/{id}` - Удалить шаблон

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template deleted successfully",
    "data": null
}
```

---

## Специализированные эндпоинты

#### GET `/api/templates/type/{type}` - Получить шаблоны по типу

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Templates of type message retrieved successfully",
    "data": {
        "templates": [
            {
                "id": 1,
                "name": "Приветствие клиента",
                "content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию.",
                "type": "message",
                "category": "greeting",
                "variables": {
                    "client_name": "Имя клиента"
                },
                "language": "ru",
                "is_active": true,
                "is_system": false,
                "usage_count": 15,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "creator": {
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
                "organization": {
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

#### GET `/api/templates/category/{category}` - Получить шаблоны по категории

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Templates of category greeting retrieved successfully",
    "data": {
        "templates": [
            {
                "id": 1,
                "name": "Приветствие клиента",
                "content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию.",
                "type": "message",
                "category": "greeting",
                "variables": {
                    "client_name": "Имя клиента"
                },
                "language": "ru",
                "is_active": true,
                "is_system": false,
                "usage_count": 15,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "creator": {
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
                "organization": {
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

#### POST `/api/templates/{id}/process` - Обработать шаблон с переменными

**Параметры запроса:**
```json
{
    "variables": {
        "client_name": "Иван Иванов",
        "service_type": "техническая поддержка",
        "contact_phone": "+7 777 123 45 67"
    }
}
```

**Опциональные поля:**
- `variables` (array) - Переменные для подстановки в шаблон

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template processed successfully",
    "data": {
        "template_id": 1,
        "template_name": "Приветствие клиента",
        "original_content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию. Мы рады помочь вам с {{service_type}}.",
        "processed_content": "Здравствуйте, Иван Иванов! Добро пожаловать в нашу компанию. Мы рады помочь вам с техническая поддержка.",
        "variables_used": {
            "client_name": "Иван Иванов",
            "service_type": "техническая поддержка",
            "contact_phone": "+7 777 123 45 67"
        }
    }
}
```

#### GET `/api/templates/stats` - Получить статистику шаблонов

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template statistics retrieved successfully",
    "data": {
        "total_templates": 25,
        "active_templates": 20,
        "system_templates": 5,
        "user_templates": 20,
        "by_type": {
            "message": 15,
            "email": 6,
            "sms": 3,
            "notification": 1
        },
        "by_category": {
            "greeting": 8,
            "farewell": 5,
            "support": 7,
            "sales": 3,
            "technical": 2,
            "general": 0
        },
        "most_used": [
            {
                "id": 1,
                "name": "Приветствие клиента",
                "usage_count": 45
            },
            {
                "id": 2,
                "name": "System Welcome Template",
                "usage_count": 32
            },
            {
                "id": 3,
                "name": "Прощание с клиентом",
                "usage_count": 28
            },
            {
                "id": 4,
                "name": "Техническая поддержка",
                "usage_count": 15
            },
            {
                "id": 5,
                "name": "Продажи",
                "usage_count": 12
            }
        ]
    }
}
```

#### GET `/api/templates/options` - Получить доступные опции

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Template options retrieved successfully",
    "data": {
        "types": [
            "message",
            "email",
            "sms",
            "notification"
        ],
        "categories": [
            "greeting",
            "farewell",
            "support",
            "sales",
            "technical",
            "general"
        ],
        "languages": [
            "ru",
            "en",
            "kk"
        ]
    }
}
```

---

## Типы шаблонов

- `message` - Сообщения в чате
- `email` - Email письма
- `sms` - SMS сообщения
- `notification` - Уведомления

## Категории шаблонов

- `greeting` - Приветствие
- `farewell` - Прощание
- `support` - Техническая поддержка
- `sales` - Продажи
- `technical` - Технические вопросы
- `general` - Общие

## Поддерживаемые языки

- `ru` - Русский
- `en` - Английский
- `kk` - Казахский

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

### Получение списка шаблонов
```bash
curl -X GET https://back-chat.ap.kz/api/templates \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение шаблонов по типу
```bash
curl -X GET https://back-chat.ap.kz/api/templates/type/message \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение шаблонов по категории
```bash
curl -X GET https://back-chat.ap.kz/api/templates/category/greeting \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Поиск шаблонов
```bash
curl -X GET "https://back-chat.ap.kz/api/templates?search=приветствие&type=message" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание шаблона
```bash
curl -X POST https://back-chat.ap.kz/api/templates \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Приветствие клиента",
    "content": "Здравствуйте, {{client_name}}! Добро пожаловать в нашу компанию.",
    "type": "message",
    "category": "greeting",
    "variables": {
      "client_name": "Имя клиента"
    },
    "language": "ru",
    "is_active": true
  }'
```

### Обновление шаблона
```bash
curl -X PUT https://back-chat.ap.kz/api/templates/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Обновленное приветствие",
    "content": "Добро пожаловать, {{client_name}}! Мы готовы помочь вам.",
    "is_active": true
  }'
```

### Обработка шаблона
```bash
curl -X POST https://back-chat.ap.kz/api/templates/1/process \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "variables": {
      "client_name": "Иван Иванов",
      "service_type": "техническая поддержка"
    }
  }'
```

### Получение статистики
```bash
curl -X GET https://back-chat.ap.kz/api/templates/stats \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение опций
```bash
curl -X GET https://back-chat.ap.kz/api/templates/options \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Удаление шаблона
```bash
curl -X DELETE https://back-chat.ap.kz/api/templates/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Пользователи видят только шаблоны своей организации + системные шаблоны
- Системные шаблоны могут редактировать только администраторы
- Системные шаблоны нельзя удалять
- При обработке шаблона увеличивается счетчик использования
- Переменные в шаблонах указываются в формате `{{variable_name}}`
- Поддерживается многоязычность шаблонов
- Шаблоны сортируются по популярности (usage_count) и дате создания
