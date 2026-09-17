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
        /*
        |--------------------------------------------------------------------------
        | WAJIB LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {
            return redirect('/login')
                ->withHeaders([
                    'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma'        => 'no-cache',
                    'Expires'       => '0',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | WAJIB ADMIN
        |--------------------------------------------------------------------------
        */

        if (Auth::user()->role !== 'admin') {
            return redirect('/home')->with(
                'error',
                'Anda tidak memiliki akses ke halaman admin.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LANJUTKAN REQUEST + JANGAN CACHE HALAMAN ADMIN
        |--------------------------------------------------------------------------
        */

        $response = $next($request);

        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
