<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMustChangePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->must_change_password && !$request->routeIs('password.change.first', 'password.update.first', 'logout')) {
                return redirect()->route('password.change.first')
                    ->with('warning', 'Demi keamanan akun ASN Anda, silakan ubah password default 4-digit NIP Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
