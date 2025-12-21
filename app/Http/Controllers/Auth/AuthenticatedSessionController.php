<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        try {
        // Attempt authentication
            $request->authenticate();
        } catch (ValidationException $e) {
            // Failed login → increment attempts
            RateLimiter::hit($this->throttleKey($request), 60);

            throw $e;
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        // Redirect based on user role
        $user = Auth::user();
        
        if ($user->role === 'instructor') {
            return redirect()->intended(route('instructor.dashboard', absolute: false));
        } elseif ($user->role === 'student') {
            return redirect()->intended(route('student.dashboard', absolute: false));
        }
        
        // Default fallback to general dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    protected function ensureIsNotRateLimited(LoginRequest $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        session()->flash('lockout_seconds', $seconds);
    }

    protected function throttleKey(LoginRequest $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
             ->with('logout_success', 'Your have been logged out successfully');
    }
}
