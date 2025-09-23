<?php

namespace App\Http\Requests;

use App\Models\Chat;
use Illuminate\Validation\Rule;

class CreateChatRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create', Chat::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'type' => ['required', Rule::in(['support', 'sales', 'general'])],
            'status' => ['sometimes', Rule::in(['active', 'pending', 'closed'])],
            'is_messenger_chat' => 'boolean',
            'messenger_phone' => 'nullable|string|max:20',
            'messenger_status' => 'nullable|string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название чата обязательно',
            'title.max' => 'Название не должно превышать 255 символов',
            'client_id.required' => 'Клиент обязателен',
            'client_id.exists' => 'Указанный клиент не существует',
            'department_id.exists' => 'Указанный отдел не существует',
            'assigned_to.exists' => 'Указанный пользователь не существует',
            'type.required' => 'Тип чата обязателен',
            'type.in' => 'Недопустимый тип чата',
            'status.in' => 'Недопустимый статус чата'
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'название',
            'client_id' => 'клиент',
            'department_id' => 'отдел',
            'assigned_to' => 'назначенный пользователь',
            'type' => 'тип',
            'status' => 'статус'
        ];
    }
}