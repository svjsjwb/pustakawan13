# Panduan Sistem Notifikasi Email Perpustakaan (Gmail SMTP & Queue)

Dokumentasi ini menjelaskan konfigurasi, arsitektur, dan cara kerja sistem notifikasi email otomatis di aplikasi Perpustakaan Laravel.

---

## 1. Konfigurasi Gmail SMTP di `.env`

Untuk mengirimkan email melalui akun Gmail, Anda harus menggunakan **Google App Password (16 karakter)**, bukan password login Gmail biasa:

1. Buka akun Google Anda di [Google Account Security](https://myaccount.google.com/security).
2. Pastikan **2-Step Verification (Verifikasi 2 Langkah)** sudah aktif.
3. Cari menu **Sandi Aplikasi (App Passwords)**.
4. Buat sandi aplikasi baru (beri nama misalnya: `Perpustakaan Laravel`). Salin kode 16 karakter tanpa spasi.
5. Isi konfigurasi berikut pada file `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=emailanda@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_FROM_ADDRESS=emailanda@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Email admin utama untuk menerima alert aktivitas perpustakaan:
ADMIN_NOTIFICATION_EMAIL=admin.perpus@gmail.com
```

> **Catatan Pengujian Lokal / Offline**:
> Jika tidak ingin mengirim email sungguhan saat dev lokal tanpa internet, Anda dapat mengubah sementara `MAIL_MAILER=log`. Semua isi email akan ditulis ke `storage/logs/laravel.log`.

---

## 2. Menjalankan Antrean (Queue Worker)

Karena pengiriman email menggunakan `QUEUE_CONNECTION=database` dan semua class `Mailable` mengimplementasikan `ShouldQueue`, email dikirim secara asynchronous di latar belakang agar pengguna tidak mengalami loading lama saat mendaftar atau meminjam buku.

### Cara 1: Jalankan start.bat (Rekomendasi untuk Windows Laragon)
Jalankan file [start.bat](file:///c:/laragon/perpustakaan_final/start.bat). Script ini otomatis membuka 4 jendela konsol:
- Vite Dev Server
- Laravel Server (`php artisan serve`)
- Queue Worker (`php artisan queue:listen`)
- Schedule Worker (`php artisan schedule:work`)

### Cara 2: Menjalankan manual via Terminal
```bash
php artisan queue:listen
```

---

## 3. Daftar Skenario Notifikasi yang Diimplementasikan

| No | Aktivitas | Penerima | Mailable Class | Template Blade |
|---|---|---|---|---|
| 1 | **Registrasi Pengguna Baru** | User & Admin | `WelcomeMail` & `AdminActivityAlertMail` | `emails/welcome.blade.php` & `emails/admin-alert.blade.php` |
| 2 | **Login Perdana (First Login)** | User | `FirstLoginMail` | `emails/first-login.blade.php` |
| 3 | **Pengajuan Peminjaman Buku** | User & Admin | `BorrowingSubmittedMail` & `AdminActivityAlertMail` | `emails/borrowing-submitted.blade.php` & `emails/admin-alert.blade.php` |
| 4 | **Peminjaman Disetujui Admin** | User | `BorrowingApprovedMail` | `emails/borrowing-approved.blade.php` |
| 5 | **Peminjaman Ditolak Admin** | User | `BorrowingRejectedMail` | `emails/borrowing-rejected.blade.php` |
| 6 | **Pengembalian Buku (Tanda Terima)** | User & Admin | `BorrowingReturnedMail` & `AdminActivityAlertMail` | `emails/borrowing-returned.blade.php` & `emails/admin-alert.blade.php` |
| 7 | **Pengajuan Reservasi Buku** | User & Admin | `ReservationSubmittedMail` & `AdminActivityAlertMail` | `emails/reservation-submitted.blade.php` & `emails/admin-alert.blade.php` |
| 8 | **Persetujuan / Penolakan Reservasi** | User | `ReservationStatusMail` | `emails/reservation-status.blade.php` |
| 9 | **Persetujuan / Penolakan Perpanjangan** | User | `ExtensionStatusMail` | `emails/extension-status.blade.php` |
| 10 | **Pengingat H-1 Jatuh Tempo Buku** | User | `BorrowingDueReminderMail` | `emails/borrowing-due-reminder.blade.php` |
| 11 | **Broadcast Event Perpustakaan** | Seluruh User & Admin | `LibraryEventMail` & `AdminActivityAlertMail` | `emails/library-event.blade.php` |

---

## 4. Automasi Pengingat H-1 (Cron / Scheduler)

Command pengingat H-1 otomatis terdaftar di [routes/console.php](file:///c:/laragon/perpustakaan_final/routes/console.php) dan berjalan setiap hari pukul 07:00 pagi:

```bash
php artisan library:send-due-reminders
```

Di server produksi (Linux cPanel / VPS), daftarkan scheduler cron setiap menit:
```cron
* * * * * cd /path/ke/perpustakaan_final && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Verifikasi & Pengujian Otomatis

Untuk menjalankan unit & feature test sistem email notifikasi:
```bash
php artisan test --filter=EmailNotificationSystemTest
```
