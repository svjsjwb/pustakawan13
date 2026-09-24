<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (Auth::user()->role !== 'member') {
            return redirect('/catalog')
                ->with(
                    'error',
                    'Akun Anda belum disetujui sebagai member.'
                );
        }

        return $next($request);
    }
}