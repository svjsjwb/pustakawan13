<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserBorrowingsController extends Controller
{
    public function index()
    {
        $member = Member::where('email', Auth::user()->email)->first();
        $borrowings = collect();
        $completedBorrowings = collect();

        if ($member) {
            $borrowings = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id)
                ->where('status', 'dipinjam')
                ->latest()
                ->get()
                ->map(function (Borrowing $borrowing) {
                    $borrowing->days_remaining = $borrowing->due_at
                        ? (int) now()->diffInDays($borrowing->due_at, false)
                        : null;
                    $bookIds = $borrowing->details->pluck('book_id');
                    $borrowing->has_reservation_queue = Reservation::whereIn('book_id', $bookIds)
                        ->where('member_id', '!=', $borrowing->member_id)
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->exists();
                    return $borrowing;
                });

            $completedBorrowings = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->latest('returned_at')
                ->take(5)
                ->get();

        }

        return view('user.borrowings.index', [
            'member' => $member,
            'borrowings' => $borrowings,
            'completedBorrowings' => $completedBorrowings,
            'completedCount' => $member ? Borrowing::where('member_id', $member->id)->where('status', 'dikembalikan')->count() : 0,
        ]);
    }

    public function extend(Request $request, Borrowing $borrowing)
    {
        $member = Member::where('email', Auth::user()->email)->first();

        if (!$member || $borrowing->member_id !== $member->id) {
            abort(403, 'Akses tidak sah.');
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
        ]);

        if ($borrowing->status !== 'dipinjam') {
            return response()->json(['message' => 'Buku tidak sedang dipinjam.'], 422);
        }

        if ($borrowing->due_at?->isPast()) {
            return response()->json(['message' => 'Buku tidak dapat diperpanjang karena sudah terlambat.'], 422);
        }

        $bookIds = $borrowing->details()->pluck('book_id');
        $hasQueue = Reservation::whereIn('book_id', $bookIds)
            ->where('member_id', '!=', $member->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($hasQueue) {
            return response()->json(['message' => 'Buku tidak dapat diperpanjang karena sedang dalam antrean reservasi.'], 409);
        }

        $currentDue = $borrowing->due_at->copy()->startOfDay();
        $newDue = Carbon::parse($validated['return_date'])->startOfDay();
        $maximumDue = $currentDue->copy()->addDays(14);

        if ($newDue->lt($currentDue) || $newDue->gt($maximumDue)) {
            return response()->json(['message' => 'Tanggal pengembalian harus berada antara jatuh tempo saat ini dan maksimal 14 hari setelahnya.'], 422);
        }

        $borrowing->update([
            'due_at' => $newDue->toDateString(),
            'extension_status' => 'disetujui',
            'extension_reason' => 'Perpanjangan mandiri oleh pengguna',
        ]);

        $activeBorrowings = Borrowing::where('member_id', $member->id)
            ->where('status', 'dipinjam')
            ->get();

        return response()->json([
            'message' => 'Peminjaman berhasil diperpanjang.',
            'due_at' => $borrowing->due_at->format('d M Y'),
            'days_remaining' => (int) now()->diffInDays($borrowing->due_at, false),
            'near_due_count' => $activeBorrowings->filter(fn ($item) => ($item->due_at ? now()->diffInDays($item->due_at, false) : 99) >= 0 && ($item->due_at ? now()->diffInDays($item->due_at, false) : 99) <= 3)->count(),
            'overdue_count' => $activeBorrowings->filter(fn ($item) => ($item->due_at ? now()->diffInDays($item->due_at, false) : 0) < 0)->count(),
        ]);
    }
}
