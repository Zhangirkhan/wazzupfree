@props([
    'department' => null,
    'organizations' => collect(),
    'isEdit' => false
])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="name" 
            label="Название отдела" 
            value="{{ old('name', $department?->name) }}" 
            required
        />
        
        <x-base.select 
            name="organization_id" 
            label="Организация"
            placeholder="Выберите организацию"
            :options="$organizations->pluck('name', 'id')->toArray()"
            :selected="old('organization_id', $department?->organization_id)"
            required
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="slug" 
            label="Slug (URL)" 
            value="{{ old('slug', $department?->slug) }}"
            help="Автоматически генерируется из названия"
        />
        
        <x-base.select 
            name="is_active" 
            label="Статус"
            :options="[
                1 => 'Активен',
                0 => 'Неактивен'
            ]"
            :selected="old('is_active', $department?->is_active ?? 1)"
        />
    </div>

    <div>
        <x-base.textarea 
            name="description" 
            label="Описание"
            value="{{ old('description', $department?->description) }}"
            placeholder="Описание отдела..."
            rows="4"
        />
    </div>
</div>
