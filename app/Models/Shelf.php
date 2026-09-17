<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelf extends Model
{
    protected $fillable = [
        'library_zone_id',
        'code',
        'name',
        'row_count',
        'column_count',
        'position_x',
        'position_y',
        'width',
        'height',
        'depth',
    ];

    protected $casts = [
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'depth' => 'decimal:2',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(
            LibraryZone::class,
            'library_zone_id'
        );
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }
}
