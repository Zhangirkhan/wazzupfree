# API Документация - Аутентификация

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

## Тестовые данные

Для тестирования API доступен тестовый пользователь:

**Тестовый пользователь:**
- **Email**: `test@back-chat.ap.kz`
- **Пароль**: `password123`
- **Имя**: `Test User`
- **Роль**: `user`
- **ID**: `3`

**Пример входа:**
```json
{
    "email": "test@back-chat.ap.kz",
    "password": "password123"
}
```

> ⚠️ **Важно**: Это тестовые данные. В продакшене используйте собственные учетные данные.

---

## Регистрация пользователя

### POST `/api/auth/register`

Создает нового пользователя в системе.

**Параметры запроса:**
```json
{
    "name": "Иван Иванов",
    "email": "ivan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+7 777 123 45 67",
    "position": "Менеджер",
    "organization_id": 1
}
```

**Обязательные поля:**
- `name` (string, max:255) - Имя пользователя
- `email` (string, email, unique) - Email адрес
- `password` (string, min:8) - Пароль
- `password_confirmation` (string) - Подтверждение пароля

**Опциональные поля:**
- `phone` (string, max:20) - Телефон
- `position` (string, max:255) - Должность
- `organization_id` (integer, exists:organizations) - ID организации

**Успешный ответ (201):**
```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@example.com",
            "phone": "+7 777 123 45 67",
            "position": "Менеджер",
            "role": "user",
            "created_at": "2024-01-01T12:00:00.000000Z"
        },
        "token": "1|abcdef123456...",
        "token_type": "Bearer"
    }
}
```

**Ошибка валидации (422):**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["Пользователь с таким email уже существует"],
        "password": ["Пароль должен содержать минимум 8 символов"]
    }
}
```

---

## Вход в систему

### POST `/api/auth/login`

Аутентификация пользователя в системе.

**Параметры запроса:**
```json
{
    "email": "ivan@example.com",
    "password": "password123"
}
```

**Обязательные поля:**
- `email` (string, email) - Email адрес
- `password` (string) - Пароль

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@example.com",
            "phone": "+7 777 123 45 67",
            "position": "Менеджер",
            "role": "user"
        },
        "token": "1|abcdef123456...",
        "token_type": "Bearer",
        "expires_in": 10080
    }
}
```

**Ошибка аутентификации (401):**
```json
{
    "status": "error",
    "message": "Invalid credentials",
    "code": 401
}
```

---

## Выход из системы

### POST `/api/auth/logout`

Выход пользователя из системы (удаление токена).

**Заголовки:**
```
Authorization: Bearer {token}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Logout successful",
    "data": null
}
```

---

## Обновление токена

### POST `/api/auth/refresh`

Обновление токена аутентификации.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|xyz789...",
        "token_type": "Bearer",
        "expires_in": 10080
    }
}
```

---

## Получение информации о пользователе

### GET `/api/auth/me`

Получение информации о текущем аутентифицированном пользователе.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "User information retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Иван Иванов",
            "email": "ivan@example.com",
            "phone": "+7 777 123 45 67",
            "position": "Менеджер",
            "role": "user",
            "organization": {
                "id": 1,
                "name": "ООО Компания"
            },
            "permissions": [],
            "roles": ["user"]
        }
    }
}
```

---

## Обновление профиля

### PUT `/api/auth/profile`

Обновление профиля пользователя.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Параметры запроса:**
```json
{
    "name": "Иван Петров",
    "phone": "+7 777 987 65 43",
    "position": "Старший менеджер"
}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "Иван Петров",
        "email": "ivan@example.com",
        "phone": "+7 777 987 65 43",
        "position": "Старший менеджер"
    }
}
```

---

## Изменение пароля

### PUT `/api/auth/password`

Изменение пароля пользователя.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Параметры запроса:**
```json
{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Обязательные поля:**
- `current_password` (string) - Текущий пароль
- `new_password` (string, min:8) - Новый пароль
- `new_password_confirmation` (string) - Подтверждение нового пароля

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "Password changed successfully",
    "data": null
}
```

**Ошибка (400):**
```json
{
    "status": "error",
    "message": "Current password is incorrect",
    "code": 400
}
```

---

## Получение статистики пользователя

### GET `/api/auth/stats`

Получение статистики пользователя.

**Заголовки:**
```
Authorization: Bearer {token}
```

**Успешный ответ (200):**
```json
{
    "status": "success",
    "message": "User statistics retrieved successfully",
    "data": {
        "total_chats": 15,
        "active_chats": 3,
        "messages_sent": 127,
        "unread_notifications": 2
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

## Rate Limiting

API имеет ограничения на количество запросов:

- **Общие запросы**: 60 запросов в минуту
- **Отправка сообщений**: 30 запросов в минуту
- **Аутентификация**: 5 попыток в минуту

При превышении лимита возвращается ошибка 429.

---

## Быстрое тестирование

### 1. Вход в систему
```bash
curl -X POST https://back-chat.ap.kz/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@back-chat.ap.kz",
    "password": "password123"
  }'
```

### 2. Получение профиля (замените {token} на полученный токен)
```bash
curl -X GET https://back-chat.ap.kz/api/auth/me \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### 3. Получение статистики
```bash
curl -X GET https://back-chat.ap.kz/api/auth/stats \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Примеры использования

### JavaScript (Fetch API)
```javascript
// Регистрация
const register = async (userData) => {
    const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    });
    
    return await response.json();
};

// Вход
const login = async (credentials) => {
    const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(credentials)
    });
    
    const data = await response.json();
    if (data.status === 'success') {
        localStorage.setItem('token', data.data.token);
    }
    return data;
};

// Защищенные запросы
const getProfile = async () => {
    const token = localStorage.getItem('token');
    const response = await fetch('/api/auth/me', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
};
```

### PHP (cURL)
```php
// Вход
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://back-chat.ap.kz/api/auth/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'ivan@example.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);
```
