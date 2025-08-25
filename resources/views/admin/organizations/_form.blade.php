@props([
    'organization' => null,
    'isEdit' => false
])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="name" 
            label="Название организации" 
            value="{{ old('name', $organization?->name) }}" 
            required
        />
        
        <x-base.input 
            name="slug" 
            label="Slug (URL)" 
            value="{{ old('slug', $organization?->slug) }}"
            help="Автоматически генерируется из названия"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="domain" 
            label="Домен" 
            value="{{ old('domain', $organization?->domain) }}"
            help="example.com"
        />
        
        <x-base.select 
            name="is_active" 
            label="Статус"
            :options="[
                1 => 'Активна',
                0 => 'Неактивна'
            ]"
            :selected="old('is_active', $organization?->is_active ?? 1)"
        />
    </div>

    <div>
        <x-base.textarea 
            name="description" 
            label="Описание"
            value="{{ old('description', $organization?->description) }}"
            placeholder="Описание организации..."
            rows="4"
        />
    </div>
</div>
