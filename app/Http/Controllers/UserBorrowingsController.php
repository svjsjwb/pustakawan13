<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($borrowing->status !== 'dipinjam') {
            return response()->json(['message' => 'Buku tidak sedang dipinjam.'], 422);
        }

        if ($borrowing->due_at?->isPast()) {
            return response()->json(['message' => 'Buku tidak dapat diperpanjang karena sudah melewati tanggal jatuh tempo.'], 422);
        }

        $bookIds = $borrowing->details()->pluck('book_id');
        $hasQueue = Reservation::whereIn('book_id', $bookIds)
            ->where('member_id', '!=', $member->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($hasQueue) {
            return response()->json(['message' => 'Buku tidak dapat diperpanjang karena sedang dalam antrean reservasi.'], 409);
        }

        $currentDue = $borrowing->due_at;
        $maxExtensionDays = 14;
        $maxDate = $currentDue->copy()->addDays($maxExtensionDays);

        $validated = $request->validate([
            'new_due_date' => [
                'required',
                'date',
                'after:' . $currentDue->toDateString(),
                'before_or_equal:' . $maxDate->toDateString(),
            ],
        ], [
            'new_due_date.required'        => 'Tanggal pengembalian baru wajib dipilih.',
            'new_due_date.date'            => 'Format tanggal tidak valid.',
            'new_due_date.after'           => 'Tanggal baru harus setelah tanggal jatuh tempo saat ini.',
            'new_due_date.before_or_equal' => "Perpanjangan maksimal adalah {$maxExtensionDays} hari.",
        ]);

        $newDueDate = \Carbon\Carbon::parse($validated['new_due_date']);
        $extensionDays = (int) $currentDue->diffInDays($newDueDate);

        $borrowing->update([
            'due_at'           => $newDueDate,
            'extension_status' => 'disetujui',
            'extension_reason' => "Perpanjangan mandiri oleh pengguna (+{$extensionDays} hari)",
        ]);

        $activeBorrowings = Borrowing::where('member_id', $member->id)
            ->where('status', 'dipinjam')
            ->get();

        $user = Auth::user();
        if ($user && method_exists($user, 'notificationsAllowed') && $user->notificationsAllowed('extension')) {
            $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';
            \App\Models\AppNotification::notifyAdmin(
                'extension_approved',
                'Perpanjangan Peminjaman Mandiri',
                "{$user->name} memperpanjang peminjaman buku \"{$bookTitle}\" (+{$extensionDays} hari) hingga {$borrowing->due_at->format('d M Y')}.",
                ['borrowing_id' => $borrowing->id]
            );
        }

        return response()->json([
            'message'        => 'Peminjaman berhasil diperpanjang.',
            'due_at'         => $borrowing->due_at->format('d M Y'),
            'days_remaining' => (int) now()->diffInDays($borrowing->due_at, false),
            'extension_days' => $extensionDays,
            'near_due_count' => $activeBorrowings->filter(fn ($item) => ($item->due_at ? now()->diffInDays($item->due_at, false) : 99) >= 0 && ($item->due_at ? now()->diffInDays($item->due_at, false) : 99) <= 3)->count(),
            'overdue_count'  => $activeBorrowings->filter(fn ($item) => ($item->due_at ? now()->diffInDays($item->due_at, false) : 0) < 0)->count(),
        ]);
    }
}
