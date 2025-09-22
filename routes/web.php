<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Все маршруты перенесены в API
|
*/

// Главная страница - информация об API
Route::get('/', function () {
    return response()->json([
        'message' => 'Chat AP.KZ - Backend API',
        'version' => '1.0.0',
        'status' => 'active',
        'description' => 'Это API для системы чатов. Используйте /api для доступа к эндпоинтам.',
        'api_endpoints' => [
            'auth' => '/api/auth',
            'chats' => '/api/chats',
            'messages' => '/api/messages',
            'webhooks' => '/api/webhooks'
        ],
        'documentation' => 'API документация доступна по адресу /api',
        'note' => 'Этот бэкенд предназначен для работы с Vue.js фронтендом'
    ]);
});

// Маршрут для перенаправления при неавторизованном доступе
Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthorized access',
        'error' => 'Please authenticate first'
    ], 401);
})->name('login');

