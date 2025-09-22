<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to' => 'required|exists:users,id',
            'note' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'Получатель чата обязателен',
            'assigned_to.exists' => 'Указанный пользователь не существует',
            'note.max' => 'Примечание не должно превышать 500 символов'
        ];
    }
}
