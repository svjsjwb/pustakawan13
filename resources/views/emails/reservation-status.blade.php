<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $status === 'disetujui' ? 'Reservasi Disetujui' : 'Reservasi Ditolak' }}</title></head>
<body style="margin:0;background:#f4f7f8;font-family:Arial,sans-serif;color:#24323d;line-height:1.6;">
<div style="max-width:600px;margin:32px auto;background:#ffffff;border:1px solid #dce5e8;border-radius:8px;overflow:hidden;">
<div style="background:#0f766e;padding:24px;color:#ffffff;"><h1 style="margin:0;font-size:22px;">{{ $status === 'disetujui' ? 'Reservasi Disetujui' : 'Reservasi Ditolak' }}</h1></div>
<div style="padding:24px;"><p>Halo {{ $reservation->user?->name ?? $reservation->member?->name ?? 'Anggota' }},</p>
<p>{{ $status === 'disetujui' ? 'Reservasi buku Anda telah disetujui.' : 'Reservasi buku Anda belum dapat disetujui.' }}</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;"><tr><td style="padding:8px 0;color:#60727d;">Judul buku</td><td style="padding:8px 0;font-weight:bold;">{{ $reservation->book?->judul_buku ?? 'Buku' }}</td></tr>
@if($status === 'disetujui')<tr><td style="padding:8px 0;color:#60727d;">Tanggal ambil</td><td style="padding:8px 0;">{{ optional($reservation->borrowing?->borrowed_at ?? now())->format('d M Y') }}</td></tr>@else<tr><td style="padding:8px 0;color:#60727d;">Alasan</td><td style="padding:8px 0;">{{ $reservation->rejection_reason ?: 'Tidak ada alasan yang dicantumkan.' }}</td></tr>@endif</table>
<p style="margin-bottom:0;">Terima kasih,<br><strong>{{ config('app.name') }}</strong></p></div></div>
</body></html>
