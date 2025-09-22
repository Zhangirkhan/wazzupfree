<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'nullable|string',
            'type' => 'in:text,image,video,file',
            'file' => [
                'nullable',
                'file',
                'max:10240', // 10MB max
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,csv,json,xml,zip,rar,7z,mp4,mov,avi,mkv',
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain,text/csv,application/json,application/xml,text/xml,application/zip,application/x-rar-compressed,application/x-7z-compressed,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska'
            ]
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $message = $this->input('message');
            $hasFile = $this->hasFile('file');

            if (empty($message) && !$hasFile) {
                $validator->errors()->add('message', 'Необходимо указать сообщение или прикрепить файл');
            }
        });
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Сообщение обязательно',
            'type.in' => 'Недопустимый тип сообщения',
            'file.file' => 'Файл должен быть загружен',
            'file.max' => 'Размер файла не должен превышать 10MB'
        ];
    }
}
