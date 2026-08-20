<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryFloor extends Model
{
    protected $fillable = [
        'name',
        'floor_number',
        'description',
    ];

    public function zones(): HasMany
    {
        return $this->hasMany(LibraryZone::class);
    }
}
