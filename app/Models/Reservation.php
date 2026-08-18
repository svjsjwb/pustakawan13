<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'member_id',
        'book_id',
        'reserved_at',
        'expires_at',
        'status',
        'seat_number',
    ];

    protected $casts = [
        'reserved_at' => 'date',
        'expires_at' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
