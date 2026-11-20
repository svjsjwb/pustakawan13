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

        $user = Auth::user();
        $role = $user->role;

        if ($role !== 'member' && $role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda belum disetujui sebagai member.'
                ], 403);
            }

            return redirect('/catalog')->with(
                'error',
                'Akun Anda belum disetujui sebagai member.'
            );
        }

        return $next($request);
    }
}
