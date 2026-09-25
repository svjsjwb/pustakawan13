<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'title',
        'description',
        'pinned_at',
    ];

    /**
     * Relasi ke user yang sudah membaca aktivitas ini.
     *
     * Relasi ini hanya digunakan untuk aktivitas MANUAL
     * yang berfungsi sebagai announcement/pengumuman.
     */
    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}