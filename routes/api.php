<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatTransferController;
use App\Http\Controllers\OrganizationController as LegacyOrganizationController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\Wazzup24Controller;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\OrganizationWazzupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ContractorController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\MessageReadController;
use App\Http\Controllers\ChatStreamController;

/*
|--------------------------------------------------------------------------
| API Routes для Vue Frontend
|--------------------------------------------------------------------------
|
| API маршруты для взаимодействия с Vue.js фронтендом
| Все маршруты возвращают JSON ответы
|
*/

// API Info
Route::get('/', function () {
    return response()->json([
        'message' => 'Chat AP.KZ API',
        'version' => '1.0.0',
        'status' => 'active',
        'endpoints' => [
            'auth' => '/api/auth',
            'chats' => '/api/chats',
            'users' => '/api/users',
            'messages' => '/api/messages',
            'organizations' => '/api/organizations'
        ]
    ]);
});

// CSRF token для фронтенда (только для SPA, не для API)
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'message' => 'CSRF token retrieved'
    ]);
})->middleware('web');


// Простые API auth routes (без CSRF для мобильных приложений)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/refresh', [AuthApiController::class, 'refresh']);
});

// SPA auth routes (с CSRF защитой для веб-приложений)
Route::middleware(['web', \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class])->prefix('auth/spa')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/refresh', [AuthApiController::class, 'refresh']);
});

// Chat Stream routes (SSE) - без стандартной авторизации middleware
Route::prefix('chat-stream')->group(function () {
    Route::get('/{chatId}/stream', [ChatStreamController::class, 'stream']);
    Route::get('/{chatId}/status', [ChatStreamController::class, 'status']);
    Route::get('/notifications', [ChatStreamController::class, 'notifications']);
});

// Chats Stream route (SSE для списка чатов)
Route::get('/chats/stream', [ChatStreamController::class, 'chatsStream']);

