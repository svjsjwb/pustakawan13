<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id',
        'shelf_id',
        'section',
        'row',
        'column',
        'side',
        'copy',
        'status',
        'condition',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function borrowingDetail(): HasOne
    {
        return $this->hasOne(BorrowingDetail::class);
    }

    public function reservation(): HasOne
    {
        return $this->hasOne(Reservation::class);
    }

    public function syncStatus(): void
    {
        if ($this->condition === 'hilang') {
            $this->updateQuietly([
                'status' => 'lost',
            ]);

            return;
        }

        if ($this->condition === 'rusak') {
            $this->updateQuietly([
                'status' => 'maintenance',
            ]);

            return;
        }

        if (
            in_array(
                $this->status,
                ['maintenance', 'lost']
            )
        ) {
            $this->updateQuietly([
                'status' => 'available',
            ]);
        }
    }
}
