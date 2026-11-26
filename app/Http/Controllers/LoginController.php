<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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
    | Verifikasi Cloudflare Turnstile
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'cf-turnstile-response' => ['required', 'string'],
        ], [
            'cf-turnstile-response.required' =>
            'Silakan selesaikan verifikasi CAPTCHA terlebih dahulu.',
        ]);

        try {
            $captchaResponse = Http::asForm()
                ->timeout(10)
                ->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret' => config('services.turnstile.secret_key'),
                        'response' => $request->input('cf-turnstile-response'),
                        'remoteip' => $request->ip(),
                    ]
                );

            $captchaResult = $captchaResponse->json();
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'captcha' => 'Verifikasi CAPTCHA gagal terhubung. Silakan coba lagi.',
                ])
                ->withInput($request->only('email'));
        }

        if (
            !$captchaResponse->successful() ||
            !is_array($captchaResult) ||
            !($captchaResult['success'] ?? false)
        ) {
            return back()
                ->withErrors([
                    'captcha' => 'Verifikasi CAPTCHA tidak valid atau sudah kedaluwarsa. Silakan coba lagi.',
                ])
                ->withInput($request->only('email'));
        }

        /*
    |--------------------------------------------------------------------------
    | Validasi kredensial login
    |--------------------------------------------------------------------------
    */

        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cari user (email atau nama, case-insensitive)
        |--------------------------------------------------------------------------
        */

        $loginInput = trim($credentials['email']);
        $user = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($loginInput)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($loginInput)])
            ->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Email/username atau password salah.',
                ])
                ->withInput($request->only('email'));
        }

        // Jika user Google belum pernah membuat password lokal
        if (empty($user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Akun ini terdaftar melalui Google dan belum memiliki kata sandi manual. Silakan masuk menggunakan tombol "Masuk dengan Google" atau buat kata sandi terlebih dahulu di menu Profil > Keamanan.',
                ])
                ->withInput($request->only('email'));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
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

        $isFirstLogin = is_null($user->last_login_at);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->flash('just_logged_in', true);
        $request->session()->put('just_logged_in', true);

        if ($user->role === 'guest') {
            $request->session()->put('show_guest_approval_popup', true);
        }

        if ($isFirstLogin) {
            \App\Services\NotificationService::firstLogin($user, $request->ip(), $request->userAgent());
        }

        $user->forceFill(['last_login_at' => now()])->save();

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->role === 'member') {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->role === 'guest') {
            return redirect()->intended(route('dashboard'))
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

        return redirect()->route('landing');
    }
}
