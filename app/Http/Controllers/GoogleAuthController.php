<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $createdViaGoogle = false;

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

        $googleId = (string) $googleUser->getId();
        $googleEmail = strtolower(trim((string) $googleUser->getEmail()));
        $googleName = $googleUser->getName()
            ?: $googleUser->getNickname()
            ?: 'User';

        if ($googleId === '' || $googleEmail === '') {
            return redirect()
                ->route('login')
                ->with('error', 'Akun Google tidak menyediakan identitas email yang valid.');
        }

        $user = DB::transaction(function () use (
            $googleId,
            $googleEmail,
            $googleName,
            &$createdViaGoogle
        ) {
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                $user = User::whereRaw('LOWER(email) = ?', [$googleEmail])->first();
            }

            if (!$user) {
                $createdViaGoogle = true;
                return User::create([
                    'name' => $googleName,
                    'email' => $googleEmail,
                    'google_id' => $googleId,
                    'password' => null,
                    'role' => 'user',
                ]);
            }

            $user->forceFill([
                'name' => $googleName,
                'email' => $googleEmail,
                'google_id' => $googleId,
            ])->save();

            Member::where(function ($query) use ($user, $googleEmail) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->orWhere('email', $googleEmail);
            })->update([
                'user_id' => $user->id,
                'name' => $googleName,
                'email' => $googleEmail,
            ]);

            return $user->fresh();
        });

        if ($createdViaGoogle) {
            \App\Services\NotificationService::userRegistered($user);
        }

        /*
        |--------------------------------------------------------------------------
        | Login user
        |--------------------------------------------------------------------------
        */

        $isFirstLogin = is_null($user->last_login_at);

        Auth::login($user);

        request()->session()->regenerate();

        if ($isFirstLogin) {
            \App\Services\NotificationService::firstLogin($user, request()->ip(), request()->userAgent());
        }

        $user->forceFill(['last_login_at' => now()])->save();

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
