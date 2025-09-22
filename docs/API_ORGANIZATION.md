# API Документация - Организационная структура

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

### GET `/api/organizations`

Получение списка всех организаций.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы для пагинации
- `per_page` (integer, optional) - Количество записей на странице (по умолчанию: 15)
- `search` (string, optional) - Поиск по названию организации

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Organizations retrieved successfully",
    "data": {
        "organizations": [
            {
                "id": 1,
                "name": "ООО Компания",
                "description": "Описание компании",
                "address": "г. Алматы, ул. Абая 1",
                "phone": "+7 727 123 45 67",
                "email": "info@company.kz",
                "website": "https://company.kz",
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
            "per_page": 15,
            "total": 1
        }
    }
}
```

---

### POST `/api/organizations`

Создание новой организации.

**Параметры запроса:**
```json
{
    "name": "ООО Новая Компания",
    "description": "Описание новой компании",
    "address": "г. Алматы, ул. Абая 1",
    "phone": "+7 727 123 45 67",
    "email": "info@newcompany.kz",
    "website": "https://newcompany.kz"
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название организации

**Опциональные поля:**
- `description` (string) - Описание организации
- `address` (string, max:500) - Адрес
- `phone` (string, max:20) - Телефон
- `email` (string, email) - Email
- `website` (string, url) - Веб-сайт

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "Organization created successfully",
    "data": {
        "organization": {
            "id": 2,
            "name": "ООО Новая Компания",
            "description": "Описание новой компании",
            "address": "г. Алматы, ул. Абая 1",
            "phone": "+7 727 123 45 67",
            "email": "info@newcompany.kz",
            "website": "https://newcompany.kz",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

---

### GET `/api/organizations/{id}`

Получение информации о конкретной организации.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Organization retrieved successfully",
    "data": {
        "organization": {
            "id": 1,
            "name": "ООО Компания",
            "description": "Описание компании",
            "address": "г. Алматы, ул. Абая 1",
            "phone": "+7 727 123 45 67",
            "email": "info@company.kz",
            "website": "https://company.kz",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "departments": [
                {
                    "id": 1,
                    "name": "IT отдел",
                    "description": "Отдел информационных технологий"
                }
            ],
            "users": [
                {
                    "id": 1,
                    "name": "Иван Иванов",
                    "email": "ivan@company.kz",
                    "position": "Менеджер"
                }
            ]
        }
    }
}
```

---

### PUT `/api/organizations/{id}`

Обновление информации об организации.

**Параметры запроса:**
```json
{
    "name": "ООО Обновленная Компания",
    "description": "Обновленное описание",
    "address": "г. Алматы, ул. Абая 2",
    "phone": "+7 727 987 65 43",
    "email": "info@updated.kz",
    "website": "https://updated.kz"
}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Organization updated successfully",
    "data": {
        "organization": {
            "id": 1,
            "name": "ООО Обновленная Компания",
            "description": "Обновленное описание",
            "address": "г. Алматы, ул. Абая 2",
            "phone": "+7 727 987 65 43",
            "email": "info@updated.kz",
            "website": "https://updated.kz",
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

---

### DELETE `/api/organizations/{id}`

Удаление организации.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Organization deleted successfully",
    "data": null
}
```

---

## Отделы

### GET `/api/departments`

Получение списка всех отделов.

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы
- `per_page` (integer, optional) - Количество записей на странице
- `search` (string, optional) - Поиск по названию отдела
- `organization_id` (integer, optional) - Фильтр по организации

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Departments retrieved successfully",
    "data": {
        "departments": [
            {
                "id": 1,
                "name": "IT отдел",
                "description": "Отдел информационных технологий",
                "organization_id": 1,
                "organization": {
                    "id": 1,
                    "name": "ООО Компания"
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
            "per_page": 15,
            "total": 1
        }
    }
}
```

---

### POST `/api/departments`

Создание нового отдела.

**Параметры запроса:**
```json
{
    "name": "HR отдел",
    "description": "Отдел кадров",
    "organization_id": 1
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название отдела
- `organization_id` (integer, exists:organizations) - ID организации

**Опциональные поля:**
- `description` (string) - Описание отдела

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "Department created successfully",
    "data": {
        "department": {
            "id": 2,
            "name": "HR отдел",
            "description": "Отдел кадров",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

---

### GET `/api/departments/{id}`

Получение информации о конкретном отделе.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Department retrieved successfully",
    "data": {
        "department": {
            "id": 1,
            "name": "IT отдел",
            "description": "Отдел информационных технологий",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "users": [
                {
                    "id": 1,
                    "name": "Иван Иванов",
                    "email": "ivan@company.kz",
                    "position": "Разработчик"
                }
            ]
        }
    }
}
```

---

### PUT `/api/departments/{id}`

Обновление информации об отделе.

**Параметры запроса:**
```json
{
    "name": "IT и Разработка",
    "description": "Отдел информационных технологий и разработки",
    "organization_id": 1
}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Department updated successfully",
    "data": {
        "department": {
            "id": 1,
            "name": "IT и Разработка",
            "description": "Отдел информационных технологий и разработки",
            "organization_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

---

### DELETE `/api/departments/{id}`

Удаление отдела.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Department deleted successfully",
    "data": null
}
```

---

## Должности

### GET `/api/positions`

Получение списка всех должностей.

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы
- `per_page` (integer, optional) - Количество записей на странице
- `search` (string, optional) - Поиск по названию должности
- `department_id` (integer, optional) - Фильтр по отделу

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Positions retrieved successfully",
    "data": {
        "positions": [
            {
                "id": 1,
                "name": "Разработчик",
                "description": "Разработчик программного обеспечения",
                "department_id": 1,
                "department": {
                    "id": 1,
                    "name": "IT отдел",
                    "organization": {
                        "id": 1,
                        "name": "ООО Компания"
                    }
                },
                "salary_min": 300000,
                "salary_max": 500000,
                "is_active": true,
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z",
                "users_count": 3
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1
        }
    }
}
```

---

### POST `/api/positions`

Создание новой должности.

**Параметры запроса:**
```json
{
    "name": "Менеджер по продажам",
    "description": "Менеджер по работе с клиентами",
    "department_id": 2,
    "salary_min": 200000,
    "salary_max": 350000
}
```

**Обязательные поля:**
- `name` (string, max:255) - Название должности
- `department_id` (integer, exists:departments) - ID отдела

**Опциональные поля:**
- `description` (string) - Описание должности
- `salary_min` (integer) - Минимальная зарплата
- `salary_max` (integer) - Максимальная зарплата

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "Position created successfully",
    "data": {
        "position": {
            "id": 2,
            "name": "Менеджер по продажам",
            "description": "Менеджер по работе с клиентами",
            "department_id": 2,
            "department": {
                "id": 2,
                "name": "Отдел продаж",
                "organization": {
                    "id": 1,
                    "name": "ООО Компания"
                }
            },
            "salary_min": 200000,
            "salary_max": 350000,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

---

### GET `/api/positions/{id}`

Получение информации о конкретной должности.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Position retrieved successfully",
    "data": {
        "position": {
            "id": 1,
            "name": "Разработчик",
            "description": "Разработчик программного обеспечения",
            "department_id": 1,
            "department": {
                "id": 1,
                "name": "IT отдел",
                "organization": {
                    "id": 1,
                    "name": "ООО Компания"
                }
            },
            "salary_min": 300000,
            "salary_max": 500000,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "users": [
                {
                    "id": 1,
                    "name": "Иван Иванов",
                    "email": "ivan@company.kz",
                    "salary": 400000
                }
            ]
        }
    }
}
```

---

### PUT `/api/positions/{id}`

Обновление информации о должности.

**Параметры запроса:**
```json
{
    "name": "Старший разработчик",
    "description": "Старший разработчик программного обеспечения",
    "department_id": 1,
    "salary_min": 400000,
    "salary_max": 600000
}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Position updated successfully",
    "data": {
        "position": {
            "id": 1,
            "name": "Старший разработчик",
            "description": "Старший разработчик программного обеспечения",
            "department_id": 1,
            "department": {
                "id": 1,
                "name": "IT отдел",
                "organization": {
                    "id": 1,
                    "name": "ООО Компания"
                }
            },
            "salary_min": 400000,
            "salary_max": 600000,
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

---

### DELETE `/api/positions/{id}`

Удаление должности.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Position deleted successfully",
    "data": null
}
```

---

## Пользователи (Сотрудники)

### GET `/api/users`

Получение списка всех пользователей/сотрудников.

**Параметры запроса:**
- `page` (integer, optional) - Номер страницы
- `per_page` (integer, optional) - Количество записей на странице
- `search` (string, optional) - Поиск по имени или email
- `organization_id` (integer, optional) - Фильтр по организации
- `department_id` (integer, optional) - Фильтр по отделу
- `position_id` (integer, optional) - Фильтр по должности
- `role` (string, optional) - Фильтр по роли
- `is_active` (boolean, optional) - Фильтр по статусу активности

**Успешный ответ (200):**
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
                "role": "user",
                "organization_id": 1,
                "department_id": 1,
                "position_id": 1,
                "organization": {
                    "id": 1,
                    "name": "ООО Компания"
                },
                "department": {
                    "id": 1,
                    "name": "IT отдел"
                },
                "position": {
                    "id": 1,
                    "name": "Разработчик"
                },
                "is_active": true,
                "last_login_at": "2024-01-01T10:00:00.000000Z",
                "created_at": "2024-01-01T12:00:00.000000Z",
                "updated_at": "2024-01-01T12:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1
        }
    }
}
```

---

### POST `/api/users`

Создание нового пользователя/сотрудника.

**Параметры запроса:**
```json
{
    "name": "Петр Петров",
    "email": "petr@company.kz",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+7 777 987 65 43",
    "position": "Менеджер",
    "organization_id": 1,
    "department_id": 2,
    "position_id": 2,
    "role": "user"
}
```

**Обязательные поля:**
- `name` (string, max:255) - Имя пользователя
- `email` (string, email, unique) - Email адрес
- `password` (string, min:8) - Пароль
- `password_confirmation` (string) - Подтверждение пароля
- `organization_id` (integer, exists:organizations) - ID организации

**Опциональные поля:**
- `phone` (string, max:20) - Телефон
- `position` (string, max:255) - Должность
- `department_id` (integer, exists:departments) - ID отдела
- `position_id` (integer, exists:positions) - ID должности
- `role` (string, in:admin,manager,user) - Роль пользователя

**Успешный ответ (201):**
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
            "role": "user",
            "organization_id": 1,
            "department_id": 2,
            "position_id": 2,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "department": {
                "id": 2,
                "name": "Отдел продаж"
            },
            "position": {
                "id": 2,
                "name": "Менеджер по продажам"
            },
            "is_active": true,
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

---

### GET `/api/users/{id}`

Получение информации о конкретном пользователе.

**Успешный ответ (200):**
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
            "role": "user",
            "organization_id": 1,
            "department_id": 1,
            "position_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "department": {
                "id": 1,
                "name": "IT отдел"
            },
            "position": {
                "id": 1,
                "name": "Разработчик"
            },
            "is_active": true,
            "last_login_at": "2024-01-01T10:00:00.000000Z",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z",
            "permissions": [],
            "roles": ["user"]
        }
    }
}
```

