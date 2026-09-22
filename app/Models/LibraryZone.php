<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryZone extends Model
{
    protected $fillable = [
        'library_floor_id',
        'code',
        'name',
        'description',
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(
            LibraryFloor::class,
            'library_floor_id'
        );
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class);
    }
}
