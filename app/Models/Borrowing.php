<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    protected $fillable = [
        'member_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'rejection_reason',
        'extension_status',
        'extension_requested_due_at',
        'extension_reason',
        'extension_admin_notes',
        'seat_number',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
        'extension_requested_due_at' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'     => 'Menunggu Persetujuan',
            'dipinjam'     => 'Dipinjam',
            'ditolak'      => 'Ditolak',
            'dikembalikan' => 'Dikembalikan',
            'terlambat'    => 'Terlambat',
            default        => ucfirst($this->status),
        };
    }

    public function getDisplayExtensionStatusAttribute(): ?string
    {
        return match ($this->extension_status) {
            'menunggu'  => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => null,
        };
    }
}
