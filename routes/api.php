<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatTransferController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\Wazzup24Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    

    
    // Chat transfer routes
    Route::post('/chats/{chat}/transfer', [ChatTransferController::class, 'transfer']);
    Route::get('/chats/{chat}/transfer-history', [ChatTransferController::class, 'history']);
    
    // Message routes
    Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
    Route::post('/messages/{message}/hide', [MessageController::class, 'hide']);
    Route::post('/chats/{chat}/system-message', [MessageController::class, 'sendSystemMessage']);
    
    // Organization routes
    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::get('/organizations/{organization}/departments', [OrganizationController::class, 'departments']);
    Route::get('/organizations/{organization}/roles', [OrganizationController::class, 'roles']);
    Route::get('/organizations/{organization}/users', [OrganizationController::class, 'users']);
    
    // Wazzup24 API routes
    Route::post('/chats/{chat}/wazzup24/send', [Wazzup24Controller::class, 'sendMessage']);
    Route::get('/wazzup24/connection', [Wazzup24Controller::class, 'checkConnection']);
    Route::get('/chats/{chat}/wazzup24/info', [Wazzup24Controller::class, 'getChatInfo']);
});

// Wazzup24 Webhook (без аутентификации)
Route::match(['GET', 'POST'], '/webhook', [WebhookController::class, 'wazzup24']);
