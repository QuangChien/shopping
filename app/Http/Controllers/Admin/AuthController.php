<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    protected AuthService $authService;

    /**
     * Initialize controller with dependency injection
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show Admin login form
     */
    public function showLoginForm()
    {
        return Inertia::render('Admin/Auth/Login');
    }

    /**
     * Admin login handling
     */
    public function login(LoginRequest $request)
    {
        // Only take email and password for credentials
        $credentials = $request->only(['email', 'password']);

        if ($this->authService->attemptLogin($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update login time
            $user = $this->authService->getAuthenticatedUser();
            if ($user) {
                $this->authService->updateLastLoginTime($user);
            }
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request)
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
