# 🚀 Corporate Chat System

Современная система корпоративного общения с интеграцией WhatsApp через Wazzup24.

## 📋 Описание проекта

Corporate Chat System - это полнофункциональная платформа для корпоративного общения, включающая:

- **Внутренний чат** между сотрудниками
- **Интеграция с WhatsApp** через Wazzup24
- **Ролевая система** с иерархией доступа
- **Административная панель** для управления
- **API** для мобильных приложений

## 🎨 Дизайн

Система использует современный дизайн с **зеленой цветовой схемой**:
- Зеленый фон (`bg-green-50`)
- Зеленые акценты в интерфейсе
- Современные компоненты на Tailwind CSS
- Адаптивный дизайн для всех устройств

## 🏗️ Архитектура

### Backend
- **Laravel 12** - основной фреймворк
- **PostgreSQL** - база данных
- **Laravel Sanctum** - аутентификация API
- **Eloquent ORM** - работа с базой данных

### Frontend
- **Blade Templates** - серверный рендеринг
- **Tailwind CSS** - стилизация
- **Alpine.js** - интерактивность
- **Livewire** - динамические обновления

## 🚀 Быстрый старт

### 1. Установка зависимостей
```bash
composer install
npm install
```

### 2. Настройка окружения
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Настройка базы данных
```bash
# В .env файле настройте PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=corporate_chat
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Миграции и сидеры
```bash
php artisan migrate
php artisan db:seed --class=TestDataSeeder
```

### 5. Сборка фронтенда
```bash
npm run build
```

### 6. Запуск сервера
```bash
php artisan serve
```

## 🔐 Доступ к системе

### Веб-интерфейс
- **Главная страница**: http://127.0.0.1:8000/
- **Админ-панель**: http://127.0.0.1:8000/admin
- **Страница входа**: http://127.0.0.1:8000/login

### Тестовые аккаунты
После запуска сидера доступны следующие аккаунты:

| Роль | Email | Пароль | Описание |
|------|-------|--------|----------|
| 👨‍💼 Администратор | admin@example.com | password | Полный доступ ко всем функциям |
| 👨‍💻 Менеджер | manager@example.com | password | Управление чатами и пользователями |
| 👷 Сотрудник | employee@example.com | password | Базовый доступ к чатам |

## 📱 API Endpoints

### Аутентификация
```bash
POST /api/register    # Регистрация пользователя
POST /api/login       # Вход в систему
POST /api/logout      # Выход из системы
GET  /api/me          # Информация о текущем пользователе
```

### Чаты
```bash
GET    /api/chats                    # Список чатов
POST   /api/chats                    # Создание чата
GET    /api/chats/{id}               # Информация о чате
PUT    /api/chats/{id}               # Обновление чата
POST   /api/chats/{id}/close         # Закрытие чата
POST   /api/chats/{id}/transfer      # Передача чата
```

### Сообщения
```bash
GET    /api/chats/{id}/messages      # Сообщения чата
POST   /api/chats/{id}/messages      # Отправка сообщения
POST   /api/messages/{id}/hide       # Скрытие сообщения
POST   /api/chats/{id}/system-message # Системное сообщение
```

### Организации
```bash
GET    /api/organizations            # Список организаций
POST   /api/organizations            # Создание организации
GET    /api/organizations/{id}/departments # Отделы организации
GET    /api/organizations/{id}/roles # Роли организации
GET    /api/organizations/{id}/users # Пользователи организации
```

## 🏢 Структура данных

### Основные модели
- **Organization** - Организации/компании
- **Department** - Отделы
- **Role** - Роли пользователей
- **User** - Пользователи системы
- **Chat** - Чаты
- **Message** - Сообщения
- **ChatParticipant** - Участники чатов

### Связи
- Пользователи могут принадлежать к нескольким организациям
- Каждый пользователь имеет роль в организации
- Чаты могут быть между пользователями или группами
- Сообщения привязаны к чатам и пользователям

## 🎯 Функциональность

### Админ-панель
- **Dashboard** - статистика и обзор системы
- **Пользователи** - управление пользователями и их ролями
- **Отделы** - создание и редактирование отделов
- **Чаты** - просмотр и управление чатами
- **Организации** - управление организациями

### Чат-система
- **Создание чатов** между пользователями
- **Отправка сообщений** (текст, файлы, изображения)
- **Передача чатов** между сотрудниками
- **Закрытие чатов** с системными сообщениями
- **Скрытие сообщений** для подчиненных

### Ролевая система
- **Администратор** - полный доступ
- **Менеджер** - управление чатами и пользователями
- **Сотрудник** - базовый доступ к чатам

## 🔧 Команды Artisan

### Управление системой
```bash
php artisan chat:manage list-users          # Список пользователей
php artisan chat:manage list-chats          # Список чатов
php artisan chat:manage list-organizations  # Список организаций
php artisan chat:manage close-chat {id}     # Закрыть чат
php artisan chat:manage stats               # Статистика системы
```

### Проверка неактивных чатов
```bash
php artisan chats:check-inactive --days=7   # Проверка чатов неактивных 7 дней
```

## 📁 Структура проекта

```
app/
├── Console/Commands/           # Artisan команды
├── Http/Controllers/          # Контроллеры
│   ├── Admin/                # Контроллеры админки
│   └── Api/                  # API контроллеры
├── Models/                   # Eloquent модели
├── Services/                 # Сервисные классы
└── Providers/               # Сервис-провайдеры

