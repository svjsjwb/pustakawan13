<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'book_id',
        'book_copy_id',
        'borrowing_id',
        'reserved_at',
        'expires_at',
        'status',
        'seat_number',
    ];

    protected $casts = [
        'reserved_at' => 'date',
        'expires_at'  => 'date',
    ];

    // ─── Relations ──────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    // ─── Alias Atribut Bahasa Indonesia ───────────────

    public function getIdReservasiAttribute(): ?int
    {
        return $this->id;
    }

    public function getBukuIdAttribute(): ?int
    {
        return $this->book_id;
    }

    public function getNamaUserAttribute(): string
    {
        return $this->user?->name ?? $this->member?->name ?? '-';
    }

    // ─── Helpers ─────────────────────────────────────

    /**
     * Apakah reservasi ini masih bisa dibatalkan oleh user?
     */
    public function isCancellable(): bool
    {
        return $this->status === 'menunggu';
    }

    /**
     * Hitung sisa jam sebelum expires_at (untuk badge countdown).
     */
    public function hoursUntilExpiry(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return (int) max(0, now()->diffInHours($this->expires_at, false));
    }

    /**
     * Badge label sesuai status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu'   => 'Menunggu Konfirmasi',
            'disetujui'  => 'Siap Diambil',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            'selesai'    => 'Selesai',
            default      => ucfirst($this->status),
        };
    }

    /**
     * Warna badge inline style sesuai status.
     */
    public function statusStyle(): array
    {
        return match ($this->status) {
            'menunggu'   => [
                'bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a',
            ],
            'disetujui'  => [
                'bg' => '#ecfdf5', 'color' => '#047857', 'border' => '#6ee7b7',
            ],
            'ditolak', 'dibatalkan' => [
                'bg' => '#f8fafc', 'color' => '#475569', 'border' => '#cbd5e1',
            ],
            'selesai'    => [
                'bg' => '#f0fdfa', 'color' => '#0f766e', 'border' => '#99f6e4',
            ],
            default => [
                'bg' => '#f1f5f9', 'color' => '#334155', 'border' => '#e2e8f0',
            ],
        };
    }
}