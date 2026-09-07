<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'division',
        'phone',
        'address',
        'status',
    ];

    /**
     * Relasi ke Akun Pengguna (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}