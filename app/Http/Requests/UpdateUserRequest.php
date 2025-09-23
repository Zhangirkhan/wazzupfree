<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($userId), 'max:255'],
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'role' => ['sometimes', Rule::in(['admin', 'manager', 'employee'])],
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.email' => 'Некорректный email адрес',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'role.in' => 'Недопустимая роль',
            'department_id.exists' => 'Указанный отдел не существует'
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
            'department_id' => 'отдел'
        ];
    }
}
