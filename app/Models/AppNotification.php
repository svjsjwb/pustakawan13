<?php

namespace App\Models;

use App\Events\NotificationCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppNotification extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (self $notification): void {
            DB::afterCommit(function () use ($notification): void {
                try {
                    broadcast(new NotificationCreated($notification));
                } catch (\Throwable $e) {
                    Log::warning('Gagal broadcast notifikasi Reverb: ' . $e->getMessage());
                }
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper cepat membuat notifikasi untuk Admin
     */
    public static function notifyAdmin(string $type, string $title, string $message, ?array $data = null): self
    {
        return self::create([
            'user_id' => null,
            'role'    => 'admin',
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Helper cepat membuat notifikasi untuk User
     */
    public static function notifyUser(int $userId, string $type, string $title, string $message, ?array $data = null): self
    {
        return self::create([
            'user_id' => $userId,
            'role'    => 'user',
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
            'is_read' => false,
        ]);
    }
}
