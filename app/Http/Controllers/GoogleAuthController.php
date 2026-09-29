<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

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
                $googleUser = Socialite::driver('google')
                    ->stateless()
                    ->user();
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

        $googleEmail = strtolower(
            trim((string) $googleUser->getEmail())
        );

        $googleName = $googleUser->getName()
            ?: $googleUser->getNickname()
            ?: 'User';

        if ($googleId === '' || $googleEmail === '') {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Akun Google tidak menyediakan identitas email yang valid.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CARI USER YANG SUDAH ADA
        |--------------------------------------------------------------------------
        */

        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            $user = User::whereRaw(
                'LOWER(email) = ?',
                [$googleEmail]
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | USER GOOGLE BARU
        |--------------------------------------------------------------------------
        |
        | Jangan langsung membuat User/Member.
        | Simpan data Google sementara di session.
        | User baru akan dibuat setelah memilih divisi.
        |
        */

        if (!$user) {
            session([
                'google_registration' => [
                    'google_id' => $googleId,
                    'name'      => $googleName,
                    'email'     => $googleEmail,
                ],
            ]);

            return redirect()
                ->route('google.complete');
        }

        /*
        |--------------------------------------------------------------------------
        | USER SUDAH ADA
        |--------------------------------------------------------------------------
        |
        | Sinkronkan identitas Google tanpa mengubah role
        | dan tanpa mengubah status Member.
        |
        */

        $user->forceFill([
            'name'      => $googleName,
            'email'     => $googleEmail,
            'google_id' => $googleId,
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN DATA MEMBER ADA
        |--------------------------------------------------------------------------
        */

        $member = Member::where(
            'user_id',
            $user->id
        )->first();

        if (!$member) {
            $member = Member::whereRaw(
                'LOWER(email) = ?',
                [$googleEmail]
            )->first();
        }

        if (!$member) {
            Member::create([
                'user_id'       => $user->id,
                'name'          => $googleName,
                'email'         => $googleEmail,
                'division'      => 'Anggota',
                'phone'         => null,
                'address'       => null,
                'nis_nip'       => null,
                'gender'        => null,
                'class'         => null,
                'registered_at' => now()->toDateString(),
                'status'        => 'nonaktif',
            ]);
        } else {
            $member->update([
                'user_id' => $user->id,
                'name'    => $googleName,
                'email'   => $googleEmail,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN USER YANG SUDAH ADA
        |--------------------------------------------------------------------------
        */

        return $this->loginUser($user);
    }

    /**
     * Menampilkan form pelengkap data untuk Google user baru.
     */
    public function completeForm()
    {
        $googleRegistration = session('google_registration');

        if (!$googleRegistration) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Data pendaftaran Google tidak ditemukan. Silakan login dengan Google lagi.'
                );
        }

        return view(
            'auth.google-complete',
            compact('googleRegistration')
        );
    }

    /**
     * Menyelesaikan pendaftaran Google user baru.
     */
    public function complete(Request $request)
    {
        $googleRegistration = session('google_registration');

        if (!$googleRegistration) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Data pendaftaran Google tidak ditemukan. Silakan login dengan Google lagi.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DIVISI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'division' => [
                'required',
                'string',
                'max:100',
                'in:Center Of Excellence,Digital Business,E-Publishing,Finance,General Trading,HR & GA,HSE,IQA,IT,Marketing,MTIS Perpuskita dan Tisera,MTIS Planning and Development,People Development Center,Production,School Book Sales,School Book Publishing,SCM,TAX',
            ],
        ], [
            'division.required' => 'Divisi wajib dipilih.',
            'division.in' => 'Divisi yang dipilih tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CEK ULANG EMAIL
        |--------------------------------------------------------------------------
        |
        | Untuk mencegah duplikasi jika email sudah terdaftar
        | sebelum form ini dikirim.
        |
        */

        $existingUser = User::whereRaw(
            'LOWER(email) = ?',
            [strtolower($googleRegistration['email'])]
        )->first();

        if ($existingUser) {
            session()->forget('google_registration');

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Email Google tersebut sudah terdaftar. Silakan login kembali.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BUAT USER + MEMBER
        |--------------------------------------------------------------------------
        */

        $user = DB::transaction(function () use (
            $googleRegistration,
            $validated
        ) {
            $user = User::create([
                'name'      => $googleRegistration['name'],
                'email'     => $googleRegistration['email'],
                'google_id' => $googleRegistration['google_id'],
                'password'  => null,
                'role'      => 'guest',
            ]);

            Member::create([
                'user_id'       => $user->id,
                'name'          => $googleRegistration['name'],
                'email'         => $googleRegistration['email'],
                'division'      => $validated['division'],
                'phone'         => null,
                'address'       => null,
                'nis_nip'       => null,
                'gender'        => null,
                'class'         => null,
                'registered_at' => now()->toDateString(),
                'status'        => 'nonaktif',
            ]);

            return $user;
        });

        /*
        |--------------------------------------------------------------------------
        | HAPUS SESSION PENDAFTARAN GOOGLE
        |--------------------------------------------------------------------------
        */

        session()->forget('google_registration');

        /*
        |--------------------------------------------------------------------------
        | EMAIL SAMBUTAN
        |--------------------------------------------------------------------------
        */

        if ($user->email) {
            try {
                Mail::to($user->email)->send(
                    new WelcomeMail($user)
                );
            } catch (\Throwable $mailException) {
                Log::warning(
                    'Email sambutan Google gagal dikirim.',
                    [
                        'user_id'   => $user->id,
                        'recipient' => $user->email,
                        'error'     => $mailException->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        request()->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | FIRST LOGIN NOTIFICATION
        |--------------------------------------------------------------------------
        */

        \App\Services\NotificationService::firstLogin(
            $user,
            request()->ip(),
            request()->userAgent()
        );

        /*
        |--------------------------------------------------------------------------
        | LAST LOGIN
        |--------------------------------------------------------------------------
        */

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | GOOGLE USER BARU → CATALOG
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('catalog')
            ->with(
                'success',
                'Pendaftaran berhasil. Akun Anda sedang menunggu persetujuan Admin.'
            );
    }

    /**
     * Login user yang sudah ada dan redirect berdasarkan role.
     */
    private function loginUser(User $user)
    {
        $isFirstLogin = is_null($user->last_login_at);

        Auth::login($user);

        request()->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | FIRST LOGIN NOTIFICATION
        |--------------------------------------------------------------------------
        */

        if ($isFirstLogin) {
            \App\Services\NotificationService::firstLogin(
                $user,
                request()->ip(),
                request()->userAgent()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE LAST LOGIN
        |--------------------------------------------------------------------------
        */

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
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

        if ($user->role === 'guest') {
            return redirect()
                ->route('catalog')
                ->with(
                    'success',
                    'Login berhasil. Akun Anda sedang menunggu persetujuan Admin.'
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
