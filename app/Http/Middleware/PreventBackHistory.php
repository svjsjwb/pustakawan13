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

        if (!$request->is('/') && !str_contains($response->headers->get('Cache-Control', ''), 'public')) {
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
        }

        /*
        |--------------------------------------------------------------------------
        | SECURITY HEADERS (OWASP Security Baseline)
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

        $response->headers->set(
            'X-XSS-Protection',
            '1; mode=block'
        );

        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob: https:; img-src 'self' data: blob: https:; font-src 'self' https: data:; frame-ancestors 'self';"
            );
        }

        return $response;
    }
}