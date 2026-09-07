<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'google_id',
    'phone',
    'location',
    'avatar',
    'is_notification_enabled',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $attributes = [
        'is_notification_enabled' => true,
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_notification_enabled' => 'boolean',
        ];
    }

    /**
     * Fallback sinkronisasi ke tabel members jika trigger DB belum menangani
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role === 'user') {
                $memberExists = Member::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->exists();

                if (!$memberExists) {
                    Member::create([
                        'user_id'    => $user->id,
                        'name'       => $user->name,
                        'email'      => $user->email,
                        'phone'      => $user->phone ?: '-',
                        'division'   => 'Anggota',
                        'status'     => 'Aktif',
                    ]);
                }
            }
        });
    }

    /**
     * Relasi ke Data Keanggotaan (Member)
     */
    public function member()
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Relasi ke UserFavorite
     */
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Relasi Many-to-Many ke Buku Favorit
     */
    public function favoriteBooks()
    {
        return $this->belongsToMany(Book::class, 'user_favorites', 'user_id', 'book_id')->withTimestamps();
    }

    /**
     * Relasi ke Reservasi Pengguna
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Reservasi aktif pengguna (menunggu + disetujui).
     */
    public function activeReservations()
    {
        return $this->hasMany(Reservation::class)
            ->whereIn('status', ['menunggu', 'disetujui']);
    }
}