<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Log in with your credentials
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        return Auth::guard('admin')->attempt($credentials, $remember);
    }

    /**
     * Log out current user
     */
    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }

    /**
     * Update last login time for admin
     */
    public function updateLastLoginTime(User $user): void
    {
        $user->last_login_at = now();
        $user->save();
    }

    /**
     * Get the admin who is logged in
     */
    public function getAuthenticatedUser(): ?User
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Check if admin is logged in
     */
    public function isAuthenticated(): bool
    {
        return Auth::guard('admin')->check();
    }
}
