<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 15 * 60);
            return back()
                ->withErrors(['email' => 'Invalid credentials.'])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();

        if ($user->status->value === 'suspended') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        return redirect()->route($this->dashboardRoute($user->role->value));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardRoute(string $role): string
    {
        return match ($role) {
            'admin', 'receptionist' => 'dashboard',
            'doctor'                => 'doctor.dashboard',
            'patient'               => 'patient.dashboard',
            default                 => 'dashboard',
        };
    }
}
