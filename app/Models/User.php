<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'theme',
        'layout_density',
        'allow_notifications',
        'email_notifications',
        'reservation_notifications',
        'borrowing_notifications',
        'extension_notifications',
        'return_reminder_notifications',
        'late_return_notifications',
        'fine_notifications',
        'favorite_categories',
        'favorite_genres',
        'last_theme_used',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'             => 'datetime',
            'last_login_at'                 => 'datetime',
            'password'                      => 'hashed',
            'allow_notifications'           => 'boolean',
            'email_notifications'           => 'boolean',
            'reservation_notifications'     => 'boolean',
            'borrowing_notifications'       => 'boolean',
            'extension_notifications'       => 'boolean',
            'return_reminder_notifications' => 'boolean',
            'late_return_notifications'     => 'boolean',
            'fine_notifications'            => 'boolean',
            'favorite_categories'           => 'array',
            'favorite_genres'               => 'array',
        ];
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah user biasa.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Cek apakah notifikasi jenis tertentu diizinkan.
     * Types: reservation, borrowing, extension, return_reminder, late_return, email
     */
    public function notificationsAllowed(string $type = ''): bool
    {
        // Jika master switch OFF, tidak ada notifikasi yang boleh dikirim
        if (!($this->allow_notifications ?? true)) {
            return false;
        }

        return match ($type) {
            'reservation'     => (bool) ($this->reservation_notifications ?? true),
            'borrowing'       => (bool) ($this->borrowing_notifications ?? true),
            'extension'       => (bool) ($this->extension_notifications ?? true),
            'return_reminder' => (bool) ($this->return_reminder_notifications ?? true),
            'late_return',
            'fine'            => (bool) ($this->late_return_notifications ?? $this->fine_notifications ?? true),
            'email'           => (bool) ($this->email_notifications ?? true),
            default           => true,
        };
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'email', 'email');
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function bookProposals()
    {
        return $this->hasMany(BookProposal::class);
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if ($user->role === 'user' && !empty($user->email)) {
                Member::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'user_id'  => $user->id,
                        'name'     => $user->name,
                        'phone'    => $user->phone ?? '-',
                        'division' => 'Anggota',
                        'status'   => 'aktif',
                    ]
                );
            }
        });
    }

    public function syncToMember(): Member
    {
        return Member::updateOrCreate(
            ['email' => $this->email],
            [
                'user_id'  => $this->id,
                'name'     => $this->name,
                'phone'    => $this->phone ?? '-',
                'division' => 'Anggota',
                'status'   => 'aktif',
            ]
        );
    }
}
