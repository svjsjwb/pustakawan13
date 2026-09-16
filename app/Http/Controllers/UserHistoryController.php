<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        $activities   = collect();
        $stats = [
            'total_borrowed'     => 0,
            'total_reservations' => 0,
            'active_borrowed'    => 0,
            'returned_borrowed'  => 0,
        ];

        if ($member) {
            $search = trim((string) $request->input('search', ''));
            $month  = $request->input('month', '');
            $year   = $request->input('year', '');

            // -- Statistik hero (seluruh data, tanpa filter) --
            $allBorrowings = Borrowing::where('member_id', $member->id)->get();
            $stats['total_borrowed']     = $allBorrowings->count();
            $stats['active_borrowed']    = $allBorrowings->where('status', 'dipinjam')->count();
            $stats['returned_borrowed']  = $allBorrowings->where('status', 'dikembalikan')->count();
            $stats['total_reservations'] = Reservation::where('member_id', $member->id)->count();

            // -- Ambil semua Borrowing user (semua status) --
            $borrowingQuery = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id);

            if ($month) {
                $borrowingQuery->whereMonth('created_at', $month);
            }
            if ($year) {
                $borrowingQuery->whereYear('created_at', $year);
            }
            if ($search !== '') {
                $borrowingQuery->whereHas('details.book', fn($q) =>
                    $q->where('title', 'like', "{$search}%")
                      ->orWhere('author', 'like', "{$search}%")
                );
            }

            $borrowings = $borrowingQuery->get()->map(function ($b) {
                $detail = $b->details->first();
                $book   = $detail?->book;

                $statusRaw = strtolower($b->status ?? '');
                $isReturned = !empty($b->returned_at) || $statusRaw === 'dikembalikan' || $statusRaw === 'selesai';
                $isOverdue  = !$isReturned && (($b->due_at && now()->gt($b->due_at)) || $statusRaw === 'terlambat');

                if ($isReturned) {
                    $statusLabel = 'Selesai';
                    $badgeClass  = 'status-selesai';
                } elseif ($isOverdue) {
                    $statusLabel = 'Terlambat';
                    $badgeClass  = 'status-terlambat';
                } else {
                    // Peminjaman otomatis diproses saat user meminjam, tidak boleh 'menunggu'
                    $statusLabel = 'Dipinjam';
                    $badgeClass  = 'status-dipinjam';
                }

                return (object)[
                    'type'         => 'borrowing',
                    'id'           => 'b-' . $b->id,
                    'book'         => $book,
                    'date'         => $b->created_at,
                    'date_label'   => $b->borrowed_at
                                    ? \Carbon\Carbon::parse($b->borrowed_at)->format('d M Y')
                                    : $b->created_at->format('d M Y'),
                    'extra'        => $b->returned_at
                                    ? 'Dikembalikan ' . \Carbon\Carbon::parse($b->returned_at)->format('d M Y')
                                    : ($b->due_at ? 'Batas: ' . \Carbon\Carbon::parse($b->due_at)->format('d M Y') : '-'),
                    'status_raw'   => $b->status,
                    'status_label' => $statusLabel,
                    'badge_class'  => $badgeClass,
                ];
            });

            // -- Ambil semua Reservasi user (semua status) --
            $reservationQuery = Reservation::with('book.category')
                ->where('member_id', $member->id);

            if ($month) {
                $reservationQuery->whereMonth('created_at', $month);
            }
            if ($year) {
                $reservationQuery->whereYear('created_at', $year);
            }
            if ($search !== '') {
                $reservationQuery->whereHas('book', fn($q) =>
                    $q->where('title', 'like', "{$search}%")
                      ->orWhere('author', 'like', "{$search}%")
                );
            }

            $reservations = $reservationQuery->get()->map(function ($r) {
                $statusRaw = strtolower($r->status ?? '');
                $statusLabel = match($statusRaw) {
                    'menunggu'                  => 'Menunggu Persetujuan',
                    'disetujui', 'siap_diambil' => 'Disetujui',
                    'ditolak', 'dibatalkan'     => 'Ditolak',
                    'selesai'                   => 'Selesai',
                    default                     => ucfirst($r->status),
                };
                $badgeClass = match($statusRaw) {
                    'menunggu'                  => 'status-menunggu',   // oranye/kuning
                    'disetujui', 'siap_diambil' => 'status-disetujui',  // hijau
                    'ditolak', 'dibatalkan'     => 'status-ditolak',    // merah
                    'selesai'                   => 'status-selesai',    // BIRU LANGIT (sky blue)
                    default                     => 'status-menunggu',
                };
                return (object)[
                    'type'         => 'reservation',
                    'id'           => 'r-' . $r->id,
                    'book'         => $r->book,
                    'date'         => $r->created_at,
                    'date_label'   => $r->created_at?->format('d M Y') ?? '-',
                    'extra'        => $r->expires_at
                                        ? 'Berlaku s/d: ' . \Carbon\Carbon::parse($r->expires_at)->format('d M Y')
                                        : '-',
                    'status_raw'   => $r->status,
                    'status_label' => $statusLabel,
                    'badge_class'  => $badgeClass,
                ];
            });

            // -- Gabungkan dan urutkan berdasarkan tanggal terbaru --
            $activities = $borrowings->concat($reservations)
                ->sortByDesc('date')
                ->values();
        }

        return view('user.history', compact('activities', 'member', 'stats'));
    }
}
