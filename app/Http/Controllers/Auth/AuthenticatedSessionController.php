<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    $request->authenticate();

    $request->session()->regenerate();

    if (!$request->user()->is_confirmed) {
        Auth::logout();
        return redirect('/login')->with('error', 'Por favor, confirma tu correo primero.');
    }
    if ($request->user()->is_suspended) {
        Auth::logout();
        return redirect('/login')->with('error', 'Tu cuenta ha sido suspendida. Por favor, comunícate con admin@example.com.');
    }

    if ($request->user()->is_inactive) {
        Auth::logout();
        return redirect('/')->with('error', 'Tu cuenta ha sido inactivada. Contacta al administrador para más información.');
    }

    return redirect()->intended(route('dashboard', absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
