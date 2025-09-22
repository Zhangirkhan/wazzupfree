# API Эндпоинты - Сотрудники (Пользователи)

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

## Сотрудники (Пользователи)

### Основные эндпоинты

#### GET `/api/users` - Получить список сотрудников

**Параметры запроса:**
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 20)
- `search` (string, optional) - Поиск по имени или email
- `role` (string, optional) - Фильтр по роли (admin, manager, employee, user)
- `department_id` (integer, optional) - Фильтр по отделу
- `organization_id` (integer, optional) - Фильтр по организации

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Users retrieved successfully",
    "data": {
        "users": [
            {
                "id": 1,
                "name": "Иван Иванов",
                "email": "ivan@company.kz",
                "phone": "+7 777 123 45 67",
                "position": "Разработчик",
                "avatar": "https://example.com/avatars/ivan.jpg",
                "role": "employee",
                "department_id": 1,
                "department": {
                    "id": 1,
                    "name": "IT отдел",
                    "slug": "it-otdel",
                    "description": "Отдел информационных технологий",
                    "organization_id": 1,
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                },
                "organizations": [
                    {
                        "id": 1,
                        "name": "ООО Компания",
                        "slug": "ooo-kompaniya",
                        "description": "Описание компании",
                        "is_active": true,
                        "created_at": "2024-01-01T12:00:00.000000Z",
                        "updated_at": "2024-01-01T12:00:00.000000Z"
                    }
                ],
                "is_active": true,
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

#### POST `/api/users` - Создать сотрудника

**Параметры запроса:**
```json
{
    "name": "Петр Петров",
    "email": "petr@company.kz",
    "password": "password123",
    "phone": "+7 777 987 65 43",
    "position": "Менеджер",
    "role": "employee",
    "department_id": 2,
    "organization_ids": [1, 2]
}
```

**Обязательные поля:**
- `name` (string, max:255) - Имя сотрудника
- `email` (string, email, unique) - Email адрес
- `password` (string, min:8) - Пароль
- `role` (string, in:admin,manager,employee,user) - Роль сотрудника

**Опциональные поля:**
- `phone` (string, max:20) - Телефон
- `position` (string, max:255) - Должность
- `department_id` (integer, exists:departments) - ID отдела
- `organization_ids` (array) - Массив ID организаций
- `organization_ids.*` (integer, exists:organizations) - ID организации

**Ответ (201):**
```json
{
    "status": "success",
    "message": "User created successfully",
    "data": {
        "user": {
            "id": 2,
            "name": "Петр Петров",
            "email": "petr@company.kz",
            "phone": "+7 777 987 65 43",
            "position": "Менеджер",
            "avatar": null,
            "role": "employee",
            "department_id": 2,
            "department": {
                "id": 2,
                "name": "Отдел продаж",
                "slug": "otdel-prodazh",
                "description": "Отдел продаж",
                "organization_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "organizations": [
                {
                    "id": 1,
                    "name": "ООО Компания",
                    "slug": "ooo-kompaniya",
                    "description": "Описание компании",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### GET `/api/users/{id}` - Получить сотрудника по ID

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@company.kz",
            "phone": "+7 777 123 45 67",
            "position": "Разработчик",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "role": "employee",
            "department_id": 1,
            "department": {
                "id": 1,
                "name": "IT отдел",
                "slug": "it-otdel",
                "description": "Отдел информационных технологий",
                "organization_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "organizations": [
                {
                    "id": 1,
                    "name": "ООО Компания",
                    "slug": "ooo-kompaniya",
                    "description": "Описание компании",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

#### PUT `/api/users/{id}` - Обновить сотрудника

**Параметры запроса:**
```json
{
    "name": "Иван Петров",
    "phone": "+7 777 555 44 33",
    "position": "Старший разработчик",
    "role": "manager",
    "department_id": 1,
    "organization_ids": [1, 3]
}
```

**Опциональные поля:**
- `name` (string, max:255) - Имя сотрудника
- `phone` (string, max:20) - Телефон
- `position` (string, max:255) - Должность
- `role` (string, in:admin,manager,employee,user) - Роль сотрудника
- `department_id` (integer, exists:departments) - ID отдела
- `organization_ids` (array) - Массив ID организаций
- `organization_ids.*` (integer, exists:organizations) - ID организации

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User updated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Петров",
            "email": "ivan@company.kz",
            "phone": "+7 777 555 44 33",
            "position": "Старший разработчик",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "role": "manager",
            "department_id": 1,
            "department": {
                "id": 1,
                "name": "IT отдел",
                "slug": "it-otdel",
                "description": "Отдел информационных технологий",
                "organization_id": 1,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            },
            "organizations": [
                {
                    "id": 1,
                    "name": "ООО Компания",
                    "slug": "ooo-kompaniya",
                    "description": "Описание компании",
                    "is_active": true,
                    "created_at": "2024-01-01T12:00:00.000000Z",
                    "updated_at": "2024-01-01T12:00:00.000000Z"
                }
            ],
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### DELETE `/api/users/{id}` - Удалить сотрудника

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User deleted successfully",
    "data": null
}
```

### Дополнительные эндпоинты для сотрудников

#### PUT `/api/users/{id}/password` - Изменить пароль сотрудника

**Параметры запроса:**
```json
{
    "password": "newpassword123"
}
```

**Обязательные поля:**
- `password` (string, min:8) - Новый пароль

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Password changed successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@company.kz",
            "phone": "+7 777 123 45 67",
            "position": "Разработчик",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "role": "employee",
            "department_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### PUT `/api/users/{id}/activate` - Активировать сотрудника

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User activated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@company.kz",
            "phone": "+7 777 123 45 67",
            "position": "Разработчик",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "role": "employee",
            "department_id": 1,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### PUT `/api/users/{id}/deactivate` - Деактивировать сотрудника

**Ответ (200):**
```json
{
    "status": "success",
    "message": "User deactivated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@company.kz",
            "phone": "+7 777 123 45 67",
            "position": "Разработчик",
            "avatar": "https://example.com/avatars/ivan.jpg",
            "role": "employee",
            "department_id": 1,
            "is_active": false,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

#### GET `/api/users/roles` - Получить список ролей

**Ответ (200):**
```json
{
    "status": "success",
    "message": "Roles retrieved successfully",
    "data": [
        {
            "value": "admin",
            "label": "Администратор",
            "description": "Полный доступ к системе"
        },
        {
            "value": "manager",
            "label": "Менеджер",
            "description": "Управление отделом и клиентами"
        },
        {
            "value": "employee",
            "label": "Сотрудник",
            "description": "Работа с клиентами и сообщениями"
        },
        {
            "value": "user",
            "label": "Пользователь",
            "description": "Базовый доступ"
        }
    ]
}
```

---

## Роли сотрудников

### Доступные роли:
- `admin` - Администратор (полный доступ к системе)
- `manager` - Менеджер (управление отделом и клиентами)
- `employee` - Сотрудник (работа с клиентами и сообщениями)
- `user` - Пользователь (базовый доступ)

### Разрешения по ролям:

#### Администратор (admin)
- `dashboard` - Панель управления
- `users` - Управление пользователями
- `departments` - Управление отделами
- `chats` - Управление чатами
- `organizations` - Управление организациями
- `positions` - Управление должностями
- `clients` - Управление клиентами
- `settings` - Настройки системы

#### Менеджер (manager)
- `dashboard` - Панель управления
- `clients` - Управление клиентами
- `messenger` - Мессенджер

#### Сотрудник (employee)
- `dashboard` - Панель управления
- `clients` - Работа с клиентами
- `messenger` - Мессенджер

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

### Получение списка сотрудников
```bash
curl -X GET https://back-chat.ap.kz/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Поиск сотрудников по роли
```bash
curl -X GET "https://back-chat.ap.kz/api/users?role=employee" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Фильтрация по отделу
```bash
curl -X GET "https://back-chat.ap.kz/api/users?department_id=1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Создание нового сотрудника
```bash
curl -X POST https://back-chat.ap.kz/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Петр Петров",
    "email": "petr@company.kz",
    "password": "password123",
    "phone": "+7 777 987 65 43",
    "position": "Менеджер",
    "role": "employee",
    "department_id": 2,
    "organization_ids": [1]
  }'
```

### Обновление сотрудника
```bash
curl -X PUT https://back-chat.ap.kz/api/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Иван Петров",
    "phone": "+7 777 555 44 33",
    "position": "Старший разработчик",
    "role": "manager"
  }'
```

### Изменение пароля
```bash
curl -X PUT https://back-chat.ap.kz/api/users/1/password \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "password": "newpassword123"
  }'
```

### Активация сотрудника
```bash
curl -X PUT https://back-chat.ap.kz/api/users/1/activate \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Деактивация сотрудника
```bash
curl -X PUT https://back-chat.ap.kz/api/users/1/deactivate \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Удаление сотрудника
```bash
curl -X DELETE https://back-chat.ap.kz/api/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Получение списка ролей
```bash
curl -X GET https://back-chat.ap.kz/api/users/roles \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примечания

- Все эндпоинты требуют аутентификации через Bearer Token
- Все ответы возвращаются в формате JSON
- Для создания и обновления ресурсов используется Content-Type: application/json
- Сотрудник может принадлежать нескольким организациям
- При создании сотрудника обязательно указывать роль
- Email должен быть уникальным в системе
- Пароль не возвращается в ответах API
- В ответах включена информация об отделе и организациях (если загружены)
