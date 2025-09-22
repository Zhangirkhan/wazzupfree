# Chat AP.KZ - API Документация

Полная документация API для системы чатов Chat AP.KZ.

## 📋 Содержание

1. [Аутентификация](API_AUTH.md) - Регистрация, вход, управление профилем
2. [Чаты](API_CHATS.md) - Создание, получение, поиск чатов
3. [Сообщения](API_MESSAGES.md) - Отправка и получение сообщений
4. [Webhooks](API_WEBHOOKS.md) - Интеграция с внешними сервисами

## 🚀 Быстрый старт

### 1. Получение токена
```bash
curl -X POST https://back-chat.ap.kz/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@back-chat.ap.kz",
    "password": "password123"
  }'
```

### 2. Использование токена
```bash
curl -X GET https://back-chat.ap.kz/api/chats \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

## 🔑 Тестовые данные

**Тестовый пользователь:**
- **Email**: `test@back-chat.ap.kz`
- **Пароль**: `password123`

## 📡 Базовый URL

```
https://back-chat.ap.kz/api
```

## 🔐 Аутентификация

API использует Bearer Token аутентификацию через Laravel Sanctum.

### Заголовки для всех запросов:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## 📊 Структура ответов

### Успешный ответ:
```json
{
    "status": "success",
    "message": "Описание операции",
    "data": { ... }
}
```

### Ответ с ошибкой:
```json
{
    "status": "error",
    "message": "Описание ошибки",
    "errors": { ... }
}
```

### Пагинированный ответ:
```json
{
    "status": "success",
    "message": "Данные получены",
    "data": [ ... ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 100,
            "from": 1,
            "to": 20,
            "has_more_pages": true,
            "links": {
                "first": "https://back-chat.ap.kz/api/chats?page=1",
                "last": "https://back-chat.ap.kz/api/chats?page=5",
                "prev": null,
                "next": "https://back-chat.ap.kz/api/chats?page=2"
            }
        },
        "timestamp": "2024-01-01T16:00:00.000000Z",
        "version": "1.0.0"
    }
}
```

## 🚦 Коды ответов

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

## ⚡ Rate Limiting

- **Общие запросы**: 60 запросов в минуту
- **Отправка сообщений**: 30 запросов в минуту
- **Аутентификация**: 5 попыток в минуту

## 🛠 Основные эндпоинты

### Аутентификация
- `POST /api/auth/register` - Регистрация
- `POST /api/auth/login` - Вход
- `POST /api/auth/logout` - Выход
- `GET /api/auth/me` - Профиль пользователя
- `PUT /api/auth/profile` - Обновление профиля
- `PUT /api/auth/password` - Смена пароля
- `GET /api/auth/stats` - Статистика пользователя

### Чаты
- `GET /api/chats` - Список чатов
- `POST /api/chats` - Создание чата
- `GET /api/chats/{id}` - Получение чата
- `GET /api/chats/search` - Поиск чатов
- `POST /api/chats/{id}/end` - Завершение чата
- `POST /api/chats/{id}/transfer` - Передача чата

### Сообщения
- `POST /api/chats/{id}/send` - Отправка сообщения
- `GET /api/chats/{id}/messages` - Получение сообщений
- `POST /api/messages/chats/{id}/system-message` - Системное сообщение
- `POST /api/messages/{id}/hide` - Скрытие сообщения

### Webhooks
- `POST /api/webhooks/wazzup24` - Webhook Wazzup24

## 📱 Примеры использования

### JavaScript (Fetch API)
```javascript
class ChatAPI {
    constructor(baseURL = 'https://back-chat.ap.kz/api') {
        this.baseURL = baseURL;
        this.token = localStorage.getItem('token');
    }
    
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };
        
        if (this.token) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }
        
        const response = await fetch(url, config);
        return await response.json();
    }
    
    // Аутентификация
    async login(email, password) {
        const data = await this.request('/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        if (data.status === 'success') {
            this.token = data.data.token;
            localStorage.setItem('token', this.token);
        }
        
        return data;
    }
    
    // Чаты
    async getChats(page = 1) {
        return this.request(`/chats?page=${page}`);
    }
    
    async createChat(chatData) {
        return this.request('/chats', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(chatData)
        });
    }
    
    // Сообщения
    async sendMessage(chatId, message, file = null) {
        const formData = new FormData();
        formData.append('message', message);
        if (file) formData.append('file', file);
        
        return this.request(`/chats/${chatId}/send`, {
            method: 'POST',
            body: formData
        });
    }
    
    async getMessages(chatId, page = 1) {
        return this.request(`/chats/${chatId}/messages?page=${page}`);
    }
}

// Использование
const api = new ChatAPI();

// Вход
await api.login('test@back-chat.ap.kz', 'password123');

// Получение чатов
const chats = await api.getChats();

// Отправка сообщения
await api.sendMessage(1, 'Привет!');
```

### PHP
```php
class ChatAPI {
    private $baseURL;
    private $token;
    
    public function __construct($baseURL = 'https://back-chat.ap.kz/api') {
        $this->baseURL = $baseURL;
    }
    
    private function request($endpoint, $options = []) {
        $url = $this->baseURL . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        if ($this->token) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]);
        }
        
        if (isset($options['method'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        }
        
        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['data']));
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function login($email, $password) {
        $data = $this->request('/auth/login', [
            'method' => 'POST',
            'data' => ['email' => $email, 'password' => $password]
        ]);
        
        if ($data['status'] === 'success') {
            $this->token = $data['data']['token'];
        }
        
        return $data;
    }
    
    public function getChats($page = 1) {
        return $this->request("/chats?page={$page}");
    }
    
    public function sendMessage($chatId, $message) {
        return $this->request("/chats/{$chatId}/send", [
            'method' => 'POST',
            'data' => ['message' => $message, 'type' => 'text']
        ]);
    }
}

// Использование
$api = new ChatAPI();
$api->login('test@back-chat.ap.kz', 'password123');
$chats = $api->getChats();
```

## 🔧 Интеграция с Wazzup24

### Настройка webhook в Wazzup24:
1. URL: `https://back-chat.ap.kz/api/webhooks/wazzup24`
2. Методы: GET, POST
3. События: message, status, contact

### Тестирование webhook:
```bash
curl -X POST https://back-chat.ap.kz/api/webhooks/wazzup24 \
  -H "Content-Type: application/json" \
  -d '{
    "event": "message",
    "data": {
        "id": "test_123",
        "chatId": "test_chat",
        "text": "Тестовое сообщение",
        "type": "text",
        "from": "+7 777 000 00 00",
        "timestamp": 1640995200
    }
  }'
```

## 📞 Поддержка

При возникновении вопросов или проблем:
1. Проверьте документацию по конкретному эндпоинту
2. Убедитесь в правильности токена аутентификации
3. Проверьте формат отправляемых данных
4. Обратитесь к разработчикам API

## 🔄 Версионирование

Текущая версия API: **1.0.0**

Все ответы содержат информацию о версии в поле `meta.version`.

## 📝 Changelog

### v1.0.0 (2024-01-01)
- Первоначальный релиз API
- Аутентификация через Laravel Sanctum
- Управление чатами и сообщениями
- Интеграция с Wazzup24
- Rate limiting
- Пагинация
- Валидация файлов
- Soft deletes
- Логирование
- Обработка ошибок
