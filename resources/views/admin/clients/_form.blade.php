@props([
    'client' => null,
    'isEdit' => false
])

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="name" 
            label="Имя клиента" 
            value="{{ old('name', $client?->name) }}" 
            required
        />
        
        <x-base.input 
            name="phone" 
            label="Номер телефона" 
            value="{{ old('phone', $client?->phone) }}"
            placeholder="+7 (999) 123-45-67"
            required
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-base.input 
            name="uuid_wazzup" 
            label="UUID Wazzup" 
            value="{{ old('uuid_wazzup', $client?->uuid_wazzup) }}"
            placeholder="UUID из Wazzup24"
            help="Уникальный идентификатор клиента в Wazzup24"
        />
        
        <x-base.select 
            name="is_active" 
            label="Статус"
            :options="[
                1 => 'Активен',
                0 => 'Неактивен'
            ]"
            :selected="old('is_active', $client?->is_active ?? 1)"
        />
    </div>

    <div>
        <x-base.textarea 
            name="comment" 
            label="Комментарий"
            value="{{ old('comment', $client?->comment) }}"
            placeholder="Дополнительная информация о клиенте..."
            rows="4"
        />
    </div>
</div>
