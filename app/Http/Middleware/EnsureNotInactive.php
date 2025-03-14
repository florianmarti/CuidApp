<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotInactive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->is_suspended) {
                \Log::info("Usuario suspendido detectado: " . $user->name);
                Auth::logout();
                return redirect('/login')->with('error', 'Tu cuenta ha sido suspendida. Por favor, comunícate con admin@example.com.');
            }

            if ($user->is_inactive) {
                \Log::info("Usuario inactivo detectado: " . $user->name);
                Auth::logout();
                return redirect('/')->with('error', 'Tu cuenta ha sido inactivada. Contacta al administrador para más información.');
            }
        }

        return $next($request);
    }
}
