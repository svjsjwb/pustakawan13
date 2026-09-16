<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya mengizinkan user dengan role 'user'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, redirect ke login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Jika sudah login tapi bukan user biasa, redirect ke admin dashboard
        if (Auth::user()->role !== 'user') {
            return redirect('/dashboard')->with(
                'error',
                'Anda tidak memiliki akses ke halaman user.'
            );
        }

        return $next($request);
    }
}
