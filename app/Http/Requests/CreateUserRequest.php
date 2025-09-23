<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;

class CreateUserRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create', User::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'role' => ['required', Rule::in(['admin', 'manager', 'employee'])],
            'department_id' => 'nullable|exists:departments,id',
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя пользователя обязательно',
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.required' => 'Email обязателен',
            'email.email' => 'Некорректный email адрес',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Пароль обязателен',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'role.required' => 'Роль обязательна',
            'role.in' => 'Недопустимая роль',
            'department_id.exists' => 'Указанный отдел не существует',
            'organization_id.required' => 'Организация обязательна',
            'organization_id.exists' => 'Указанная организация не существует'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'имя',
            'email' => 'email',
            'password' => 'пароль',
            'phone' => 'телефон',
            'position' => 'должность',
            'role' => 'роль',
            'department_id' => 'отдел',
            'organization_id' => 'организация'
        ];
    }
}
