<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     *
     * Menggunakan case-insensitive email lookup + Hash::check
     * agar password yang tersimpan tetap aman.
     */
    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cari user (case-insensitive email)
        |--------------------------------------------------------------------------
        */

        $credentials['email'] = strtolower(trim($credentials['email']));
        $user = \App\Models\User::whereRaw('LOWER(email) = ?', [$credentials['email']])->first();

        if (!$user || !$user->password || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Email/username atau password salah.',
                ])
                ->withInput($request->only('email'));
        }

        /*
        |--------------------------------------------------------------------------
        | Login & session
        |--------------------------------------------------------------------------
        */

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->role === 'member') {
            return redirect()->intended(route('user.home'));
        }

        if ($user->role === 'guest') {
            return redirect()->intended(route('catalog'))
                ->with(
                    'info',
                    'Akun Anda masih menunggu persetujuan Admin. Anda belum dapat melakukan reservasi atau peminjaman buku.'
                );
        }

        return redirect()->route('login');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
