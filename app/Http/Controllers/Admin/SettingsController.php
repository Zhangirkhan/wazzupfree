<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Services\Wazzup24Service;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'wazzup24' => [
                'api_key' => config('wazzup24.api_key'),
                'channel_id' => config('wazzup24.channel_id'),
                'webhook_url' => config('wazzup24.webhook_url'),
                'enabled' => config('wazzup24.enabled', false),
            ],
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone', 'Asia/Almaty'), // UTC+5
                'locale' => config('app.locale', 'ru'), // Русский язык
            ],
            'mail' => [
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
            'chat' => [
                'inactive_days' => config('chat.inactive_days', 7),
                'max_message_length' => config('chat.max_message_length', 1000),
                'auto_close_enabled' => config('chat.auto_close_enabled', true),
            ]
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wazzup24.api_key' => 'nullable|string',
            'wazzup24.channel_id' => 'nullable|string',
            'wazzup24.webhook_url' => 'nullable|url',
            'wazzup24.enabled' => 'boolean',
            'chat.inactive_days' => 'integer|min:1|max:30',
            'chat.max_message_length' => 'integer|min:100|max:5000',
            'chat.auto_close_enabled' => 'boolean',
        ]);

        // Обновляем настройки Wazzup24
        if ($request->has('wazzup24.api_key')) {
            $this->updateConfig('wazzup24.api_key', $request->input('wazzup24.api_key'));
        }
        
        if ($request->has('wazzup24.channel_id')) {
            $this->updateConfig('wazzup24.channel_id', $request->input('wazzup24.channel_id'));
        }
        
        if ($request->has('wazzup24.webhook_url')) {
            $this->updateConfig('wazzup24.webhook_url', $request->input('wazzup24.webhook_url'));
        }
        
        if ($request->has('wazzup24.enabled')) {
            $this->updateConfig('wazzup24.enabled', $request->boolean('wazzup24.enabled'));
        }

        // Обновляем настройки чата
        if ($request->has('chat.inactive_days')) {
            $this->updateConfig('chat.inactive_days', $request->input('chat.inactive_days'));
        }
        
        if ($request->has('chat.max_message_length')) {
            $this->updateConfig('chat.max_message_length', $request->input('chat.max_message_length'));
        }
        
        if ($request->has('chat.auto_close_enabled')) {
            $this->updateConfig('chat.auto_close_enabled', $request->boolean('chat.auto_close_enabled'));
        }

        // Очищаем кэш конфигурации
        Cache::forget('config');
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Настройки успешно обновлены');
    }

    public function toggleIntegration(Request $request)
    {
        $request->validate([
            'integration' => 'required|string',
            'enabled' => 'required|boolean',
            'password' => 'required_if:enabled,false|string',
        ]);

        $integration = $request->input('integration');
        $enabled = $request->boolean('enabled');

        // Если отключаем интеграцию, проверяем пароль
        if (!$enabled) {
            $password = $request->input('password');
            
            if (!Hash::check($password, auth()->user()->password)) {
                return response()->json([
                    'error' => 'invalid_password',
                    'message' => 'Неверный пароль'
                ], 400);
            }
        }

        // Обновляем статус интеграции
        $this->updateConfig("{$integration}.enabled", $enabled);

        // Очищаем кэш конфигурации
        Cache::forget('config');

        return response()->json([
            'success' => true,
            'message' => $enabled ? 'Интеграция включена' : 'Интеграция отключена'
        ]);
    }

    public function testWazzupConnection(Request $request)
    {
        try {
            $wazzupService = new Wazzup24Service();
            
            // Проверяем подключение
            $connectionTest = $wazzupService->testConnection();
            
            if (!$connectionTest['success']) {
                return response()->json([
                    'error' => true,
                    'message' => $connectionTest['error']
                ], 400);
            }

            // Получаем список каналов
            $channelsResult = $wazzupService->getChannels();
            
            if (!$channelsResult['success']) {
                return response()->json([
                    'error' => true,
                    'message' => $channelsResult['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'channels' => $channelsResult['channels']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ошибка при проверке подключения: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateConfig($key, $value)
    {
        // В реальном проекте здесь можно сохранять в базу данных
        // или использовать пакет для управления настройками
        config([$key => $value]);
    }
}
