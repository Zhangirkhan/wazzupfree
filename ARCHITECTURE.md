# Архитектура Backend ERP системы

## Обзор

Данный документ описывает улучшенную архитектуру backend системы, реализующую современные паттерны проектирования для повышения масштабируемости, тестируемости и производительности.

## 🏗️ Архитектурные паттерны

### 1. Repository Pattern

**Цель:** Абстракция доступа к данным, упрощение тестирования и изменение источников данных.

**Структура:**
```
app/
├── Contracts/
│   ├── UserRepositoryInterface.php
│   ├── ChatRepositoryInterface.php
│   ├── MessageRepositoryInterface.php
│   ├── ClientRepositoryInterface.php
│   ├── DepartmentRepositoryInterface.php
│   ├── OrganizationRepositoryInterface.php
│   ├── AuthServiceInterface.php
│   ├── ChatServiceInterface.php
│   └── MessageServiceInterface.php
└── Repositories/
    ├── UserRepository.php
    ├── ChatRepository.php
    ├── MessageRepository.php
    ├── ClientRepository.php
    ├── DepartmentRepository.php
    ├── OrganizationRepository.php
    └── CachedUserRepository.php
```

**Использование:**
```php
// В сервисах
public function __construct(
    private UserRepositoryInterface $userRepository
) {}

public function getUsers(): LengthAwarePaginator
{
    return $this->userRepository->getAll();
}
```

### 2. Event-Driven Architecture

**Цель:** Слабая связанность компонентов, возможность добавления новой функциональности без изменения существующего кода.

**Структура:**
```
app/Events/
├── ChatCreated.php
├── MessageSent.php
├── ChatAssigned.php
└── UserCreated.php

app/Listeners/
├── SendChatNotification.php
├── UpdateChatActivity.php
└── SendAssignmentNotification.php
```

**Использование:**
```php
// В сервисах
public function createChat(array $data, User $user): Chat
{
    $chat = $this->chatRepository->create($data, $user);
    
    // Отправляем событие
    event(new ChatCreated($chat));
    
    return $chat;
}
```

### 3. Form Request Validation

**Цель:** Централизованная валидация, единообразная обработка ошибок.

**Структура:**
```
app/Http/Requests/
├── BaseFormRequest.php
├── CreateUserRequest.php
├── UpdateUserRequest.php
├── CreateChatRequest.php
└── SendMessageRequest.php
```

**Использование:**
```php
// В контроллерах
public function store(CreateUserRequest $request): JsonResponse
{
    $user = $this->userService->createUser($request->validated());
    return $this->successResponse(new UserResource($user));
}
```

### 4. API Resources

**Цель:** Единообразная трансформация данных для API, контроль структуры ответов.

**Структура:**
```
app/Http/Resources/
├── BaseResource.php
├── UserResource.php
├── ChatResource.php
├── MessageResource.php
├── ClientResource.php
├── DepartmentResource.php
├── OrganizationResource.php
├── PositionResource.php
└── CompanyResource.php
```

**Использование:**
```php
// В контроллерах
return $this->successResponse(
    UserResource::collection($users),
    'Users retrieved successfully'
);
```

### 5. Caching System

**Цель:** Повышение производительности, уменьшение нагрузки на базу данных.

**Компоненты:**
- `CacheService` - основной сервис кэширования
- `CachedUserRepository` - пример кэшированного репозитория

**Использование:**
```php
// В сервисах
$users = $this->cacheService->remember('users:all', 300, function() {
    return $this->userRepository->getAll();
});
```

### 6. Authorization with Policies

**Цель:** Централизованная авторизация, четкое разделение прав доступа.

**Структура:**
```
app/Policies/
├── UserPolicy.php
├── ChatPolicy.php
├── MessagePolicy.php
└── ClientPolicy.php
```

**Использование:**
```php
// В контроллерах
public function update(UpdateUserRequest $request, User $user): JsonResponse
{
    $this->authorize('update', $user);
    // Логика обновления
}
```

## 🔧 Сервисы

### Основные сервисы

1. **CacheService** - управление кэшированием
2. **NotificationService** - отправка уведомлений
3. **LoggingService** - централизованное логирование

### Обновленные сервисы

- `UserManagementService` - использует Repository Pattern
- `ChatService` - использует события и репозитории

## 📊 Middleware

### ApiLoggingMiddleware
Логирует все API запросы с метриками производительности.

### PolicyAuthorizationMiddleware
Проверяет права доступа с использованием Policies.

## 🚀 Преимущества новой архитектуры

### 1. Масштабируемость
- Слабая связанность компонентов
- Легкое добавление новой функциональности
- Возможность горизонтального масштабирования

### 2. Тестируемость
- Dependency Injection
- Интерфейсы для всех зависимостей
- Изолированное тестирование компонентов

### 3. Производительность
- Кэширование на уровне репозиториев
- Оптимизированные запросы к БД
- Асинхронная обработка событий

### 4. Поддерживаемость
- Четкое разделение ответственности
- Единообразные паттерны
- Централизованная валидация и авторизация

## 📝 Примеры использования

### Создание пользователя
```php
// 1. Валидация через Form Request
public function store(CreateUserRequest $request): JsonResponse
{
    // 2. Использование сервиса с Repository Pattern
    $user = $this->userService->createUser($request->validated());
    
    // 3. Логирование действия
    $this->loggingService->logUserAction('user_created', ['user_id' => $user->id]);
    
    // 4. Возврат через API Resource
    return $this->successResponse(new UserResource($user));
}
```

### Создание чата с событиями
```php
public function createChat(array $data, User $user): Chat
{
    // 1. Создание через репозиторий
    $chat = $this->chatRepository->create($data, $user);
    
    // 2. Отправка события
    event(new ChatCreated($chat));
    
    // 3. Логирование
    $this->loggingService->logChatActivity('chat_created', $chat->id);
    
    return $chat;
}
```

## 🔄 Миграция

### Поэтапная миграция
1. ✅ Создание Repository Pattern
2. ✅ Реализация Event-Driven Architecture
3. ✅ Внедрение Form Requests
4. ✅ Добавление API Resources
5. ✅ Система кэширования
6. ✅ Policies для авторизации
7. ✅ Обновление сервисов
8. ✅ Обновление контроллеров

### Совместимость
Все изменения обратно совместимы. Существующий код продолжает работать, новые компоненты добавляются постепенно.

## 📈 Мониторинг и логирование

### Структурированное логирование
- Все действия пользователей
- API запросы с метриками
- Ошибки с контекстом
- События системы

### Метрики производительности
- Время выполнения запросов
- Использование кэша
- Количество запросов к БД

## 🎯 Рекомендации по развитию

1. **Добавить Unit тесты** для всех репозиториев и сервисов
2. **Реализовать API версионирование** для обратной совместимости
3. **Добавить Rate Limiting** для защиты от злоупотреблений
4. **Внедрить мониторинг** с помощью Prometheus/Grafana
5. **Добавить документацию API** с помощью Swagger/OpenAPI

## 📚 Дополнительные ресурсы

- [Laravel Repository Pattern](https://laravel.com/docs/eloquent-repositories)
- [Laravel Events and Listeners](https://laravel.com/docs/events)
- [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
- [Laravel Policies](https://laravel.com/docs/authorization#creating-policies)
