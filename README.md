# Chat AP.KZ - Система управления чатами

## Описание
Система управления чатами и клиентами для организации AP.KZ. Включает в себя управление пользователями, организациями, отделами, должностями, клиентами, контрагентами и шаблонами сообщений.

## Технологии
- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **База данных**: MySQL
- **Аутентификация**: Laravel Sanctum
- **API**: RESTful API

## Установка

### Требования
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+

### Установка зависимостей
```bash
composer install
npm install
```

### Настройка окружения
```bash
cp .env.example .env
php artisan key:generate
```

### Настройка базы данных
Отредактируйте файл `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_ap_kz
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Миграции и сиды
```bash
php artisan migrate
php artisan db:seed
```

### Запуск приложения
```bash
# Backend
php artisan serve

# Frontend (в отдельном терминале)
npm run dev
```

## Доступ к системе

### Пользователи по умолчанию
После установки системы создаются следующие пользователи:

#### 👑 Администратор
**Логин**: `admin@erp.ap.kz`  
**Пароль**: `password`  
**Роль**: `admin`
**Описание**: Полный доступ ко всем разделам системы

#### 👨‍💼 Менеджер
**Логин**: `manager@erp.ap.kz`  
**Пароль**: `password`  
**Роль**: `manager`  
**Описание**: Доступ к мессенджеру и клиентам

#### 👷 Руководитель
**Логин**: `leader@erp.ap.kz`  
**Пароль**: `password`  
**Роль**: `leader`  
**Описание**: Руководство отделом и принятие решений

### Тестовые пользователи
Для тестирования API можно использовать:
**Логин**: `test@back-chat.ap.kz`  
**Пароль**: `password123`  
**Роль**: `employee`

### Создание дополнительных пользователей
```bash
# Создать администратора
php artisan user:create-admin admin@example.com

# Создать менеджера
php artisan user:create-manager manager@example.com 1

# Назначить роль пользователю
php artisan user:assign-role user@example.com admin
```

### Быстрый доступ к админке
```bash
# Показать информацию для входа
php artisan admin:login admin@erp.ap.kz

# Показать всех пользователей
php artisan profile:show --all
```

## Решение проблем

### ❌ Устаревшие инструкции (CSRF токены)
**ВНИМАНИЕ**: Эти инструкции устарели! API теперь использует Bearer токены, а не CSRF.

### ✅ Правильная аутентификация (SPA + Bearer токены)
API использует Laravel Sanctum с гибридным подходом:
- **CSRF токены** для защиты от CSRF атак при логине
- **Bearer токены** для API аутентификации после логина
- **Cookies** для хранения сессии

**Проблема "419 CSRF token mismatch" решена** - настроен правильный flow аутентификации.

### 🔄 Flow аутентификации:

```
1. Фронтенд → GET /api/csrf-token → Получает CSRF токен
2. Фронтенд → POST /api/auth/login (с CSRF токеном) → Получает Bearer токен
3. Фронтенд → API запросы с Bearer токеном → Доступ к защищенным ресурсам
```

**Почему нужен CSRF токен?**
- Защита от CSRF атак при логине
- Валидация, что запрос пришел от легитимного клиента
- Laravel Sanctum требует CSRF для SPA аутентификации

### CORS ошибки
CORS настроен для работы с Bearer токенами. Убедитесь, что фронтенд отправляет правильные заголовки:

```javascript
// Axios
const api = axios.create({
  baseURL: 'https://back-chat.ap.kz/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
});

// Добавить токен к запросам
api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Fetch
const token = localStorage.getItem('auth_token');
fetch(url, {
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': token ? `Bearer ${token}` : '',
  }
});
```

### Проблема с CSS (FOUC)
Ошибка "Макет был принудительно применён перед полной загрузкой страницы" - проблема фронтенда:

```html
<!-- Предзагрузка критических стилей -->
<link rel="preload" href="/css/app.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="/css/app.css"></noscript>

