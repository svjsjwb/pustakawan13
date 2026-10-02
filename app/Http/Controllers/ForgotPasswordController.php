<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Services\OtpMailService;

class ForgotPasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private OtpMailService $otpMailService
    ) {}

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
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // Jangan membocorkan apakah sebuah email terdaftar.
        if (!$user) {
            return back()->with('success', 'Jika email tersebut terdaftar, kode OTP sudah dikirim. Silakan cek inbox/spam.');
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::where('email', $email)->delete();

        $reset = PasswordResetOtp::create([
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        $this->otpMailService->sendPasswordResetOtp(
            $email,
            $otp,
            self::OTP_TTL_MINUTES
        );

        $request->session()->put('password_reset_email', $email);
        $request->session()->forget('password_reset_verified');

        return redirect()->route('password.otp')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showOtpForm(Request $request)
    {
        if (!$request->session()->has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $reset = PasswordResetOtp::where('email', $email)
            ->latest('id')
            ->first();

        if (!$reset || $reset->isExpired()) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa. Silakan kirim OTP baru.']);
        }

        if ($reset->isVerified()) {
            $request->session()->put('password_reset_verified', true);
            return redirect()->route('password.reset');
        }

        if ($reset->attempts >= self::MAX_ATTEMPTS) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan kirim OTP baru.']);
        }

        if (!Hash::check($data['otp'], $reset->otp_hash)) {
            $reset->increment('attempts');
            $remaining = max(0, self::MAX_ATTEMPTS - $reset->attempts);

            return back()->withErrors([
                'otp' => $remaining > 0
                    ? "OTP salah. Sisa percobaan: {$remaining}."
                    : 'OTP salah dan batas percobaan sudah habis. Silakan kirim OTP baru.',
            ]);
        }

        $reset->update(['verified_at' => now()]);
        $request->session()->put('password_reset_verified', true);

        return redirect()->route('password.reset');
    }

    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::where('email', $email)->delete();

        PasswordResetOtp::create([
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        $this->otpMailService->sendPasswordResetOtp(
            $email,
            $otp,
            self::OTP_TTL_MINUTES
        );

        $request->session()->forget('password_reset_verified');

        return back()->with('success', 'OTP baru telah dikirim.');
    }

    public function showResetForm(Request $request)
    {
        if (!$request->session()->get('password_reset_verified')) {
            return redirect()->route('password.otp');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (!$request->session()->get('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $email = $request->session()->get('password_reset_email');
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            $request->session()->forget([
                'password_reset_email',
                'password_reset_verified',
            ]);

            return redirect()->route('password.request')->withErrors([
                'email' => 'Akun tidak ditemukan.',
            ]);
        }

        // User model memakai cast 'hashed', jadi password akan di-hash otomatis.
        $user->password = $data['password'];
        $user->save();

        PasswordResetOtp::where('email', $email)->delete();

        $request->session()->forget([
            'password_reset_email',
            'password_reset_verified',
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
