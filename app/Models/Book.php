<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'judul_buku',
        'penulis',
        'category_id',
        'subcategory_id',
        'stok',
        'status',
        'no_iventaris',
        'kode_buku',
        'ddc',
        'rak',
        'sku',
        'edition',
    ];

    /*
     * |--------------------------------------------------------------------------
     * | ACCESSORS (alias kolom lama → baru)
     * |--------------------------------------------------------------------------
     */

    public function getTitleAttribute(): ?string
    {
        return $this->attributes['judul_buku'] ?? null;
    }

    public function getAuthorAttribute(): ?string
    {
        return $this->attributes['penulis'] ?? null;
    }

    public function getStockAttribute(): int
    {
        return (int) ($this->attributes['stok'] ?? 0);
    }

    public function getAvailableStockAttribute(): int
    {
        return (int) ($this->attributes['stok'] ?? 0);
    }

    /*
     * |--------------------------------------------------------------------------
     * | CATEGORY
     * |--------------------------------------------------------------------------
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'category_id'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | SUBCATEGORY
     * |--------------------------------------------------------------------------
     */

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(
            Subcategory::class,
            'subcategory_id'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | RACK
     * |--------------------------------------------------------------------------
     */

    public function rack(): BelongsTo
    {
        return $this->belongsTo(
            Rack::class,
            'rak',
            'code'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | BORROWING DETAILS
     * |--------------------------------------------------------------------------
     */

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(
            BorrowingDetail::class
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | BOOK COPIES
     * |--------------------------------------------------------------------------
     */

    public function copies(): HasMany
    {
        return $this->hasMany(
            BookCopy::class
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | FAVORITES
     * |--------------------------------------------------------------------------
     */

    public function favorites(): HasMany
    {
        return $this->hasMany(
            UserFavorite::class,
            'book_id'
        );
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
