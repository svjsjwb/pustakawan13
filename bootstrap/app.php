<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'no.back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // User sudah login → jangan bisa akses /login atau /register
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = $request->user();

            if ($user && $user->role === 'admin') {
                return route('dashboard');
            }

            return route('user.home');
        });

        // User belum login → redirect ke halaman login
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();