---

### PUT `/api/users/{id}`

Обновление информации о пользователе.

**Параметры запроса:**
```json
{
    "name": "Иван Петров",
    "phone": "+7 777 555 44 33",
    "position": "Старший разработчик",
    "organization_id": 1,
    "department_id": 1,
    "position_id": 1,
    "role": "manager"
}
```

**Успешный ответ (200):**
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
            "role": "manager",
            "organization_id": 1,
            "department_id": 1,
            "position_id": 1,
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "department": {
                "id": 1,
                "name": "IT отдел"
            },
            "position": {
                "id": 1,
                "name": "Разработчик"
            },
            "is_active": true,
            "last_login_at": "2024-01-01T10:00:00.000000Z",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T13:00:00.000000Z"
        }
    }
}
```

---

### DELETE `/api/users/{id}`

Удаление пользователя.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "User deleted successfully",
    "data": null
}
```

---

### PUT `/api/users/{id}/password`

Изменение пароля пользователя.

**Параметры запроса:**
```json
{
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Обязательные поля:**
- `password` (string, min:8) - Новый пароль
- `password_confirmation` (string) - Подтверждение нового пароля

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Password changed successfully",
    "data": null
}
```

---

### PUT `/api/users/{id}/activate`

Активация пользователя.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "User activated successfully",
    "data": {
        "user": {
            "id": 1,
            "is_active": true
        }
    }
}
```

---

### PUT `/api/users/{id}/deactivate`

Деактивация пользователя.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "User deactivated successfully",
    "data": {
        "user": {
            "id": 1,
            "is_active": false
        }
    }
}
```

