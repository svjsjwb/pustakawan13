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
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menerima callback dari Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Sesi login Google sudah kedaluwarsa. Silakan login dengan Google lagi.'
                );
        }

        /*
         * |--------------------------------------------------------------------------
         * | CARI USER BERDASARKAN GOOGLE ID
         * |--------------------------------------------------------------------------
         */

        $user = User::where(
            'google_id',
            $googleUser->getId()
        )->first();

        /*
         * |--------------------------------------------------------------------------
         * | JIKA BELUM ADA, CARI BERDASARKAN EMAIL
         * |--------------------------------------------------------------------------
         */

        if (!$user) {
            $user = User::where(
                'email',
                $googleUser->getEmail()
            )->first();

            /*
             * |--------------------------------------------------------------------------
             * | EMAIL SUDAH TERDAFTAR
             * |--------------------------------------------------------------------------
             */

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
            /*
             * |--------------------------------------------------------------------------
             * | USER GOOGLE BARU
             * |--------------------------------------------------------------------------
             */ else {
                $user = User::create([
                    'name' => $googleUser->getName()
                        ?: $googleUser->getNickname()
                        ?: 'User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,

                    /*
                     * |--------------------------------------------------------------------------
                     * | USER BARU DEFAULT = USER
                     * |--------------------------------------------------------------------------
                     */
                    'role' => 'user',
                ]);
            }
        }

        /*
         * |--------------------------------------------------------------------------
         * | LOGIN USER
         * |--------------------------------------------------------------------------
         */

        Auth::login($user);

        request()->session()->regenerate();

        /*
         * |--------------------------------------------------------------------------
         * | CEK ROLE
         * |--------------------------------------------------------------------------
         */

        if ($user->role === 'admin') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Selamat datang di Dashboard Admin.'
                );
        }

        /*
         * |--------------------------------------------------------------------------
         * | USER BIASA
         * |--------------------------------------------------------------------------
         */

        return redirect()
            ->route('user.home')
            ->with(
                'success',
                'Login berhasil.'
            );
    }
}
