<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'user_id',
        'member_number',
        'name',
        'email',
        'division',
        'phone',
        'address',
        'nis_nip',
        'gender',
        'class',
        'registered_at',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (empty($member->member_number)) {
                $member->member_number = static::generateMemberNumber();
            }
        });
    }

    public static function generateMemberNumber(): string
    {
        $lastNumber = static::where('member_number', 'like', 'MBR-%')
            ->orderByDesc('id')
            ->value('member_number');

        if ($lastNumber && preg_match('/^MBR-(\d+)$/', $lastNumber, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        do {
            $memberNumber = 'MBR-' . str_pad(
                (string) $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );
            $nextNumber++;
        } while (
            static::where('member_number', $memberNumber)->exists()
        );

        return $memberNumber;
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = $value ? strtolower(trim((string) $value)) : 'nonaktif';
    }

    /**
     * Relasi ke Akun Pengguna (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}