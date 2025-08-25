@props([
    'user' => null,
    'organizations' => collect(),
    'departments' => collect(),
    'roles' => collect(),
    'isEdit' => false
])

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <!-- Name -->
        <x-base.input 
            name="name" 
            label="Имя"
            value="{{ old('name', $user?->name) }}"
            error="{{ $errors->first('name') }}"
            required
        />

        <!-- Email -->
        <x-base.input 
            type="email"
            name="email" 
            label="Email"
            value="{{ old('email', $user?->email) }}"
            error="{{ $errors->first('email') }}"
            required
        />

        <!-- Password (only for create) -->
        @if(!$isEdit)
            <x-base.input 
                type="password"
                name="password" 
                label="Пароль"
                error="{{ $errors->first('password') }}"
                required
            />

            <x-base.input 
                type="password"
                name="password_confirmation" 
                label="Подтверждение пароля"
                required
            />
        @endif

        <!-- Phone -->
        <x-base.input 
            name="phone" 
            label="Телефон"
            value="{{ old('phone', $user?->phone) }}"
            error="{{ $errors->first('phone') }}"
        />

        <!-- Position -->
        <x-base.input 
            name="position" 
            label="Должность"
            value="{{ old('position', $user?->position) }}"
            error="{{ $errors->first('position') }}"
        />

        <!-- Organization -->
        <x-base.select 
            name="organization_id" 
            label="Организация"
            placeholder="Выберите организацию"
            :options="$organizations->pluck('name', 'id')->toArray()"
            :selected="old('organization_id', $user?->organizations->first()?->id)"
            required
        />

        <!-- Department -->
        <x-base.select 
            name="department_id" 
            label="Отдел"
            placeholder="Выберите отдел"
            :options="$departments->pluck('name', 'id')->toArray()"
            :selected="old('department_id', $user?->organizations->first()?->pivot?->department_id)"
        />

        <!-- Role -->
        <x-base.select 
            name="role_id" 
            label="Роль"
            placeholder="Выберите роль"
            :options="$roles->pluck('name', 'id')->toArray()"
            :selected="old('role_id', $user?->organizations->first()?->pivot?->role_id)"
        />
    </div>
</div>