// Protected routes (требуют аутентификации)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // User info
    Route::get('/user', function (Request $request) {
        try {
            $user = $request->user();

            // Загружаем связанные данные
            $user->load(['department', 'positions' => function($query) {
                $query->wherePivot('is_primary', true);
            }]);

            return response()->json([
                'user' => $user,
                'permissions' => $user->getAvailablePermissions(),
                'roles' => [$user->role],
                'has_position_and_department' => $user->hasPositionAndDepartment(),
                'primary_position' => $user->primaryPosition(),
                'department' => $user->department,
                'access_level' => $user->hasPositionAndDepartment() ? 'employee_with_position' : $user->role
            ]);
        } catch (\Exception $e) {
            \Log::error('User endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    });

    // Auth management
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::put('/password', [AuthApiController::class, 'changePassword']);
        Route::get('/stats', [AuthApiController::class, 'getStats']);
    });



    // Chat routes
    Route::prefix('chats')->group(function () {
        Route::get('/', [ChatApiController::class, 'index']);
        Route::post('/', [ChatApiController::class, 'store']);
        Route::get('/search', [ChatApiController::class, 'search']);
        Route::get('/{id}', [ChatApiController::class, 'show']);
        Route::post('/{chatId}/send', [ChatApiController::class, 'sendMessage'])->middleware('throttle:120,1');
        Route::get('/{chatId}/messages', [ChatApiController::class, 'getMessages']);
        Route::post('/{chatId}/end', [ChatApiController::class, 'endChat']);
        Route::post('/{chatId}/close', [ChatApiController::class, 'closeMessengerChat']);
        Route::post('/{chatId}/transfer', [ChatApiController::class, 'transferChat']);


        // Chat transfer routes
        Route::post('/{chat}/transfer', [ChatTransferController::class, 'transfer']);
        Route::get('/{chat}/transfer-history', [ChatTransferController::class, 'history']);

        // Chat read routes
        Route::post('/{chat}/mark-read', [MessageReadController::class, 'markChatAsRead']);
    });

    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/chats/{chat}', [MessageController::class, 'index']);
        Route::post('/chats/{chat}', [MessageController::class, 'store']);
        Route::post('/{message}/hide', [MessageController::class, 'hide']);
        Route::post('/chats/{chat}/system-message', [MessageController::class, 'sendSystemMessage']);

        // Message read routes
        Route::post('/{message}/mark-read', [MessageReadController::class, 'markAsRead']);
        Route::post('/mark-multiple-read', [MessageReadController::class, 'markMultipleAsRead']);
        Route::post('/read-status', [MessageReadController::class, 'getReadStatus']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    });

    // Organization Management routes
    Route::prefix('organizations')->group(function () {
        Route::get('/', [OrganizationController::class, 'index']);
        Route::post('/', [OrganizationController::class, 'store']);
        Route::get('/{id}', [OrganizationController::class, 'show']);
        Route::put('/{id}', [OrganizationController::class, 'update']);
        Route::delete('/{id}', [OrganizationController::class, 'destroy']);
        Route::get('/{id}/departments', [OrganizationController::class, 'departments']);
        Route::get('/{id}/users', [OrganizationController::class, 'users']);
        Route::get('/{id}/structure', [OrganizationController::class, 'structure']);

        // Legacy organization routes
        Route::get('/{organization}/roles', [LegacyOrganizationController::class, 'roles']);

        // Wazzup24 настройки для организаций
        Route::prefix('{organization}/wazzup24')->group(function () {
            Route::get('/settings', [OrganizationWazzupController::class, 'getSettings']);
            Route::put('/settings', [OrganizationWazzupController::class, 'updateSettings']);
            Route::post('/test-connection', [OrganizationWazzupController::class, 'testConnection']);
            Route::get('/channels', [OrganizationWazzupController::class, 'getChannels']);
            Route::post('/setup-webhooks', [OrganizationWazzupController::class, 'setupWebhooks']);
            Route::get('/clients', [OrganizationWazzupController::class, 'getClients']);
            Route::post('/send-message', [OrganizationWazzupController::class, 'sendMessage']);
        });
    });

    // Department Management routes
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{id}', [DepartmentController::class, 'show']);
        Route::put('/{id}', [DepartmentController::class, 'update']);
        Route::delete('/{id}', [DepartmentController::class, 'destroy']);
        Route::get('/{id}/users', [DepartmentController::class, 'users']);
        Route::get('/{id}/supervisors', [DepartmentController::class, 'supervisors']);
        Route::get('/{id}/managers', [DepartmentController::class, 'managers']);
    });

    // Department Supervisors routes
    Route::prefix('department-supervisors')->group(function () {
        Route::post('/', [DepartmentController::class, 'assignSupervisor']);
        Route::delete('/', [DepartmentController::class, 'removeSupervisor']);
    });

    // Department Managers routes
    Route::prefix('department-managers')->group(function () {
        Route::post('/', [DepartmentController::class, 'assignManager']);
        Route::delete('/', [DepartmentController::class, 'removeManager']);
    });

    // Position Management routes
    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionController::class, 'index']);
        Route::post('/', [PositionController::class, 'store']);
        Route::get('/{id}', [PositionController::class, 'show']);
        Route::put('/{id}', [PositionController::class, 'update']);
        Route::delete('/{id}', [PositionController::class, 'destroy']);
    });

    // Contractor Management routes
    Route::prefix('contractors')->group(function () {
        Route::get('/', [ContractorController::class, 'index']);
        Route::post('/', [ContractorController::class, 'store']);
        Route::get('/{id}', [ContractorController::class, 'show']);
        Route::put('/{id}', [ContractorController::class, 'update']);
        Route::delete('/{id}', [ContractorController::class, 'destroy']);

        // Contractor clients management
        Route::get('/{id}/clients', [ContractorController::class, 'clients']);
        Route::post('/{id}/clients', [ContractorController::class, 'addClient']);
        Route::delete('/{contractor_id}/clients/{client_id}', [ContractorController::class, 'removeClient']);
    });

    // Company Management routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{id}', [CompanyController::class, 'show']);
        Route::put('/{id}', [CompanyController::class, 'update']);
        Route::delete('/{id}', [CompanyController::class, 'destroy']);
        Route::get('/{id}/clients', [CompanyController::class, 'clients']);
    });

    // Client Management routes
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/individuals', [ClientController::class, 'individuals']);
        Route::get('/corporate', [ClientController::class, 'corporate']);
        Route::get('/{id}', [ClientController::class, 'show']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);

        // Client contractor management
        Route::post('/{id}/attach-contractor', [ClientController::class, 'attachContractor']);
        Route::post('/{id}/detach-contractor', [ClientController::class, 'detachContractor']);
    });

    // Template Management routes
    Route::prefix('templates')->group(function () {
        Route::get('/', [TemplateController::class, 'index']);
        Route::post('/', [TemplateController::class, 'store']);
        Route::get('/options', [TemplateController::class, 'options']);
        Route::get('/stats', [TemplateController::class, 'stats']);
        Route::get('/type/{type}', [TemplateController::class, 'byType']);
        Route::get('/category/{category}', [TemplateController::class, 'byCategory']);
        Route::get('/{id}', [TemplateController::class, 'show']);
        Route::put('/{id}', [TemplateController::class, 'update']);
        Route::delete('/{id}', [TemplateController::class, 'destroy']);
        Route::post('/{id}/process', [TemplateController::class, 'process']);
    });

    // User Management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::post('/', [UserManagementController::class, 'store']);
        Route::get('/{id}', [UserManagementController::class, 'show']);
        Route::put('/{id}', [UserManagementController::class, 'update']);
        Route::delete('/{id}', [UserManagementController::class, 'destroy']);
        Route::put('/{id}/password', [UserManagementController::class, 'changePassword']);
        Route::put('/{id}/activate', [UserManagementController::class, 'activate']);
        Route::put('/{id}/deactivate', [UserManagementController::class, 'deactivate']);
    });

    // Roles routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [UserManagementController::class, 'roles']);
        Route::get('/{id}', [UserManagementController::class, 'showRole']);
        Route::post('/', [UserManagementController::class, 'createRole']);
        Route::put('/{id}', [UserManagementController::class, 'updateRole']);
        Route::delete('/{id}', [UserManagementController::class, 'deleteRole']);
    });

    // Permissions routes
    Route::get('/permissions', [UserManagementController::class, 'permissions']);

    // Chat Management routes
    Route::prefix('chats')->group(function () {
        Route::get('/', [ChatApiController::class, 'index']);
        Route::post('/', [ChatApiController::class, 'store']);
        Route::get('/search', [ChatApiController::class, 'search']);
        Route::get('/{id}', [ChatApiController::class, 'show']);
        Route::get('/{id}/messages', [ChatApiController::class, 'getMessages']);
        Route::post('/{id}/messages', [ChatApiController::class, 'sendMessage']);
        Route::post('/{id}/end', [ChatApiController::class, 'endChat']);
        Route::post('/{id}/transfer', [ChatApiController::class, 'transferChat']);
    });

    // Wazzup24 API routes
    Route::prefix('wazzup24')->group(function () {
        Route::post('/chats/{chat}/send', [Wazzup24Controller::class, 'sendMessage']);
        Route::post('/chats/{chat}/send-media', [Wazzup24Controller::class, 'sendMedia']);
        Route::get('/connection', [Wazzup24Controller::class, 'checkConnection']);
        Route::get('/chats/{chat}/info', [Wazzup24Controller::class, 'getChatInfo']);
    });

    // File upload routes
    Route::prefix('upload')->group(function () {
        Route::post('/file', [FileUploadController::class, 'uploadFile']);
        Route::delete('/file', [FileUploadController::class, 'deleteFile']);
    });
});

// Webhook routes (без аутентификации)
Route::prefix('webhooks')->group(function () {
    Route::match(['GET', 'POST'], '/wazzup24', [WebhookController::class, 'wazzup24']);
    Route::match(['GET', 'POST'], '/organization/{organization}', [WebhookController::class, 'organizationWebhook'])->name('webhooks.organization');
});

