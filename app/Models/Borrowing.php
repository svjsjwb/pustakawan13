<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    protected $fillable = [
        'member_id',
        'user_id',
        'reservation_id',
        'book_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'seat_number',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // ─── Alias Atribut Bahasa Indonesia ───────────────

    public function getIdReservasiAttribute(): ?int
    {
        return $this->reservation_id ?? $this->reservation?->id;
    }

    public function getBukuIdAttribute(): ?int
    {
        return $this->book_id ?? $this->details->first()?->book_id;
    }

    public function getTglPinjamAttribute()
    {
        return $this->borrowed_at;
    }

    public function getTglHarusKembaliAttribute()
    {
        return $this->due_at;
    }

    public function getStatusPinjamAttribute(): ?string
    {
        return $this->status ? ucfirst($this->status) : null;
    }
}
