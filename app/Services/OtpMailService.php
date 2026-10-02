<?php

namespace App\Services;

use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;

class OtpMailService
{
    /**
     * Kirim OTP reset password.
     *
     * Business logic tidak perlu tahu apakah
     * email dikirim melalui SMTP, Resend, atau provider lain.
     */
    public function sendPasswordResetOtp(
        string $email,
        string $otp,
        int $expiresInMinutes = 5
    ): void {
        Mail::to($email)->send(
            new PasswordResetOtpMail(
                $otp,
                $expiresInMinutes
            )
        );
    }
}