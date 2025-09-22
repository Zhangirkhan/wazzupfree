<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'] ?? null,
            'role' => 'user',
            'email_verified_at' => now()
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        Log::info('User registered successfully', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Log::warning('Login failed - invalid credentials', ['email' => $credentials['email']]);
            throw new InvalidCredentialsException();
        }
        $token = $user->createToken('auth-token')->plainTextToken;

        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 24 * 7 // 7 дней
        ];
    }

    public function logout(Request $request): bool
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        Log::info('User logged out', ['user_id' => $user->id]);

        return true;
    }

    public function refreshToken(Request $request): array
    {
        $user = $request->user();

        // Удаляем старый токен
        $request->user()->currentAccessToken()->delete();

        // Создаем новый токен
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 24 * 7 // 7 дней
        ];
    }

    public function updateProfile(User $user, array $data): User
    {
        $updateData = array_filter($data, function($value) {
            return $value !== null;
        });

        $user->update($updateData);

        return $user->load(['organization', 'roles', 'permissions']);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new InvalidCredentialsException('Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    public function getUserStats(User $user): array
    {
        return Cache::remember("user_stats_{$user->id}", 300, function () use ($user) {
            return [
                'total_chats' => \App\Models\Chat::where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id)
                    ->count(),
                'active_chats' => \App\Models\Chat::where(function($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhere('assigned_to', $user->id);
                })->where('status', 'active')->count(),
                'messages_sent' => \App\Models\Message::where('user_id', $user->id)
                    ->where('direction', 'out')
                    ->count(),
                'unread_notifications' => 0 // Пока нет таблицы notifications
            ];
        });
    }
}
