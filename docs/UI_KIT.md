# UI Kit - Компоненты интерфейса

## Обзор

UI Kit представляет собой набор переиспользуемых компонентов для создания единообразного интерфейса админ-панели. Все компоненты используют Tailwind CSS и следуют единому дизайн-системе.

## Компоненты

### 1. Input (`x-ui.input`)

Базовый компонент для текстовых полей ввода.

```blade
<x-ui.input 
    name="email" 
    label="Email адрес" 
    value="{{ old('email') }}"
    placeholder="Введите email"
    required
    help="Будет использоваться для входа в систему"
/>
```

**Параметры:**
- `name` - имя поля (обязательный)
- `label` - подпись поля
- `value` - значение по умолчанию
- `placeholder` - placeholder текст
- `required` - обязательное поле
- `disabled` - отключенное поле
- `readonly` - только для чтения
- `help` - текст помощи
- `error` - текст ошибки
- `size` - размер (sm, md, lg)
- `variant` - вариант (default, error, success)

### 2. Select (`x-ui.select`)

Компонент для выпадающих списков.

```blade
<x-ui.select 
    name="role" 
    label="Роль пользователя"
    placeholder="Выберите роль"
    :options="[
        'admin' => 'Администратор',
        'user' => 'Пользователь',
        'manager' => 'Менеджер'
    ]"
    :selected="old('role', $user->role)"
    required
/>
```

**Параметры:**
- `name` - имя поля (обязательный)
- `label` - подпись поля
- `options` - массив опций [value => label]
- `selected` - выбранное значение
- `placeholder` - placeholder текст
- `required` - обязательное поле
- `disabled` - отключенное поле
- `help` - текст помощи
- `error` - текст ошибки
- `size` - размер (sm, md, lg)

### 3. Textarea (`x-ui.textarea`)

Компонент для многострочного текста.

```blade
<x-ui.textarea 
    name="description" 
    label="Описание"
    value="{{ old('description') }}"
    placeholder="Введите описание..."
    rows="4"
    help="Максимум 500 символов"
/>
```

**Параметры:**
- `name` - имя поля (обязательный)
- `label` - подпись поля
- `value` - значение по умолчанию
- `placeholder` - placeholder текст
- `rows` - количество строк
- `required` - обязательное поле
- `disabled` - отключенное поле
- `readonly` - только для чтения
- `help` - текст помощи
- `error` - текст ошибки
- `size` - размер (sm, md, lg)

### 4. Checkbox (`x-ui.checkbox`)

Компонент для чекбоксов.

```blade
<x-ui.checkbox 
    name="is_active" 
    label="Активный пользователь"
    checked="{{ $user->is_active }}"
    help="Пользователь сможет войти в систему"
/>
```

**Параметры:**
- `name` - имя поля (обязательный)
- `label` - подпись поля
- `value` - значение (по умолчанию "1")
- `checked` - отмечен ли чекбокс
- `disabled` - отключенное поле
- `help` - текст помощи
- `error` - текст ошибки

### 5. Radio (`x-ui.radio`)

Компонент для радиокнопок.

```blade
<x-ui.radio 
    name="status" 
    value="active"
    label="Активный"
    checked="{{ $user->status === 'active' }}"
/>
```

**Параметры:**
- `name` - имя поля (обязательный)
- `value` - значение (обязательный)
- `label` - подпись поля
- `checked` - отмечена ли кнопка
- `disabled` - отключенное поле
- `help` - текст помощи
- `error` - текст ошибки

## Базовые компоненты

### Button (`x-base.button`)

```blade
<x-base.button variant="primary" size="md">
    Сохранить
</x-base.button>

<x-base.button href="{{ route('admin.users.index') }}" variant="secondary">
    Назад
</x-base.button>
```

**Варианты:**
- `primary` - основная кнопка (зеленая)
- `secondary` - вторичная кнопка (серая)
- `danger` - опасная кнопка (красная)
- `success` - успешная кнопка (зеленая)
- `outline` - контурная кнопка
- `ghost` - призрачная кнопка

**Размеры:**
- `sm` - маленькая
- `md` - средняя (по умолчанию)
- `lg` - большая

### Card (`x-card`)

```blade
<x-card>
    <h2 class="text-lg font-medium">Заголовок карточки</h2>
    <p class="text-gray-600">Содержимое карточки</p>
</x-card>
```

### Badge (`x-badge`)

```blade
<x-badge variant="success">Активен</x-badge>
<x-badge variant="danger">Неактивен</x-badge>
```

**Варианты:**
- `primary` - основной (зеленый)
- `secondary` - вторичный (серый)
- `success` - успех (зеленый)
- `danger` - опасность (красный)
- `warning` - предупреждение (желтый)
- `info` - информация (синий)

### Alert (`x-alert`)

```blade
<x-alert type="success" message="Операция выполнена успешно" />
<x-alert type="error" message="Произошла ошибка" />
```

**Типы:**
- `success` - успех
- `error` - ошибка
- `warning` - предупреждение
- `info` - информация

## Использование в формах

### Пример формы создания пользователя

```blade
<x-layout>
    <x-slot name="title">Создание пользователя</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Создание пользователя</h1>
            <x-base.button href="{{ route('admin.users.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-card>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-ui.input 
                        name="name" 
                        label="Имя пользователя" 
                        value="{{ old('name') }}" 
                        required
                    />
                    
                    <x-ui.input 
                        name="email" 
                        label="Email" 
                        type="email"
                        value="{{ old('email') }}" 
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-ui.select 
                        name="role" 
                        label="Роль"
                        :options="[
                            'admin' => 'Администратор',
                            'user' => 'Пользователь'
                        ]"
                        :selected="old('role')"
                        required
                    />
                    
                    <x-ui.checkbox 
                        name="is_active" 
                        label="Активный пользователь"
                        checked="{{ old('is_active', true) }}"
                    />
                </div>

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.users.index') }}" variant="secondary">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Создать пользователя
                    </x-base.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layout>
```

## Стилизация

Все компоненты используют единую цветовую схему:

- **Основной цвет**: `green-600` (зеленый)
- **Фокус**: `green-500` (зеленый)
- **Ошибки**: `red-600` (красный)
- **Успех**: `green-600` (зеленый)
- **Предупреждения**: `yellow-600` (желтый)
- **Информация**: `blue-600` (синий)

## Адаптивность

Все компоненты адаптивны и корректно отображаются на различных устройствах:

- **Мобильные устройства**: одноколоночная сетка
- **Планшеты**: двухколоночная сетка
- **Десктоп**: многоколоночная сетка

## Доступность

Компоненты включают базовые элементы доступности:

- Правильные `label` и `id` атрибуты
- ARIA атрибуты где необходимо
- Поддержка клавиатурной навигации
- Контрастные цвета