---

## Роли

### GET `/api/roles`

Получение списка всех ролей.

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Roles retrieved successfully",
    "data": {
        "roles": [
            {
                "id": 1,
                "name": "admin",
                "display_name": "Администратор",
                "description": "Полный доступ к системе"
            },
            {
                "id": 2,
                "name": "manager",
                "display_name": "Менеджер",
                "description": "Управление отделом"
            },
            {
                "id": 3,
                "name": "user",
                "display_name": "Пользователь",
                "description": "Обычный пользователь"
            }
        ]
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
| 404 | Ресурс не найден |
| 422 | Ошибка валидации |
| 429 | Превышен лимит запросов |
| 500 | Внутренняя ошибка сервера |

---

## Примеры использования

### JavaScript (Fetch API)
```javascript
// Получение списка организаций
const getOrganizations = async () => {
    const token = localStorage.getItem('token');
    const response = await fetch('/api/organizations', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};

// Создание нового отдела
const createDepartment = async (departmentData) => {
    const token = localStorage.getItem('token');
    const response = await fetch('/api/departments', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(departmentData)
    });
    
    return await response.json();
};

// Обновление пользователя
const updateUser = async (userId, userData) => {
    const token = localStorage.getItem('token');
    const response = await fetch(`/api/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    });
    
    return await response.json();
};
```

### PHP (cURL)
```php
// Получение списка должностей
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://back-chat.ap.kz/api/positions');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// Создание новой организации
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://back-chat.ap.kz/api/organizations');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'ООО Новая Компания',
    'description' => 'Описание компании',
    'address' => 'г. Алматы, ул. Абая 1',
    'phone' => '+7 727 123 45 67',
    'email' => 'info@newcompany.kz'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);
```

---

## Быстрое тестирование

### 1. Получение списка организаций
```bash
curl -X GET https://back-chat.ap.kz/api/organizations \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### 2. Создание нового отдела
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

### 3. Получение списка пользователей
```bash
curl -X GET https://back-chat.ap.kz/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### 4. Создание новой должности
```bash
curl -X POST https://back-chat.ap.kz/api/positions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Менеджер по продажам",
    "description": "Менеджер по работе с клиентами",
    "department_id": 2,
    "salary_min": 200000,
    "salary_max": 350000
  }'
```
