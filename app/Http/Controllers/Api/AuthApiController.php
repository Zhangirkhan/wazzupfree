<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AuthServiceInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends ApiController
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}
    /**
     * Регистрация нового пользователя
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return $this->successResponse($result, 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to register user', $e->getMessage(), 500);
        }
    }

    /**
     * Вход в систему
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return $this->successResponse($result, 'Login successful');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid credentials');
        }
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request);

            return $this->successResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to logout', $e->getMessage(), 500);
        }
    }

    /**
     * Получить информацию о текущем пользователе
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->load(['organizations', 'roles', 'department']);

            return $this->successResponse([
                'user' => $user,
                'permissions' => $this->getUserPermissions($user),
                'roles' => [$user->role]
            ], 'User information retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user information', $e->getMessage(), 500);
        }
    }

    private function getUserPermissions(User $user): array
    {
        $permissions = [
            'admin' => [
                'dashboard', 'users', 'departments', 'chats', 'organizations',
                'positions', 'clients', 'settings'
            ],
            'manager' => [
                'dashboard', 'clients', 'messenger'
            ],
            'employee' => [
                'dashboard', 'clients', 'messenger'
            ],
            'user' => [
                'dashboard', 'chats', 'messenger'
            ]
        ];

        return $permissions[$user->role] ?? [];
    }

    /**
     * Обновить профиль пользователя
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'position' => 'sometimes|nullable|string|max:255',
            'avatar' => 'sometimes|nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $updateData = $request->only(['name', 'phone', 'position']);

            // Обработка аватара
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = time() . '_' . $avatar->getClientOriginalName();
                $path = $avatar->storeAs('avatars', $filename, 'public');
                $updateData['avatar'] = $path;
            }

            $user->update($updateData);
            $user->load(['organizations', 'roles', 'department']);

            return $this->successResponse($user, 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile', $e->getMessage(), 500);
        }
    }

    /**
     * Изменить пароль
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', null, 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return $this->successResponse(null, 'Password changed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change password', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить токен
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request);

            return $this->successResponse($result, 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to refresh token', $e->getMessage(), 500);
        }
    }

    /**
     * Получить статистику пользователя
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $stats = $this->authService->getUserStats($user);

            return $this->successResponse($stats, 'User statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user statistics', $e->getMessage(), 500);
        }
    }
}