config/
├── wazzup24.php             # Конфигурация Wazzup24
└── ...

database/
├── migrations/              # Миграции БД
├── seeders/                # Сидеры данных
└── factories/              # Фабрики моделей

resources/
├── views/                  # Blade шаблоны
│   ├── admin/             # Админ-панель
│   ├── components/        # Blade компоненты
│   └── auth/              # Страницы аутентификации
├── css/                   # Стили
└── js/                    # JavaScript

routes/
├── web.php                # Веб маршруты
├── admin.php              # Маршруты админки
└── api.php                # API маршруты

docs/                      # Документация
├── LOGIN_INSTRUCTIONS.md  # Инструкции по входу
└── ...
```

## 🧪 Тестирование

### Запуск тестов
```bash
php artisan test                    # Все тесты
php artisan test --filter=AuthTest  # Только тесты аутентификации
php artisan test --filter=ChatTest  # Только тесты чатов
```

### Доступные тесты
- **AuthTest** - тесты аутентификации
- **ChatTest** - тесты чатов
- **MessageTest** - тесты сообщений

## 🔒 Безопасность

### Аутентификация
- **Sanctum токены** для API
- **Session аутентификация** для веб-интерфейса
- **CSRF защита** для форм

### Авторизация
- **Ролевая система** с иерархией
- **Проверка прав доступа** к чатам
- **Soft delete** для сообщений

## 📊 Мониторинг

### Логирование
- **API запросы** и ответы
- **Ошибки аутентификации**
- **Действия с чатами**
- **Системные события**

### Статистика
- **Количество пользователей**
- **Активные чаты**
- **Сообщения по типам**
- **Активность по времени**

## 🚀 Развертывание

### Продакшн настройки
```bash
# Оптимизация для продакшна
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Настройка очередей (если используется)
php artisan queue:work
```

### Переменные окружения
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=corporate_chat
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Wazzup24 интеграция (опционально)
WAZZUP24_API_KEY=your_api_key
WAZZUP24_CHANNEL_ID=your_channel_id
WAZZUP24_WEBHOOK_SECRET=your_webhook_secret
```

## 🤝 Вклад в проект

1. Форкните репозиторий
2. Создайте ветку для новой функции
3. Внесите изменения
4. Добавьте тесты
5. Создайте Pull Request

## 📄 Лицензия

Этот проект распространяется под лицензией MIT.

## 📞 Поддержка

Если у вас есть вопросы или проблемы:
- Создайте Issue в репозитории
- Обратитесь к документации в папке `docs/`
- Проверьте логи системы

---

**🎉 Спасибо за использование Corporate Chat System!**
