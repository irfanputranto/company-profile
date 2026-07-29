<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $login = $request->validated('login');
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([
            $loginType => $login,
            'password' => $request->validated('password'),
            'is_active' => true,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => [__('auth.failed')],
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route('dashboard')
            ->with('alert', [
                'icon' => 'success',
                'title' => __('admin.auth.welcome_title'),
                'message' => __('admin.auth.welcome_message'),
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('alert', [
                'icon' => 'success',
                'title' => __('admin.auth.logout_title'),
                'message' => __('admin.auth.logout_message'),
            ]);
    }
}
