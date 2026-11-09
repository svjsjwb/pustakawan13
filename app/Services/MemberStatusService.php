<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;

class MemberStatusService
{
    public function sync(Member $member): void
    {
        // Guest selalu nonaktif
        if ($member->user && $member->user->role === 'guest') {
            $member->update([
                'status' => 'nonaktif'
            ]);

            return;
        }

        // Hanya member yang diproses sebagai anggota aktif/nonaktif
        if ($member->user && $member->user->role === 'member') {

            // Cek peminjaman yang masih berlangsung
            $hasActiveBorrowing = $member->borrowings()
                ->whereNull('returned_at')
                ->whereIn('status', [
                    'dipinjam',
                    'diperpanjang',
                    'terlambat',
                ])
                ->exists();

            // Cek reservasi yang masih berlangsung
            $hasActiveReservation = $member->reservations()
                ->whereNotIn('status', [
                    'selesai',
                    'ditolak',
                    'dibatalkan',
                ])
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->exists();

            $newStatus = ($hasActiveBorrowing || $hasActiveReservation) ? 'aktif' : 'nonaktif';
            if ($member->status !== $newStatus) {
                $member->update(['status' => $newStatus]);
            }

            return;
        }

        // Jika tidak memiliki user / role yang valid
        if ($member->status !== 'nonaktif') {
            $member->update([
                'status' => 'nonaktif'
            ]);
        }
    }

    /**
     * Sinkronkan seluruh member.
     */
    public function syncAll(): void
    {
        /*
         * Guest selalu nonaktif.
         */
        Member::whereHas('user', function ($q) {
            $q->where('role', 'guest');
        })->where('status', '!=', 'nonaktif')
          ->update([
              'status' => 'nonaktif'
          ]);

        /*
         * Member perlu dihitung berdasarkan aktivitasnya secara batch.
         */
        $activeMemberIds = Member::whereHas('user', function ($q) {
            $q->where('role', 'member');
        })->where(function ($query) {
            $query->whereHas('borrowings', function ($b) {
                $b->whereNull('returned_at')
                  ->whereIn('status', ['dipinjam', 'diperpanjang', 'terlambat']);
            })->orWhereHas('reservations', function ($r) {
                $r->whereNotIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                  ->where(function ($sub) {
                      $sub->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                  });
            });
        })->pluck('id');

        Member::whereIn('id', $activeMemberIds)
            ->where('status', '!=', 'aktif')
            ->update(['status' => 'aktif']);

        Member::whereNotIn('id', $activeMemberIds)
            ->where('status', '!=', 'nonaktif')
            ->update(['status' => 'nonaktif']);
    }
}