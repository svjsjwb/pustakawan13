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
        $hasActiveBorrowing = Borrowing::where('member_id', $member->id)
            ->whereNull('returned_at')
            ->exists();

        $hasActiveReservation = Reservation::where('member_id', $member->id)
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->exists();

        $member->update([
            'status' => ($hasActiveBorrowing || $hasActiveReservation)
                ? 'aktif'
                : 'nonaktif',
        ]);
    }

    /**
     * Sinkronkan seluruh member tanpa query per member.
     */
    public function syncAll(): void
    {
        $activeBorrowingIds = Borrowing::query()
            ->whereNull('returned_at')
            ->whereNotNull('member_id')
            ->distinct()
            ->pluck('member_id');

        $activeReservationIds = Reservation::query()
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->whereNotNull('member_id')
            ->distinct()
            ->pluck('member_id');

        $activeMemberIds = $activeBorrowingIds
            ->merge($activeReservationIds)
            ->unique()
            ->values();

        Member::query()
            ->whereIn('id', $activeMemberIds)
            ->where('status', '!=', 'aktif')
            ->update(['status' => 'aktif']);

        Member::query()
            ->whereNotIn('id', $activeMemberIds)
            ->where('status', '!=', 'nonaktif')
            ->update(['status' => 'nonaktif']);
    }
}
