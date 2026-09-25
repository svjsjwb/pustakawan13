<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

        protected $fillable = [
        'judul_buku',
        'penulis',
        'isbn',
        'publisher',
        'publication_year',
        'edition',
        'call_number',
        'ddc',
        'description',
        'cover',
        'category_id',
        'subcategory_id',
        'main_category',
        'sub_category',
        'education_level',
        'stok',
        'status',
        'no_iventaris',
        'kode_buku',
        'rak',
        'sku',
    ];

    /*
     * |--------------------------------------------------------------------------
     * | ACCESSORS & MUTATORS (alias kolom lama <-> baru)
     * |--------------------------------------------------------------------------
     */

    public function getTitleAttribute(): ?string
    {
        return $this->attributes['judul_buku'] ?? null;
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['judul_buku'] = $value;
    }

    public function getJudulBukuAttribute(): ?string
    {
        return $this->attributes['judul_buku'] ?? null;
    }

    public function setJudulBukuAttribute($value): void
    {
        $this->attributes['judul_buku'] = $value;
    }

    public function getAuthorAttribute(): ?string
    {
        return $this->attributes['penulis'] ?? null;
    }

    public function setAuthorAttribute($value): void
    {
        $this->attributes['penulis'] = $value;
    }

    public function getPenulisAttribute(): ?string
    {
        return $this->attributes['penulis'] ?? null;
    }

    public function setPenulisAttribute($value): void
    {
        $this->attributes['penulis'] = $value;
    }

    public function getStockAttribute(): int
    {
        return (int) ($this->attributes['stok'] ?? 0);
    }

    public function setStockAttribute($value): void
    {
        $this->attributes['stok'] = (int) $value;
    }

    public function getAvailableStockAttribute(): int
    {
        return (int) ($this->attributes['stok'] ?? 0);
    }

    public function setAvailableStockAttribute($value): void
    {
        $this->attributes['stok'] = (int) $value;
    }

    public function getStokAttribute(): int
    {
        return (int) ($this->attributes['stok'] ?? 0);
    }

    public function setStokAttribute($value): void
    {
        $this->attributes['stok'] = (int) $value;
    }

    /*
     * |--------------------------------------------------------------------------
     * | RELATIONSHIPS
     * |--------------------------------------------------------------------------
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class, 'rak', 'code');
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'book_id');
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
