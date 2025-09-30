<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class SystemMessageService
{
    /**
     * Получить версию системных сообщений
     */
    public function getVersion(): string
    {
        return Config::get('system_messages.version', '1.0.0');
    }

    /**
     * Получить язык по умолчанию
     */
    public function getDefaultLanguage(): string
    {
        return Config::get('system_messages.default_language', 'ru');
    }

    /**
     * Получить текст системного сообщения
     * 
     * @param string $key Ключ сообщения
     * @param array $params Параметры для подстановки в шаблон
     * @param string|null $language Язык (если null, используется язык по умолчанию)
     * @return string
     */
    public function getMessage(string $key, array $params = [], ?string $language = null): string
    {
        $language = $language ?? $this->getDefaultLanguage();
        
        // Получаем шаблон сообщения
        $template = Config::get("system_messages.messages.{$language}.{$key}");
        
        // Если сообщение не найдено для текущего языка, пытаемся получить на языке по умолчанию
        if (!$template && $language !== $this->getDefaultLanguage()) {
            $template = Config::get("system_messages.messages.{$this->getDefaultLanguage()}.{$key}");
        }
        
        // Если сообщение все еще не найдено, возвращаем ключ
        if (!$template) {
            return $key;
        }
        
        // Заменяем параметры в шаблоне
        foreach ($params as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        
        return $template;
    }

    /**
     * Получить приветственное меню
     * 
     * @param array|\Illuminate\Support\Collection $departments Массив или коллекция отделов
     * @param string|null $clientName Имя клиента
     * @param string|null $language Язык
     * @return string
     */
    public function getWelcomeMenu($departments, ?string $clientName = null, ?string $language = null): string
    {
        $language = $language ?? $this->getDefaultLanguage();
        
        // Приветствие
        if (!empty($clientName)) {
            $text = $this->getMessage('welcome_with_name', ['name' => $clientName], $language) . "\n\n";
        } else {
            $text = $this->getMessage('welcome_without_name', [], $language) . "\n\n";
        }
        
        // Вопрос о выборе отдела
        $text .= $this->getMessage('select_department', [], $language) . "\n\n";
        
        // Список отделов
        foreach ($departments as $index => $department) {
            $number = $index + 1;
            $departmentName = $department['name'] ?? $department->name ?? "Отдел {$number}";
            $text .= "{$number}. {$departmentName}\n";
        }
        
        // Подсказка по возврату в меню
        $text .= "\n" . $this->getMessage('return_to_menu', [], $language);
        
        return $text;
    }

    /**
     * Получить метаданные системного сообщения с версией
     * 
     * @param string $messageType Тип сообщения
     * @param string|null $language Язык
     * @return array
     */
    public function getMessageMetadata(string $messageType, ?string $language = null): array
    {
        $language = $language ?? $this->getDefaultLanguage();
        
        return [
            'direction' => 'outgoing',
            'is_bot_message' => true,
            'sender' => 'Система',
            'system_message_version' => $this->getVersion(),
            'message_type' => $messageType,
            'language' => $language,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Проверить, поддерживается ли язык
     * 
     * @param string $language Код языка
     * @return bool
     */
    public function isLanguageSupported(string $language): bool
    {
        $supportedLanguages = Config::get('system_messages.supported_languages', ['ru']);
        return in_array($language, $supportedLanguages);
    }

    /**
     * Получить список поддерживаемых языков
     * 
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return Config::get('system_messages.supported_languages', ['ru']);
    }
}
