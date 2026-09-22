<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'library:test-email {recipient? : Alamat email penerima uji coba}';
    protected $description = 'Kirim email uji coba untuk memverifikasi koneksi SMTP Gmail';

    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  UJI KONEKSI EMAIL GMAIL SMTP PERPUSTAKAAN');
        $this->info('====================================================');

        $mailer     = config('mail.default');
        $host       = config("mail.mailers.{$mailer}.host");
        $port       = config("mail.mailers.{$mailer}.port");
        $username   = config("mail.mailers.{$mailer}.username");
        $password   = config("mail.mailers.{$mailer}.password");
        $encryption = config("mail.mailers.{$mailer}.encryption");
        $from       = config('mail.from.address');
        $fromName   = config('mail.from.name');

        $this->table(['Parameter', 'Nilai'], [
            ['MAIL_MAILER', $mailer],
            ['MAIL_HOST', $host ?? '-'],
            ['MAIL_PORT', $port ?? '-'],
            ['MAIL_ENCRYPTION', $encryption ?? '-'],
            ['MAIL_USERNAME', !empty($username) ? $username : '<KOSONG>'],
            ['MAIL_PASSWORD', !empty($password) ? '****** (Terisi: ' . strlen($password) . ' karakter)' : '<KOSONG>'],
            ['MAIL_FROM_ADDRESS', $from ?? '-'],
            ['MAIL_FROM_NAME', $fromName ?? '-'],
        ]);

        if (empty($username) || empty($password)) {
            $this->error('GAGAL: MAIL_USERNAME atau MAIL_PASSWORD belum diisi pada file .env!');
            $this->warn("\nLangkah Mengaktifkan Gmail:");
            $this->line('1. Buka https://myaccount.google.com/security');
            $this->line('2. Pastikan Verifikasi 2 Langkah (2-Step Verification) aktif.');
            $this->line('3. Cari "Sandi Aplikasi" (App Passwords) dan generate 16 karakter.');
            $this->line('4. Masukkan ke file .env:');
            $this->line("   MAIL_USERNAME=alamat_email_anda@gmail.com");
            $this->line("   MAIL_PASSWORD=16_karakter_app_password");
            $this->line("   MAIL_FROM_ADDRESS=alamat_email_anda@gmail.com");
            return self::FAILURE;
        }

        $recipient = $this->argument('recipient') ?: $username;
        $this->info("\nMengirim email uji coba ke: {$recipient}...");

        try {
            Mail::html('
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <h2 style="color: #2563eb;">Koneksi Gmail Berhasil!</h2>
                    <p>Halo, ini adalah email uji coba dari sistem informasi <strong>' . e($fromName) . '</strong>.</p>
                    <p>Jika Anda menerima email ini, konfigurasi SMTP Gmail di aplikasi perpustakaan telah berhasil aktif dan siap digunakan untuk seluruh notifikasi peminjaman, reservasi, event, dan akun.</p>
                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
                    <small style="color: #64748b;">Waktu pengiriman: ' . now()->toDateTimeString() . ' WIB</small>
                </div>
            ', function ($message) use ($recipient, $fromName) {
                $message->to($recipient)
                        ->subject('Uji Coba Koneksi Gmail - ' . $fromName);
            });

            $this->info("BERHASIL! Email uji coba berhasil terkirim ke {$recipient}.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("GAGAL MENGIRIM EMAIL:");
            $this->error($e->getMessage());
            $this->warn("\nTips Pemecahan Masalah:");
            $this->line('- Pastikan menggunakan "Sandi Aplikasi" (App Password 16 karakter), bukan kata sandi akun Google biasa.');
            $this->line('- Pastikan port 587 tidak diblokir oleh firewall atau ISP.');
            return self::FAILURE;
        }
    }
}
