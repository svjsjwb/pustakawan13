<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookProposal extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'title',
        'author',
        'publisher',
        'year',
        'category_name',
        'reason',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'  => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }
}
