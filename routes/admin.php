<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Admin\ChatTransferController;
use App\Http\Controllers\Admin\ProfileController;

Route::middleware(['auth'])->group(function () {
    // Главная страница админки - редирект на dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin.index');

    // Dashboard - доступен всем авторизованным
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Пользователи - только админы
    Route::middleware(['permission:users'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Отделы - только админы
    Route::middleware(['permission:departments'])->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    // Чаты - только админы
    Route::middleware(['permission:chats'])->group(function () {
        Route::resource('chats', ChatController::class);
        Route::post('/chats/{chat}/transfer', [ChatController::class, 'transfer'])->name('chats.transfer');
        Route::post('/chats/{chat}/close', [ChatController::class, 'close'])->name('chats.close');
        Route::post('/chats/{chat}/accept', [ChatController::class, 'accept'])->name('chats.accept');
        Route::post('/chats/{chat}/reject', [ChatController::class, 'reject'])->name('chats.reject');
        Route::post('/chats/bulk-accept', [ChatController::class, 'bulkAccept'])->name('chats.bulk-accept');
        Route::get('/chats/export', [ChatController::class, 'export'])->name('chats.export');
    });

    // Организации - только админы
    Route::middleware(['permission:organizations'])->group(function () {
        Route::resource('organizations', OrganizationController::class);
    });

    // Должности - только админы
    Route::middleware(['permission:positions'])->group(function () {
        Route::resource('positions', PositionController::class);
    });

    // Клиенты - доступны менеджерам и сотрудникам
    Route::middleware(['permission:clients'])->group(function () {
        Route::resource('clients', ClientController::class);
        Route::get('/clients/wazzup/preview', [ClientController::class, 'previewWazzupClients'])->name('clients.wazzup.preview');
        Route::post('/clients/wazzup/import', [ClientController::class, 'importFromWazzup'])->name('clients.wazzup.import');
        Route::post('/clients/{client}/start-chat', [ClientController::class, 'startChat'])->name('clients.start-chat');
    });

    // Настройки - только админы
    Route::middleware(['permission:settings'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/toggle-integration', [SettingsController::class, 'toggleIntegration'])->name('settings.toggle-integration');
        Route::post('/settings/test-wazzup-connection', [SettingsController::class, 'testWazzupConnection'])->name('settings.test-wazzup-connection');
    });



    // Передача чатов - доступна менеджерам и админам
    Route::middleware(['permission:messenger'])->prefix('chat-transfer')->name('chat-transfer.')->group(function () {
        Route::get('/{chat}/form', [ChatTransferController::class, 'showTransferForm'])->name('form');
        Route::post('/{chat}/to-department', [ChatTransferController::class, 'transferToDepartment'])->name('to-department');
        Route::post('/{chat}/to-user', [ChatTransferController::class, 'transferToUser'])->name('to-user');
        Route::post('/bulk', [ChatTransferController::class, 'bulkTransfer'])->name('bulk');
        Route::get('/{chat}/history', [ChatTransferController::class, 'transferHistory'])->name('history');
        Route::get('/departments', [ChatTransferController::class, 'getAvailableDepartments'])->name('departments');
        Route::get('/managers', [ChatTransferController::class, 'getAvailableManagers'])->name('managers');
    });

    // Profile - доступен всем авторизованным
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        
        // Для админов - управление профилями других пользователей
        Route::middleware(['permission:users'])->group(function () {
            Route::get('/user/{user}', [ProfileController::class, 'showUser'])->name('show-user');
            Route::get('/user/{user}/edit', [ProfileController::class, 'editUser'])->name('edit-user');
            Route::put('/user/{user}/update', [ProfileController::class, 'updateUser'])->name('update-user');
        });
    });
});