<!-- Или используйте CSS-in-JS для предотвращения FOUC -->
```

## ✅ Все проблемы решены!

### Что было исправлено:
1. **CORS настройки** - настроены для работы с Bearer токенами
2. **Аутентификация** - исправлен AuthService для работы с Sanctum
3. **Пользователи** - созданы все необходимые пользователи
4. **Права доступа** - исправлены права на storage/logs и storage/framework/views
5. **Папка views** - создана отсутствующая папка resources/views
6. **Кэш** - очищен весь кэш Laravel для применения изменений
7. **SPA аутентификация** - настроен правильный flow: CSRF токен → логин → Bearer токен
8. **CSRF защита** - добавлен /api/csrf-token и middleware для SPA безопасности

### Рабочие пользователи:
- **👑 Админ**: `admin@erp.ap.kz` / `password`
- **👨‍💼 Менеджер**: `manager@erp.ap.kz` / `password`  
- **👷 Руководитель**: `leader@erp.ap.kz` / `password`
- **🧪 Тест**: `test@back-chat.ap.kz` / `password123`

### 🔧 Правильная последовательность для фронтенда (SPA + Bearer токены):

#### 1. Получить CSRF токен:
```javascript
// Получаем CSRF токен для защиты от CSRF атак
const csrfResponse = await fetch('https://back-chat.ap.kz/api/csrf-token', {
  method: 'GET',
  credentials: 'include' // Важно для cookies
});

const csrfData = await csrfResponse.json();
const csrfToken = csrfData.csrf_token;
```

#### 2. Войти в систему с CSRF токеном:
```javascript
const response = await fetch('https://back-chat.ap.kz/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': csrfToken // CSRF защита
  },
  credentials: 'include', // Для cookies
  body: JSON.stringify({
    email: 'admin@erp.ap.kz',
    password: 'password'
  })
});

const data = await response.json();
const token = data.data.token; // Bearer токен
```

#### 3. Использовать Bearer токен для защищенных запросов:
```javascript
const response = await fetch('https://back-chat.ap.kz/api/user', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});
```

#### 4. Выйти из системы:
```javascript
const response = await fetch('https://back-chat.ap.kz/api/auth/logout', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});
```

### 📝 Инструкции для фронтенда

#### Полный пример аутентификации:

**Для веб-приложений (SPA):**
```javascript
class AuthService {
  constructor() {
    this.baseURL = 'https://back-chat.ap.kz/api';
    this.token = localStorage.getItem('auth_token');
  }

