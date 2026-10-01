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

            $member->update([
                'status' => ($hasActiveBorrowing || $hasActiveReservation)
                    ? 'aktif'
                    : 'nonaktif'
            ]);

            return;
        }

        // Jika tidak memiliki user / role yang valid
        $member->update([
            'status' => 'nonaktif'
        ]);
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
         * Member perlu dihitung berdasarkan aktivitasnya.
         */
        Member::whereHas('user', function ($q) {
            $q->where('role', 'member');
        })
        ->with(['borrowings', 'reservations'])
        ->get()
        ->each(function (Member $member) {

            $hasActiveBorrowing = $member->borrowings()
                ->whereNull('returned_at')
                ->whereIn('status', [
                    'dipinjam',
                    'diperpanjang',
                    'terlambat',
                ])
                ->exists();

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

            $member->update([
                'status' => ($hasActiveBorrowing || $hasActiveReservation)
                    ? 'aktif'
                    : 'nonaktif'
            ]);
        });
    }
}