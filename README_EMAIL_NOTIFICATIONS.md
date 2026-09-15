# Automated Email Notification

Tambahkan SMTP pada `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=akun@example.com
MAIL_PASSWORD=password-smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=akun@example.com
MAIL_FROM_NAME="Nama Perpustakaan"
```

Jalankan migration:

```bash
php artisan migrate --force
```

Scheduler Laravel harus berjalan setiap menit:

```cron
* * * * * cd /var/www/pustakawan13 && php artisan schedule:run >> /dev/null 2>&1
```

Command pengingat mencari transaksi aktif dengan `due_at` besok dan hanya menandai `is_reminder_sent = true` setelah SMTP berhasil mengirim email:

```bash
php artisan library:send-due-reminders
```

Broadcast acara tersedia pada `POST /library-events/broadcast` untuk admin dengan field `name`, `event_date`, `location`, dan `description`.
