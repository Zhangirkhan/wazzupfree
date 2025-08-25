# Компоненты Blade

## Структура компонентов

### 📁 Базовые компоненты (папка base/)
**Простые UI элементы, которые используются как строительные блоки**
- `x-base.button` - кнопки с различными вариантами стилей
- `x-base.card` - карточки с заголовком и действиями
- `x-base.badge` - бейджи для статусов
- `x-base.input` - поля ввода с поддержкой темной темы
- `x-base.select` - выпадающие списки
- `x-base.textarea` - многострочные поля ввода
- `x-base.radio` - радиокнопки
- `x-base.checkbox` - чекбоксы
- `x-base.alert` - уведомления с различными типами

### 📁 Навигационные компоненты (папка navigation/)
**Компоненты для навигации и структуры страниц**
- `x-navigation.layout` - основной макет приложения
- `x-navigation.app` - макет приложения с сайдбаром
- `x-navigation.header` - шапка с поиском и навигацией
- `x-navigation.sidebar` - боковая панель навигации
- `x-navigation.mobile-sidebar` - мобильная боковая панель
- `x-navigation.nav-item` - элемент навигации
- `x-navigation.notifications-dropdown` - выпадающее меню уведомлений

### 📁 Компоненты данных (папка data/)
**Компоненты для отображения и работы с данными**
- `x-data.table` - таблицы с сортировкой и темной темой

### 📁 Компоненты обратной связи (папка feedback/)
**Компоненты для отображения сообщений и уведомлений**
- `x-feedback.alert` - уведомления с различными типами

### 📁 Компоненты настроек (папка settings/)
**Компоненты для управления настройками системы**
- `x-settings.theme-toggle` - переключатель темы
- `x-settings.integration-toggle` - переключатель интеграций

### 📁 Составные компоненты (папка composite/)
**Сложные компоненты, состоящие из нескольких базовых**
- *(пока пусто, для будущих составных компонентов)*

## Правила использования

### ✅ Правильно
- Используйте `x-base.input` для полей ввода
- Используйте `x-base.button` для кнопок
- Используйте `x-base.card` для карточек
- Используйте `x-feedback.alert` для уведомлений
- Используйте `x-navigation.layout` для основного макета

### ❌ Неправильно
- Не создавайте дублирующиеся компоненты
- Не используйте старые компоненты `x-input` (удален)
- Не создавайте компоненты без проверки существующих

## Добавление новых компонентов

1. **Определите тип компонента:**
   - `base/` - простые UI элементы
   - `navigation/` - навигация и структура
   - `data/` - работа с данными
   - `feedback/` - уведомления и сообщения
   - `settings/` - настройки системы
   - `composite/` - сложные составные компоненты

2. **Проверьте, нет ли уже похожего компонента**
3. **Создавайте компонент в соответствующей папке**
4. **Добавьте поддержку темной темы**
5. **Обновите эту документацию**
6. **Протестируйте компонент**

## Переиспользуемые формы

### Формы для админки (автоматические компоненты)
Формы находятся в соответствующих папках и автоматически регистрируются Laravel:
- `x-admin.users.form` - форма пользователя (`resources/views/admin/users/_form.blade.php`)
- `x-admin.organizations.form` - форма организации (`resources/views/admin/organizations/_form.blade.php`)
- `x-admin.departments.form` - форма отдела (`resources/views/admin/departments/_form.blade.php`)
- `x-admin.positions.form` - форма должности (`resources/views/admin/positions/_form.blade.php`)

### Использование форм:
```blade
<!-- Для создания -->
<x-admin.users.form 
    :organizations="$organizations"
    :departments="$departments"
    :roles="$roles"
    :isEdit="false"
/>

<!-- Для редактирования -->
<x-admin.users.form 
    :user="$user"
    :organizations="$organizations"
    :departments="$departments"
    :roles="$roles"
    :isEdit="true"
/>
```

## Поддержка темной темы

Все компоненты должны поддерживать темную тему с использованием классов `dark:`:
- `dark:bg-gray-800` для фонов
- `dark:text-white` для текста
- `dark:border-gray-600` для границ
- `dark:focus:ring-green-400` для фокуса

## Примеры использования

### Базовые компоненты:
```blade
<x-base.button variant="primary" size="md">
    Нажми меня
</x-base.button>

<x-base.input name="email" label="Email" type="email" required />

<x-base.card title="Заголовок карточки">
    Содержимое карточки
</x-base.card>
```

### Навигационные компоненты:
```blade
<x-navigation.layout>
    <x-navigation.header />
    <x-navigation.sidebar />
    <!-- Содержимое страницы -->
</x-navigation.layout>
```

### Компоненты данных:
```blade
<x-data.table :headers="$headers" :sortable="true">
    <!-- Строки таблицы -->
</x-data.table>
```

### Компоненты обратной связи:
```blade
<x-feedback.alert type="success" message="Операция выполнена успешно!" />
```

### Компоненты настроек:
```blade
<x-settings.theme-toggle />
<x-settings.integration-toggle />
```
