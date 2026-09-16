<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya mengizinkan user dengan role 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, redirect ke login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Jika sudah login tapi bukan admin, redirect ke halaman user
        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with(
                'error',
                'Anda tidak memiliki akses ke halaman admin.'
            );
        }

        return $next($request);
    }
}
