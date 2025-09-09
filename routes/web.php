<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
});

// Auth routes для веб-интерфейса
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

// User Chat routes
Route::prefix('user/chat')->name('user.chat.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\UserChatController::class, 'index'])->name('index');
    Route::post('/search', [App\Http\Controllers\UserChatController::class, 'search'])->name('search');
    Route::get('/search-clients', [App\Http\Controllers\UserChatController::class, 'searchClients'])->name('search-clients');
    Route::post('/create', [App\Http\Controllers\UserChatController::class, 'createChat'])->name('create');
    Route::post('/send/{chatId}', [App\Http\Controllers\UserChatController::class, 'sendMessage'])->name('send');
    Route::post('/upload-image', [App\Http\Controllers\UserChatController::class, 'uploadImage'])->name('upload-image');
Route::post('/upload-video', [App\Http\Controllers\UserChatController::class, 'uploadVideo'])->name('upload-video');
    Route::get('/messages/{chatId}', [App\Http\Controllers\UserChatController::class, 'getMessages'])->name('messages');
    Route::delete('/messages/{messageId}', [App\Http\Controllers\UserChatController::class, 'deleteMessage'])->name('delete-message');
    Route::post('/end/{chatId}', [App\Http\Controllers\UserChatController::class, 'endChat'])->name('end');
    Route::post('/transfer/{chatId}', [App\Http\Controllers\UserChatController::class, 'transferChat'])->name('transfer');
    Route::get('/history/{chatId}', [App\Http\Controllers\ChatHistoryController::class, 'getHistory'])->name('history');
    Route::get('/stream/{chatId}', [App\Http\Controllers\ChatSSEController::class, 'stream'])->name('stream');
});

// User notifications routes
Route::prefix('user')->name('user.')->middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Test routes
Route::prefix('user/chat')->name('user.chat.')->middleware(['auth'])->group(function () {
    // Тестовая страница уведомлений
    Route::get('/test-notifications', function () {
        return view('test-notifications');
    })->name('test.notifications');

    // Тестовая страница переключения отделов
    Route::get('/test-transfer', function () {
        return view('test-transfer');
    })->name('test.transfer');
});





// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});
