<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | JANGAN SIMPAN HALAMAN PROTECTED DI BROWSER CACHE
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Cache-Control',
            'private, no-store, no-cache, must-revalidate, max-age=0'
        );

        $response->headers->set(
            'Pragma',
            'no-cache'
        );

        $response->headers->set(
            'Expires',
            '0'
        );

        /*
        |--------------------------------------------------------------------------
        | SECURITY HEADERS
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'X-Frame-Options',
            'SAMEORIGIN'
        );

        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        return $response;
    }
}