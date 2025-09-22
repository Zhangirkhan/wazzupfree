# Мультитенантная интеграция с Wazzup24

## Обзор

Реализована мультитенантная система для работы с Wazzup24, где каждая организация может иметь свои настройки API, номера телефонов и webhook URL.

## Основные возможности

- ✅ Отдельные API ключи для каждой организации
- ✅ Уникальные webhook URL для каждой организации  
- ✅ Изоляция данных между организациями
- ✅ Управление настройками через API
- ✅ Контроль доступа через политики
- ✅ Админ может просматривать все организации

## Структура данных

### Таблица organizations

Добавлены поля для Wazzup24:
- `wazzup24_api_key` - API ключ организации
- `wazzup24_channel_id` - ID канала Wazzup24
- `wazzup24_webhook_url` - URL webhook (опционально)
- `wazzup24_webhook_secret` - Секрет для проверки webhook
- `wazzup24_settings` - Дополнительные настройки (JSON)
- `wazzup24_enabled` - Включен ли Wazzup24 для организации

## API Endpoints

### Управление настройками Wazzup24

```
GET    /api/organizations/{organization}/wazzup24/settings
PUT    /api/organizations/{organization}/wazzup24/settings
POST   /api/organizations/{organization}/wazzup24/test-connection
GET    /api/organizations/{organization}/wazzup24/channels
POST   /api/organizations/{organization}/wazzup24/setup-webhooks
GET    /api/organizations/{organization}/wazzup24/clients
POST   /api/organizations/{organization}/wazzup24/send-message
```

### Webhook endpoints

```
GET/POST /api/webhooks/wazzup24                    # Общий webhook (для совместимости)
GET/POST /api/webhooks/organization/{organization} # Webhook для конкретной организации
```

## Использование

### 1. Настройка организации через команду

```bash
# Настройка Wazzup24 для организации
php artisan organization:wazzup-setup 1 \
  --api-key="your_api_key" \
  --channel-id="your_channel_id" \
  --webhook-url="https://yourdomain.com/api/webhooks/organization/org-slug"

# Только тестирование подключения
php artisan organization:wazzup-setup 1 --test
```

### 2. Настройка через API

```bash
# Получение настроек
curl -H "Authorization: Bearer YOUR_TOKEN" \
  GET /api/organizations/1/wazzup24/settings

# Обновление настроек
curl -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -X PUT /api/organizations/1/wazzup24/settings \
  -d '{
    "wazzup24_api_key": "your_api_key",
    "wazzup24_channel_id": "your_channel_id",
    "wazzup24_enabled": true
  }'

# Тестирование подключения
curl -H "Authorization: Bearer YOUR_TOKEN" \
  -X POST /api/organizations/1/wazzup24/test-connection
```

### 3. Настройка webhook в Wazzup24

Для каждой организации используйте уникальный webhook URL:
```
https://yourdomain.com/api/webhooks/organization/{organization-slug}
```

## Контроль доступа

### Политики доступа

- **Суперадмин** (`role = 'admin'`) - полный доступ ко всем организациям
- **Админ организации** - управление настройками своей организации
- **Обычные пользователи** - просмотр данных своей организации

### Проверка прав

```php
// В контроллере
if (!Gate::allows('manageWazzup24', $organization)) {
    return response()->json(['error' => 'Доступ запрещен'], 403);
}

// В Blade шаблоне
@can('manageWazzup24', $organization)
    <!-- Кнопки управления -->
@endcan
```

## Сервисы

### OrganizationWazzupService

Сервис для работы с Wazzup24 API для конкретной организации:

```php
$wazzupService = new OrganizationWazzupService($organization);

// Тестирование подключения
$result = $wazzupService->testConnection();

// Получение каналов
$channels = $wazzupService->getChannels();

// Отправка сообщения
$result = $wazzupService->sendMessage($channelId, $chatType, $chatId, $text);

// Настройка webhook'ов
$result = $wazzupService->setupWebhooks($webhookUrl);
```

## Изоляция данных

### Чаты и сообщения

- Каждый чат привязан к конкретной организации
- Сообщения содержат `organization_id` в metadata
- Webhook'и обрабатываются в контексте организации

### Пользователи

- Пользователи могут быть привязаны к нескольким организациям
- При создании чата пользователь привязывается к организации
- Доступ к данным контролируется через политики

## Логирование

Все операции логируются с указанием организации:

```php
Log::info('Wazzup24 API request for organization ' . $organization->id, [
    'organization' => $organization->name,
    'method' => $method,
    'endpoint' => $endpoint
]);
```

## Безопасность

1. **API ключи** хранятся в зашифрованном виде
2. **Webhook секреты** для проверки подлинности
3. **Политики доступа** на уровне контроллеров
4. **Изоляция данных** между организациями
5. **Логирование** всех операций

## Миграция

Для применения изменений выполните:

```bash
php artisan migrate
```

## Примеры использования

### Создание организации с Wazzup24

```php
$organization = Organization::create([
    'name' => 'Моя компания',
    'slug' => 'my-company',
    'wazzup24_api_key' => 'your_api_key',
    'wazzup24_channel_id' => 'your_channel_id',
    'wazzup24_enabled' => true,
]);

// Настройка webhook'ов
$wazzupService = new OrganizationWazzupService($organization);
$webhookUrl = $organization->getWebhookUrl();
$wazzupService->setupWebhooks($webhookUrl);
```

### Обработка входящих сообщений

Webhook автоматически:
1. Определяет организацию по URL
2. Проверяет настройки Wazzup24
3. Создает/находит пользователя
4. Создает/находит чат в контексте организации
5. Сохраняет сообщение с привязкой к организации

## Мониторинг

Для мониторинга работы системы:

```bash
# Проверка статуса всех организаций
php artisan organization:wazzup-setup all --test

# Просмотр логов
tail -f storage/logs/laravel.log | grep "ORGANIZATION WEBHOOK"
```
