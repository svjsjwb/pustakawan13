<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
        }

        // Pastikan user adalah admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }

        // Jalankan request
        $response = $next($request);

        // Jangan izinkan browser menyimpan halaman admin
        $response->headers->set(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0'
        );

        $response->headers->set(
            'Pragma',
            'no-cache'
        );

        $response->headers->set(
            'Expires',
            '0'
        );

        return $response;
    }
}