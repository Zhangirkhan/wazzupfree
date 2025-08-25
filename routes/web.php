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
    Route::get('/messages/{chatId}', [App\Http\Controllers\UserChatController::class, 'getMessages'])->name('messages');
    Route::delete('/messages/{messageId}', [App\Http\Controllers\UserChatController::class, 'deleteMessage'])->name('delete-message');
});





// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});
