<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_number',
        'name',
        'nis_nip',
        'gender',
        'class',
        'address',
        'phone',
        'email',
        'registered_at',
        'status',
    ];

    protected $casts = [
        'registered_at' => 'date',
    ];
}