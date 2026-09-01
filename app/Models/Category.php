<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'parent_id',
        'level',
    ];

    /*
     * |--------------------------------------------------------------------------
     * | PARENT
     * |--------------------------------------------------------------------------
     */

    public function parent()
    {
        return $this->belongsTo(
            Category::class,
            'parent_id'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | CHILDREN
     * |--------------------------------------------------------------------------
     */

    public function children(): HasMany
    {
        return $this->hasMany(
            Category::class,
            'parent_id'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | SUBCATEGORIES
     * |--------------------------------------------------------------------------
     */

    public function subcategories(): HasMany
    {
        return $this->hasMany(
            Subcategory::class,
            'category_id'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | BOOKS
     * |--------------------------------------------------------------------------
     */

    public function books(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'category_id'
        );
    }
}
