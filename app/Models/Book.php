<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_buku',
        'penulis',
        'category_id',
        'stok',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
