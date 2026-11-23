<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\OtpMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private OtpMailService $otpMailService
    ) {}

    /**
     * Mengambil OTP yang sudah diverifikasi dan masih berlaku.
     * Database menjadi sumber kebenaran, bukan session boolean.
     */
    private function getVerifiedResetOtp(Request $request): ?PasswordResetOtp
    {
        $email = $request->session()->get('password_reset_email');
        $otpId = $request->session()->get('password_reset_otp_id');

        if (!$email || !$otpId) {
            return null;
        }

        $reset = PasswordResetOtp::where('id', $otpId)
            ->where('email', $email)
            ->first();

        if (
            !$reset ||
            !$reset->isVerified() ||
            $reset->isExpired()
        ) {
            return null;
        }

        return $reset;
    }

    private function clearResetSession(Request $request): void
    {
        $request->session()->forget([
            'password_reset_email',
            'password_reset_otp_id',
            'password_reset_verified',
        ]);
    }

    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }




    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = strtolower(trim($data['email']));

        $this->clearResetSession($request);

        $genericMessage = 'Jika email terdaftar, kode verifikasi akan dikirim. Silakan periksa kotak masuk Anda.';

        try {
            // Penerbitan OTP dilakukan dalam transaksi.
            $result = DB::transaction(function () use ($email) {
                $user = User::whereRaw('LOWER(email) = ?', [$email])
                    ->lockForUpdate()
                    ->first();

                if (!$user) {
                    return null;
                }

                // Invalidasi OTP sebelumnya.
                PasswordResetOtp::where('email', $email)->delete();

                $otp = (string) random_int(100000, 999999);

                $reset = PasswordResetOtp::create([
                    'email' => $email,
                    'otp_hash' => Hash::make($otp),
                    'attempts' => 0,
                    'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                ]);

                return [
                    'id' => $reset->id,
                    'otp' => $otp,
                ];
            });

            // Email tidak terdaftar: tetap gunakan respons umum.
            if (!$result) {
                $request->session()->put([
                    'password_reset_email' => $email,
                    'password_reset_otp_id' => null,
                ]);

                return redirect()
                    ->route('password.otp')
                    ->with('success', $genericMessage);
            }

            // Pengiriman email dilakukan setelah transaksi selesai.
            $this->otpMailService->sendPasswordResetOtp(
                $email,
                $result['otp'],
                self::OTP_TTL_MINUTES
            );

            // Pastikan OTP belum digantikan request lain.
            $stillActive = PasswordResetOtp::where('id', $result['id'])
                ->where('email', $email)
                ->exists();

            if (!$stillActive) {
                $this->clearResetSession($request);

                return redirect()
                    ->route('password.request')
                    ->with('status', $genericMessage);
            }

            $request->session()->put([
                'password_reset_email' => $email,
                'password_reset_otp_id' => $result['id'],
            ]);
        } catch (\Throwable $e) {
            report($e);

            if (isset($result['id'])) {
                PasswordResetOtp::where('id', $result['id'])
                    ->where('email', $email)
                    ->delete();
            }

            $request->session()->put([
                'password_reset_email' => $email,
                'password_reset_otp_id' => null,
            ]);
        }

        return redirect()
            ->route('password.otp')
            ->with('success', $genericMessage);
    }

    public function showOtpForm(Request $request)
    {
        $email = $request->session()->get('password_reset_email');
        $otpId = $request->session()->get('password_reset_otp_id');

        if (!$email) {
            $this->clearResetSession($request);

            return redirect()->route('password.request');
        }

        // Jika ada ID OTP, pastikan record masih berlaku.
        if ($otpId) {
            $reset = PasswordResetOtp::where('id', $otpId)
                ->where('email', $email)
                ->first();

            if (!$reset || $reset->isExpired()) {
                $this->clearResetSession($request);

                return redirect()
                    ->route('password.request')
                    ->withErrors([
                        'email' => 'OTP sudah tidak berlaku. Silakan mulai kembali proses reset password.',
                    ]);
            }
        }

        return view('auth.verify-otp', [
            'email' => $email,
        ]);
    }



    public function verifyOtp(Request $request)
    {
        $email = $request->session()->get('password_reset_email');
        $otpId = $request->session()->get('password_reset_otp_id');

        if (!$email) {
            $this->clearResetSession($request);

            return redirect()->route('password.request');
        }

        if (!$otpId) {
            return back()->withErrors([
                'otp' => 'Kode OTP tidak valid atau tidak tersedia. Silakan periksa email atau mulai kembali proses reset password.',
            ]);
        }

        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        // Seluruh pemeriksaan dan perubahan status OTP
        // dilakukan dalam transaksi dengan penguncian record.
        $result = DB::transaction(function () use ($email, $otpId, $data) {
            $reset = PasswordResetOtp::where('id', $otpId)
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if (!$reset) {
                return ['status' => 'invalid'];
            }

            if ($reset->isExpired()) {
                return ['status' => 'expired'];
            }

            if ($reset->isVerified()) {
                return ['status' => 'verified'];
            }

            if ($reset->attempts >= self::MAX_ATTEMPTS) {
                return ['status' => 'max_attempts'];
            }

            if (!Hash::check($data['otp'], $reset->otp_hash)) {
                $reset->attempts++;
                $reset->save();

                return [
                    'status' => 'wrong',
                    'remaining' => max(
                        0,
                        self::MAX_ATTEMPTS - $reset->attempts
                    ),
                ];
            }

            $reset->verified_at = now();
            $reset->save();

            return ['status' => 'success'];
        });

        if ($result['status'] === 'expired' || $result['status'] === 'invalid') {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'OTP tidak valid atau sudah kedaluwarsa. Silakan meminta OTP baru.',
                ]);
        }

        if ($result['status'] === 'max_attempts') {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan. Silakan kirim OTP baru.',
            ]);
        }

        if ($result['status'] === 'wrong') {
            $remaining = $result['remaining'];

            return back()->withErrors([
                'otp' => $remaining > 0
                    ? "OTP salah. Sisa percobaan: {$remaining}."
                    : 'OTP salah dan batas percobaan sudah habis. Silakan kirim OTP baru.',
            ]);
        }

        return redirect()->route('password.reset');
    }




    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('password_reset_email');
        $otpId = $request->session()->get('password_reset_otp_id');

        if (!$email) {
            $this->clearResetSession($request);

            return redirect()->route('password.request');
        }

        $genericMessage = 'Jika email terdaftar, kode verifikasi akan dikirim. Silakan periksa kotak masuk Anda.';

        try {
            $otp = (string) random_int(100000, 999999);

            $result = DB::transaction(function () use ($email, $otpId, $otp) {
                $user = User::whereRaw('LOWER(email) = ?', [$email])
                    ->lockForUpdate()
                    ->first();

                if (!$user) {
                    return ['status' => 'not_found'];
                }

                if ($otpId) {
                    $oldReset = PasswordResetOtp::where('id', $otpId)
                        ->where('email', $email)
                        ->lockForUpdate()
                        ->first();

                    if ($oldReset && $oldReset->isVerified() && !$oldReset->isExpired()) {
                        return ['status' => 'verified'];
                    }
                }

                PasswordResetOtp::where('email', $email)->delete();

                $newReset = PasswordResetOtp::create([
                    'email' => $email,
                    'otp_hash' => Hash::make($otp),
                    'attempts' => 0,
                    'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                ]);

                return [
                    'status' => 'created',
                    'id' => $newReset->id,
                ];
            });

            if ($result['status'] === 'verified') {
                return redirect()->route('password.reset');
            }

            if ($result['status'] === 'not_found') {
                $request->session()->put([
                    'password_reset_email' => $email,
                    'password_reset_otp_id' => null,
                ]);

                return redirect()
                    ->route('password.otp')
                    ->with('success', $genericMessage);
            }

            $this->otpMailService->sendPasswordResetOtp(
                $email,
                $otp,
                self::OTP_TTL_MINUTES
            );

            $stillActive = PasswordResetOtp::where('id', $result['id'])
                ->where('email', $email)
                ->exists();

            if (!$stillActive) {
                $request->session()->put([
                    'password_reset_email' => $email,
                    'password_reset_otp_id' => null,
                ]);

                return redirect()
                    ->route('password.otp')
                    ->with('success', $genericMessage);
            }

            $request->session()->put([
                'password_reset_email' => $email,
                'password_reset_otp_id' => $result['id'],
            ]);

            return redirect()
                ->route('password.otp')
                ->with('success', 'OTP baru telah dikirim.');
        } catch (\Throwable $e) {
            report($e);

            if (isset($result['id'])) {
                PasswordResetOtp::where('id', $result['id'])
                    ->where('email', $email)
                    ->delete();
            }

            $request->session()->put([
                'password_reset_email' => $email,
                'password_reset_otp_id' => null,
            ]);

            return redirect()
                ->route('password.otp')
                ->with('success', $genericMessage);
        }
    }



    public function showResetForm(Request $request)
    {
        if (!$this->getVerifiedResetOtp($request)) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Silakan lakukan verifikasi OTP terlebih dahulu.',
                ]);
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $reset = $this->getVerifiedResetOtp($request);

        if (!$reset) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Verifikasi OTP tidak valid atau sudah kedaluwarsa.',
                ]);
        }

        $data = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $email = $request->session()->get('password_reset_email');
        $otpId = $request->session()->get('password_reset_otp_id');

        // Validasi OTP dan perubahan password dilakukan secara atomik.
        $user = DB::transaction(function () use ($email, $otpId, $data) {
            $reset = PasswordResetOtp::where('id', $otpId)
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if (
                !$reset ||
                !$reset->isVerified() ||
                $reset->isExpired()
            ) {
                return null;
            }

            $user = User::whereRaw('LOWER(email) = ?', [$email])
                ->lockForUpdate()
                ->first();

            if (!$user) {
                return null;
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            // OTP hanya boleh digunakan satu kali.
            PasswordResetOtp::where('email', $email)->delete();

            return $user;
        });

        if (!$user) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Proses reset gagal. Silakan lakukan verifikasi OTP kembali.',
                ]);
        }

        $this->clearResetSession($request);

        $isFirstLogin = is_null($user->last_login_at);

        Auth::login($user);
        $request->session()->regenerate();

        $request->session()->put('just_logged_in', true);

        if ($user->role === 'guest') {
            $request->session()->put('show_guest_approval_popup', true);
        }

        if ($isFirstLogin) {
            \App\Services\NotificationService::firstLogin(
                $user,
                $request->ip(),
                $request->userAgent()
            );
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Password baru berhasil disimpan. Anda telah otomatis masuk ke sistem.'
            );
    }
}
