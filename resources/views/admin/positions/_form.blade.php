@props([
    'position' => null,
    'isEdit' => false
])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="name" 
            label="Название должности" 
            value="{{ old('name', $position?->name) }}" 
            required
            placeholder="Например: Менеджер по продажам"
        />
        
        <x-base.input 
            name="sort_order" 
            label="Порядок сортировки" 
            type="number"
            value="{{ old('sort_order', $position?->sort_order ?? 0) }}"
            help="Чем меньше число, тем выше в списке"
        />
    </div>

    <div>
        <x-base.textarea 
            name="description" 
            label="Описание"
            value="{{ old('description', $position?->description) }}"
            placeholder="Описание должности и обязанностей..."
            rows="4"
        />
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Права доступа
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $permissions = [
                    'users.view' => 'Просмотр пользователей',
                    'users.create' => 'Создание пользователей',
                    'users.edit' => 'Редактирование пользователей',
                    'users.delete' => 'Удаление пользователей',
                    'organizations.view' => 'Просмотр организаций',
                    'organizations.create' => 'Создание организаций',
                    'organizations.edit' => 'Редактирование организаций',
                    'organizations.delete' => 'Удаление организаций',
                    'departments.view' => 'Просмотр отделов',
                    'departments.create' => 'Создание отделов',
                    'departments.edit' => 'Редактирование отделов',
                    'departments.delete' => 'Удаление отделов',
                    'positions.view' => 'Просмотр должностей',
                    'positions.create' => 'Создание должностей',
                    'positions.edit' => 'Редактирование должностей',
                    'positions.delete' => 'Удаление должностей',
                    'chats.view' => 'Просмотр чатов',
                    'chats.create' => 'Создание чатов',
                    'chats.edit' => 'Редактирование чатов',
                    'chats.delete' => 'Удаление чатов',
                    'settings.view' => 'Просмотр настроек',
                    'settings.edit' => 'Редактирование настроек',
                ];
                $currentPermissions = old('permissions', $position?->permissions ?? []);
            @endphp
            
            @foreach($permissions as $permission => $label)
                <x-base.checkbox 
                    name="permissions[]" 
                    value="{{ $permission }}"
                    :checked="in_array($permission, $currentPermissions)"
                    label="{{ $label }}"
                />
            @endforeach
        </div>
    </div>

    <div>
        <x-base.checkbox 
            name="is_active" 
            value="1"
            :checked="old('is_active', $position?->is_active ?? true)"
            label="Активна"
        />
    </div>
</div>