  async login(email, password) {
    try {
      // 1. Получаем CSRF cookie
      await fetch('https://back-chat.ap.kz/sanctum/csrf-cookie', {
        credentials: 'include'
      });
      
      // 2. Выполняем логин через SPA endpoint
      const response = await fetch(`${this.baseURL}/auth/spa/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include',
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();
      
      if (data.success) {
        this.token = data.data.token;
        localStorage.setItem('auth_token', this.token);
```

**Для мобильных приложений:**
```javascript
class AuthService {
  constructor() {
    this.baseURL = 'https://back-chat.ap.kz/api';
    this.token = localStorage.getItem('auth_token');
  }

  async login(email, password) {
    try {
      // Простой API без CSRF
      const response = await fetch(`${this.baseURL}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();
      
      if (data.success) {
        this.token = data.data.token;
        localStorage.setItem('auth_token', this.token);
        return data;
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  async logout() {
    try {
      await fetch(`${this.baseURL}/auth/logout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Accept': 'application/json'
        }
      });
      
      this.token = null;
      localStorage.removeItem('auth_token');
    } catch (error) {
      console.error('Logout error:', error);
    }
  }

  async getUser() {
    try {
      const response = await fetch(`${this.baseURL}/user`, {
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Accept': 'application/json'
        }
      });

      return await response.json();
    } catch (error) {
      console.error('Get user error:', error);
      throw error;
    }
  }

  isAuthenticated() {
    return !!this.token;
  }
}

// Использование:
const auth = new AuthService();

// Войти
await auth.login('admin@erp.ap.kz', 'password');

// Получить пользователя
const user = await auth.getUser();

// Выйти
await auth.logout();
```

## API Документация

### Базовый URL
```
https://back-chat.ap.kz/api
```

### Аутентификация
API использует Bearer Token аутентификацию через Laravel Sanctum.

### Документация по эндпоинтам
- [Организации](docs/API_ENDPOINTS.md)
- [Отделы](docs/API_DEPARTMENTS.md)
- [Должности](docs/API_POSITIONS.md)
- [Сотрудники](docs/API_EMPLOYEES.md)
- [Контрагенты](docs/API_CONTRACTORS.md)
- [Клиенты](docs/API_CLIENTS.md)
- [Чаты и сообщения](docs/API_CHATS_MESSAGES.md)
- [Шаблоны](docs/API_TEMPLATES.md)
- [Профиль пользователя](docs/API_PROFILE.md)

## Структура проекта

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/          # API контроллеры
│   │   ├── Resources/        # API ресурсы
│   │   └── Requests/         # Валидация запросов
│   ├── Models/               # Eloquent модели
│   └── Services/             # Бизнес-логика
├── database/
│   ├── migrations/           # Миграции БД
│   └── seeders/             # Сиды
├── docs/                    # API документация
├── public/                  # Публичные файлы
└── routes/
    └── api.php              # API маршруты
```

## Основные функции

### Управление пользователями
- Регистрация и аутентификация
- Роли и права доступа
- Профили пользователей
- Статистика работы

### Управление организациями
- Создание и редактирование организаций
- Управление отделами и должностями
- Назначение сотрудников

### Работа с клиентами
- Физические лица (без контрагентов)
- Юридические лица (с контрагентами)
- Управление контрагентами
- История взаимодействий

### Система чатов
- Создание и управление чатами
- Отправка сообщений
- Передача чатов между сотрудниками
- Поиск и фильтрация

### Шаблоны сообщений
- Создание шаблонов
- Переменные в шаблонах
- Многоязычность
- Статистика использования

## Роли пользователей

| Роль | Описание | Права доступа |
|------|----------|---------------|
| `admin` | Администратор | Полный доступ ко всем разделам системы |
| `manager` | Менеджер | Доступ к мессенджеру и клиентам |
| `leader` | Руководитель | Руководство отделом и принятие решений |
| `employee` | Сотрудник | Доступ к мессенджеру и клиентам |

## Безопасность

- Аутентификация через Laravel Sanctum
- Валидация всех входящих данных
- Защита от CSRF атак
- Ограничение доступа по ролям
- Логирование действий пользователей

## Разработка

### Запуск тестов
```bash
php artisan test
```

### Линтинг кода
```bash
./vendor/bin/pint
```

### Создание миграции
```bash
php artisan make:migration create_table_name
```

### Создание контроллера
```bash
php artisan make:controller Api/ControllerName --api
```

## Troubleshooting

### 419 CSRF token mismatch

**Проблема:** При попытке логина с фронтенда получаете ошибку `419 CSRF token mismatch`.

**Решение:**

1. **Для веб-приложений (SPA) - используйте `/api/auth/spa/login`:**
   ```javascript
   // 1. Сначала получите CSRF cookie
   await fetch('https://back-chat.ap.kz/sanctum/csrf-cookie', {
     credentials: 'include'
   });
   
   // 2. Затем выполните логин через SPA endpoint
   const response = await fetch('https://back-chat.ap.kz/api/auth/spa/login', {
     method: 'POST',
     headers: {
       'Content-Type': 'application/json',
       'Accept': 'application/json',
       'X-Requested-With': 'XMLHttpRequest'
     },
     credentials: 'include',
     body: JSON.stringify({
       email: 'admin@erp.ap.kz',
       password: 'password'
     })
   });
   ```

2. **Для мобильных приложений - используйте `/api/auth/login` (без CSRF):**
   ```javascript
   const response = await fetch('https://back-chat.ap.kz/api/auth/login', {
     method: 'POST',
     headers: {
       'Content-Type': 'application/json',
       'Accept': 'application/json'
     },
     body: JSON.stringify({
       email: 'admin@erp.ap.kz',
       password: 'password'
     })
   });
   ```

3. **Проверьте конфигурацию CORS:**
   ```bash
   # В config/cors.php должно быть:
   'allowed_origins' => ['https://chat.ap.kz', 'http://localhost:3000'],
   'supports_credentials' => true,
   'allowed_headers' => ['*'],
   ```

### CORS ошибки

**Проблема:** `Access to fetch at 'https://back-chat.ap.kz/api/auth/login' from origin 'https://chat.ap.kz' has been blocked by CORS policy`

**Решение:**
1. Проверьте `config/cors.php`
2. Убедитесь, что ваш домен добавлен в `allowed_origins`
3. Перезапустите сервер после изменений

### CSS не загружается

**Проблема:** Стили не применяются, страница выглядит нестилизованной.

**Решение:**
1. Проверьте, что CSS файлы доступны по правильным путям
2. Очистите кэш браузера
3. Проверьте консоль браузера на ошибки загрузки ресурсов

### 500 Internal Server Error

**Проблема:** API возвращает ошибку 500.

**Решение:**
1. Проверьте логи Laravel: `tail -f storage/logs/laravel.log`
2. Очистите кэш: `php artisan config:clear && php artisan cache:clear`
3. Проверьте конфигурацию базы данных
4. Убедитесь, что все миграции выполнены: `php artisan migrate:status`

## Поддержка

Для получения поддержки обращайтесь к администратору системы.

## Лицензия

Проект разработан для AP.KZ. Все права защищены.
