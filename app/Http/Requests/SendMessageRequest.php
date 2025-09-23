<?php

namespace App\Http\Requests;

use App\Models\Message;
use Illuminate\Validation\Rule;

class SendMessageRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create', Message::class);
    }

    public function rules(): array
    {
        return [
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required|string|max:5000',
            'type' => ['required', Rule::in(['text', 'image', 'video', 'audio', 'document', 'sticker'])],
            'is_from_client' => 'boolean',
            'metadata' => 'nullable|array',
            'messenger_id' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'chat_id.required' => 'ID чата обязателен',
            'chat_id.exists' => 'Указанный чат не существует',
            'content.required' => 'Содержимое сообщения обязательно',
            'content.max' => 'Сообщение не должно превышать 5000 символов',
            'type.required' => 'Тип сообщения обязателен',
            'type.in' => 'Недопустимый тип сообщения',
            'metadata.array' => 'Метаданные должны быть массивом'
        ];
    }

    public function attributes(): array
    {
        return [
            'chat_id' => 'ID чата',
            'content' => 'содержимое',
            'type' => 'тип',
            'metadata' => 'метаданные'
        ];
    }
}