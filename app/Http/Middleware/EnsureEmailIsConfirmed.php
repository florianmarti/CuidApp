<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsConfirmed
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->is_confirmed) {
            return redirect('/login')->with('error', 'Por favor, confirma tu email antes de continuar.');
        }

        return $next($request);
    }
}
