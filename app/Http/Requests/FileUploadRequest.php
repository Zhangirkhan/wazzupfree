<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,mp4',
                'mimetypes:image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,video/mp4'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Файл обязателен для загрузки',
            'file.file' => 'Загруженный объект должен быть файлом',
            'file.max' => 'Размер файла не должен превышать 10MB',
            'file.mimes' => 'Недопустимый тип файла. Разрешены: jpg, jpeg, png, gif, pdf, doc, docx, txt, mp4, mp3, wav',
            'file.mimetypes' => 'Недопустимый MIME-тип файла'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');

                // Проверка на вирусы (базовая проверка расширения)
                $extension = strtolower($file->getClientOriginalExtension());
                $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];

                if (in_array($extension, $dangerousExtensions)) {
                    $validator->errors()->add('file', 'Загрузка исполняемых файлов запрещена');
                }

                // Проверка размера изображений
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $imageSize = getimagesize($file->getPathname());
                    if ($imageSize && ($imageSize[0] > 5000 || $imageSize[1] > 5000)) {
                        $validator->errors()->add('file', 'Размер изображения не должен превышать 5000x5000 пикселей');
                    }
                }
            }
        });
    }
}
