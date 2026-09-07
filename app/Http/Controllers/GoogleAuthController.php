<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Menerima callback dari Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Throwable $ex) {
                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'Sesi login Google sudah kedaluwarsa. Silakan login dengan Google lagi.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cari berdasarkan Google ID
        |--------------------------------------------------------------------------
        */

        $user = User::where(
            'google_id',
            $googleUser->getId()
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Kalau belum ditemukan, cari berdasarkan email
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            $user = User::where(
                'email',
                $googleUser->getEmail()
            )->first();

            /*
            |--------------------------------------------------------------------------
            | Email sudah ada → hubungkan akun dengan Google
            |--------------------------------------------------------------------------
            */

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName()
                        ?: $googleUser->getNickname()
                        ?: 'User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'role' => 'user',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Login user
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        request()->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Selamat datang di Dashboard Admin.'
                );
        }

        return redirect()
            ->route('user.home')
            ->with(
                'success',
                'Login berhasil.'
            );
    }
}
