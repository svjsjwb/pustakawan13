<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;

class MemberStatusService
{
    /**
     * Sinkronkan status satu member berdasarkan tanggung jawab yang masih berlangsung.
     *
     * Aktif jika masih memiliki:
     * - peminjaman yang belum dikembalikan; atau
     * - reservasi yang belum selesai/ditolak/dibatalkan dan belum kedaluwarsa.
     */
    public function sync(Member $member): void
    {
        if ($member->user && $member->user->role === 'guest') {
            $member->update(['status' => 'nonaktif']);
            return;
        }

        if ($member->user && $member->user->role === 'member') {
            $member->update(['status' => 'aktif']);
            return;
        }
    }

    /**
     * Sinkronkan seluruh member tanpa query per member.
     */
    public function syncAll(): void
    {
        // Pendaftar yang masih berstatus guest selalu nonaktif
        Member::whereHas('user', function ($q) {
            $q->where('role', 'guest');
        })->where('status', '!=', 'nonaktif')
          ->update(['status' => 'nonaktif']);

        // Anggota yang sudah disetujui admin (role member) selalu aktif
        Member::whereHas('user', function ($q) {
            $q->where('role', 'member');
        })->where('status', '!=', 'aktif')
          ->update(['status' => 'aktif']);
    }
}
