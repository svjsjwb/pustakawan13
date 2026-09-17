<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionWithdrawal extends Model
{
    protected $fillable = [
        'type',
        'book_id',
        'book_copy_id',
        'book_title',
        'barcode',
        'quantity',
        'reason',
        'withdrawn_at',
    ];

    protected $casts = [
        'withdrawn_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }
}