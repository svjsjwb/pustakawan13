<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
    ];

    /**
     * Subcategory belongs to one Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Subcategory has many Books.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}