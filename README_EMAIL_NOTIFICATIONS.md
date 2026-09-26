# Panduan Sistem Notifikasi Email Perpustakaan (Gmail SMTP & Queue)

Dokumentasi ini menjelaskan konfigurasi, arsitektur, dan cara kerja sistem notifikasi email otomatis pada aplikasi Perpustakaan Laravel.

---

## 1. Konfigurasi Gmail SMTP di `.env`

Untuk mengirimkan email melalui akun Gmail, gunakan **Google App Password (16 karakter)**, bukan password login Gmail biasa.

### Langkah konfigurasi

1. Buka akun Google Anda pada Google Account Security.
2. Pastikan **2-Step Verification (Verifikasi 2 Langkah)** sudah aktif.
3. Cari menu **Sandi Aplikasi (App Passwords)**.
4. Buat sandi aplikasi baru, misalnya `Perpustakaan Laravel`.
5. Salin kode 16 karakter tanpa spasi.
6. Isi konfigurasi berikut pada file `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=emailanda@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_FROM_ADDRESS=emailanda@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Email admin utama untuk menerima alert aktivitas perpustakaan
ADMIN_NOTIFICATION_EMAIL=admin.perpus@gmail.com