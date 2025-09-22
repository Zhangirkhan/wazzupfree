<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'message' => 'required|string',
            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Имя клиента обязательно',
            'client_name.max' => 'Имя клиента не должно превышать 255 символов',
            'client_phone.required' => 'Телефон клиента обязателен',
            'client_phone.max' => 'Телефон не должен превышать 20 символов',
            'client_email.email' => 'Некорректный email адрес',
            'client_email.max' => 'Email не должен превышать 255 символов',
            'message.required' => 'Сообщение обязательно',
            'department_id.exists' => 'Указанный отдел не существует'
        ];
    }
}
