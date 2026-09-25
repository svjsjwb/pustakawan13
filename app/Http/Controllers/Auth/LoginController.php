<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     * Validasi email & password, lalu redirect berdasarkan role.
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim((string) $request->input('email'));
        $fieldType  = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $fieldType => $loginInput,
            'password'  => $request->input('password'),
        ];

        // Coba autentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect('/dashboard');
            } else {
                $user->syncToMember();
                return redirect('/home');
            }
        }

        // Jika gagal, kembalikan ke login dengan pesan error
        return back()
            ->withErrors([
                'email' => 'Username/Email atau password yang Anda masukkan salah.',
            ])
            ->onlyInput('email');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Helper: redirect berdasarkan role user yang sedang login.
     */
    private function redirectByRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/dashboard');
        }

        return redirect('/home');
    }
}
