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
        'category_id',
        'subcategory_id',
        'main_category',
        'sub_category',
        'education_level',
        'stok',
        'status',
        'no_iventaris',
        'kode_buku',
        'ddc',
        'rak',
        'sku',
        'edition',
        'title',
        'author',
        'stock',
        'available_stock',
        'publisher',
        'publication_year',
        'isbn',
        'call_number',
        'description',
        'cover',
    ];

    /*
     * |--------------------------------------------------------------------------
     * | ACCESSORS & MUTATORS (alias kolom lama <-> baru)
     * |--------------------------------------------------------------------------
     */

    public function getTitleAttribute(): ?string
    {
        return $this->attributes['title'] ?? $this->attributes['judul_buku'] ?? null;
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['judul_buku'] = $value;
    }

    public function getJudulBukuAttribute(): ?string
    {
        return $this->attributes['judul_buku'] ?? $this->attributes['title'] ?? null;
    }

    public function setJudulBukuAttribute($value): void
    {
        $this->attributes['judul_buku'] = $value;
        $this->attributes['title'] = $value;
    }

    public function getAuthorAttribute(): ?string
    {
        return $this->attributes['author'] ?? $this->attributes['penulis'] ?? null;
    }

    public function setAuthorAttribute($value): void
    {
        $this->attributes['author'] = $value;
        $this->attributes['penulis'] = $value;
    }

    public function getPenulisAttribute(): ?string
    {
        return $this->attributes['penulis'] ?? $this->attributes['author'] ?? null;
    }

    public function setPenulisAttribute($value): void
    {
        $this->attributes['penulis'] = $value;
        $this->attributes['author'] = $value;
    }

    public function getStockAttribute(): int
    {
        return (int) ($this->attributes['stock'] ?? $this->attributes['stok'] ?? 0);
    }

    public function setStockAttribute($value): void
    {
        $this->attributes['stock'] = (int) $value;
        $this->attributes['stok']  = (int) $value;
    }

    public function getAvailableStockAttribute(): int
    {
        $avail = (int) ($this->attributes['available_stock'] ?? 0);
        if ($avail === 0 && isset($this->attributes['stok']) && (int)$this->attributes['stok'] > 0) {
            return (int) $this->attributes['stok'];
        }
        return $avail;
    }

    public function setAvailableStockAttribute($value): void
    {
        $this->attributes['available_stock'] = (int) $value;
        $this->attributes['stok']            = (int) $value;
    }

    public function getStokAttribute(): int
    {
        $stok = (int) ($this->attributes['stok'] ?? 0);
        if ($stok === 0 && isset($this->attributes['available_stock']) && (int)$this->attributes['available_stock'] > 0) {
            return (int) $this->attributes['available_stock'];
        }
        return $stok;
    }

    public function setStokAttribute($value): void
    {
        $this->attributes['stok']            = (int) $value;
        $this->attributes['available_stock'] = (int) $value;
        if (!isset($this->attributes['stock']) || (int)$this->attributes['stock'] === 0) {
            $this->attributes['stock'] = (int) $value;
        }
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
