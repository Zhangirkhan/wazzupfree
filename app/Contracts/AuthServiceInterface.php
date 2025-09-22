<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(array $credentials): array;

    public function logout(Request $request): bool;

    public function refreshToken(Request $request): array;

    public function updateProfile(User $user, array $data): User;

    public function changePassword(User $user, string $currentPassword, string $newPassword): bool;

    public function getUserStats(User $user): array;
}
