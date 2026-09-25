<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBorrowingController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        $borrowings = collect();
        $completedBorrowings = collect();
        $completedCount = 0;

        if ($member) {
            $completedCount = Borrowing::where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->count();

            $completedBorrowings = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->orderByDesc('returned_at')
                ->take(5)
                ->get();

            // Peminjaman aktif (dipinjam)
            $borrowings = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id)
                ->where('status', 'dipinjam')
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($b) {
                    $b->days_remaining = $b->due_at
                        ? (int) now()->diffInDays($b->due_at, false)
                        : null;
                    return $b;
                });
        }

        return view('user.loans', compact('borrowings', 'completedBorrowings', 'member', 'completedCount'));
    }

    /**
     * User mengajukan pinjam buku dari katalog
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'days'    => 'nullable|integer|min:1|max:30',
        ]);

        $user = Auth::user();
        $member = Member::firstOrCreate(
            ['email' => $user->email],
            [
                'name'    => $user->name,
                'phone'   => '-',
                'address' => '-',
                'status'  => 'aktif',
            ]
        );
        if (!$member->user_id) {
            $member->update(['user_id' => $user->id]);
        }
        $member->update(['status' => 'aktif']);

        $borrowedAt = now();
        $dueAt = now()->addDays(14);
        $newBorrowing = null;
        $borrowedBook = null;

        try {
            DB::transaction(function () use ($member, $validated, $borrowedAt, $dueAt, &$newBorrowing, &$borrowedBook, $user) {
                $book = Book::lockForUpdate()->findOrFail($validated['book_id']);
                $borrowedBook = $book;

                $copy = BookCopy::where('book_id', $book->id)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (!$copy || $book->available_stock < 1) {
                    throw new \RuntimeException('Maaf, stok buku ini sedang habis.');
                }

                $alreadyBorrowed = Borrowing::where('member_id', $member->id)
                    ->where('status', 'dipinjam')
                    ->whereHas('details', fn ($query) => $query->where('book_id', $book->id))
                    ->exists();

                if ($alreadyBorrowed) {
                    throw new \RuntimeException('Buku ini sedang Anda pinjam.');
                }

                $newBorrowing = Borrowing::create([
                    'member_id'   => $member->id,
                    'user_id'     => $user->id,
                    'book_id'     => $book->id,
                    'borrowed_at' => $borrowedAt->toDateString(),
                    'due_at'      => $dueAt->toDateString(),
                    'status'      => 'dipinjam',
                ]);

                BorrowingDetail::create([
                    'borrowing_id' => $newBorrowing->id,
                    'book_id'      => $book->id,
                    'book_copy_id' => $copy->id,
                    'quantity'     => 1,
                ]);

                $copy->update(['status' => 'borrowed']);
                $book->decrement('stok');
            });

            if ($newBorrowing) {
                NotificationService::borrowingSubmitted($newBorrowing, Auth::user());

                // Kirim notifikasi instan ke Admin
                AppNotification::notifyAdmin(
                    'borrowing_new',
                    'Peminjaman Buku Baru',
                    "{$user->name} meminjam buku \"{$borrowedBook?->title}\".",
                    ['borrowing_id' => $newBorrowing->id, 'book_id' => $borrowedBook?->id]
                );
            }
        } catch (\RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 409);
            }
            return back()->with('error', $exception->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message'      => 'Buku berhasil dipinjam.',
                'redirect_url' => route('borrowings.index'),
            ]);
        }

        return redirect()->route('borrowings.index')->with('success', 'Buku berhasil dipinjam.');
    }

    /**
     * User mengajukan perpanjangan peminjaman menggunakan date picker.
     * Validasi: tanggal harus > due_at sekarang dan max 14 hari dari due_at.
     */
    public function requestExtension(Request $request, Borrowing $borrowing)
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        if (!$member || $borrowing->member_id !== $member->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($borrowing->status !== 'dipinjam') {
            return back()->with('error', 'Hanya peminjaman aktif yang dapat diperpanjang.');
        }

        if ($borrowing->extension_status === 'menunggu') {
            return back()->with('error', 'Anda sudah mengajukan perpanjangan untuk peminjaman ini yang sedang menunggu persetujuan.');
        }

        // Batas perpanjangan: max 14 hari dari jatuh tempo saat ini
        $maxExtensionDays = 14;
        $currentDue       = $borrowing->due_at; // Carbon instance
        $maxDate          = $currentDue->copy()->addDays($maxExtensionDays);

        $validated = $request->validate([
            'new_due_date' => [
                'required',
                'date',
                'after:' . $currentDue->toDateString(),
                'before_or_equal:' . $maxDate->toDateString(),
            ],
            'reason' => 'nullable|string|max:500',
        ], [
            'new_due_date.required'        => 'Tanggal pengembalian baru wajib dipilih.',
            'new_due_date.date'            => 'Format tanggal tidak valid.',
            'new_due_date.after'           => 'Tanggal harus lebih besar dari tanggal jatuh tempo saat ini.',
            'new_due_date.before_or_equal' => "Perpanjangan melebihi batas maksimum {$maxExtensionDays} hari.",
        ]);

        $requestedDue  = \Carbon\Carbon::parse($validated['new_due_date']);
        $extensionDays = (int) $currentDue->diffInDays($requestedDue);

        $borrowing->update([
            'extension_status'           => 'menunggu',
            'extension_requested_due_at' => $requestedDue->toDateString(),
            'extension_reason'           => $validated['reason'] ?? "Perpanjangan peminjaman {$extensionDays} hari",
        ]);

        $borrowing->load('details.book');
        $bookTitle = $borrowing->details->first()?->book?->title ?? 'Buku';

        // Notifikasi ke Admin
        AppNotification::notifyAdmin(
            'extension_request',
            'Permintaan Perpanjangan Peminjaman',
            "{$user->name} mengajukan perpanjangan peminjaman buku \"{$bookTitle}\" hingga " . $requestedDue->format('d M Y') . ".",
            ['borrowing_id' => $borrowing->id]
        );

        return back()->with('success', 'Permintaan perpanjangan berhasil diajukan dan sedang menunggu persetujuan Admin.');
    }
}
