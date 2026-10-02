<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Member;
use App\Models\User;
use App\Models\Reservation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBorrowingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        $borrowings = collect();
        $completedBorrowings = collect();
        $completedCount = 0;

        if ($member) {
            $completedCount = Borrowing::where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->count();

            $completedBorrowings = Borrowing::with([
                'details.book.category'
            ])
                ->where('member_id', $member->id)
                ->where('status', 'dikembalikan')
                ->latest('returned_at')
                ->take(5)
                ->get();

            $borrowings = Borrowing::with([
                'details.book.category'
            ])
                ->where('member_id', $member->id)
                ->whereIn('status', [
                    'dipinjam',
                    'diperpanjang',
                    'terlambat'
                ])
                ->latest()
                ->get()
                ->map(function (Borrowing $borrowing) {

                    $borrowing->days_remaining =
                        $borrowing->due_at
                        ? (int) now()->diffInDays(
                            $borrowing->due_at,
                            false
                        )
                        : null;

                    $bookIds = $borrowing->details
                        ->pluck('book_id');

                    $borrowing->has_reservation_queue =
                        Reservation::whereIn('book_id', $bookIds)
                        ->where(
                            'member_id',
                            '!=',
                            $borrowing->member_id
                        )
                        ->whereIn('status', [
                            'menunggu',
                            'disetujui'
                        ])
                        ->exists();

                    return $borrowing;
                });
        }

        return view('user.loans', compact(
            'borrowings',
            'completedBorrowings',
            'member',
            'completedCount'
        ));
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
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'message' => 'Data anggota tidak ditemukan.',
            ], 404);
        }

        $borrowedAt = now();
        $dueAt = now()->addDays(14);
        $newBorrowing = null;
        $borrowedBook = null;

        try {
            DB::transaction(function () use (
                $member,
                $validated,
                $borrowedAt,
                $dueAt,
                &$newBorrowing,
                &$borrowedBook,
                $user
            ) {
                User::whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $activeBookCount = BorrowingDetail::whereHas(
                    'borrowing',
                    function ($query) use ($member) {
                        $query->where(
                            'member_id',
                            $member->id
                        )
                            ->whereIn('status', [
                                'dipinjam',
                                'diperpanjang',
                                'terlambat'
                            ]);
                    }
                )->sum('quantity');

                if ($activeBookCount >= 5) {
                    throw new \RuntimeException(
                        'Maksimal 5 buku. Silakan kembalikan salah satu buku yang sedang dipinjam sebelum melakukan peminjaman baru.'
                    );
                }

                $book = Book::lockForUpdate()
                    ->findOrFail($validated['book_id']);

                $borrowedBook = $book;

                $copy = BookCopy::where(
                    'book_id',
                    $book->id
                )
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (!$copy || $book->available_stock < 1) {
                    throw new \RuntimeException(
                        'Maaf, stok buku ini sedang habis.'
                    );
                }

                $alreadyBorrowed = Borrowing::where(
                    'member_id',
                    $member->id
                )
                    ->whereIn('status', [
                        'dipinjam',
                        'diperpanjang',
                        'terlambat'
                    ])
                    ->whereHas(
                        'details',
                        fn($query) => $query->where(
                            'book_id',
                            $book->id
                        )
                    )
                    ->exists();

                if ($alreadyBorrowed) {
                    throw new \RuntimeException(
                        'Buku ini sedang Anda pinjam.'
                    );
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

                $copy->update([
                    'status' => 'borrowed'
                ]);

                $book->decrement('stok');
            });

            if ($newBorrowing) {
                NotificationService::borrowingApproved(
                    $newBorrowing,
                    $user
                );

                AppNotification::notifyAdmin(
                    'borrowing_new',
                    'Peminjaman Buku Otomatis Disetujui',
                    "{$user->name} meminjam buku \"{$borrowedBook?->title}\" dan memenuhi seluruh validasi.",
                    [
                        'borrowing_id' => $newBorrowing->id,
                        'book_id' => $borrowedBook?->id
                    ]
                );
            }
        } catch (\RuntimeException $exception) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], 409);
            }

            return back()->with(
                'error',
                $exception->getMessage()
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' =>
                'Buku berhasil dipinjam. Silakan ambil buku di meja sirkulasi.',
                'redirect_url' => route('borrowings.index'),
            ]);
        }

        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                'Buku berhasil dipinjam. Silakan ambil buku di meja sirkulasi.'
            );
    }

    /**
     * User mengajukan perpanjangan peminjaman menggunakan date picker.
        * Validasi: tanggal harus > due_at sekarang dan max 14 hari dari due_at.
     */
    public function requestExtension(
        Request $request,
        Borrowing $borrowing
    ) {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || $borrowing->member_id !== $member->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($borrowing->status !== 'dipinjam') {
            return back()->with(
                'error',
                'Hanya peminjaman aktif yang dapat diperpanjang.'
            );
        }

        if ($borrowing->extension_status === 'menunggu') {
            return back()->with(
                'error',
                'Anda sudah mengajukan perpanjangan untuk peminjaman ini yang sedang menunggu persetujuan.'
            );
        }

        // Batas perpanjangan: max 14 hari dari jatuh tempo saat ini
        $maxExtensionDays = 14;

        $currentDue = \Carbon\Carbon::parse(
            $borrowing->due_at
        );

        $maxDate = $currentDue
            ->copy()
            ->addDays($maxExtensionDays);

        $validated = $request->validate([
            'new_due_date' => [
                'required',
                'date',
                'after:' . $currentDue->toDateString(),
                'before_or_equal:' . $maxDate->toDateString(),
            ],
            'reason' => 'nullable|string|max:500',
        ], [
            'new_due_date.required' =>
            'Tanggal pengembalian baru wajib dipilih.',

            'new_due_date.date' =>
            'Format tanggal tidak valid.',

            'new_due_date.after' =>
            'Tanggal harus lebih besar dari tanggal jatuh tempo saat ini.',

            'new_due_date.before_or_equal' =>
            "Perpanjangan melebihi batas maksimum {$maxExtensionDays} hari.",
        ]);

        $requestedDue = \Carbon\Carbon::parse(
            $validated['new_due_date']
        );

        $extensionDays = (int) $currentDue->diffInDays(
            $requestedDue
        );

        $borrowing->update([
            'extension_status' =>
            'menunggu',

            'extension_requested_due_at' =>
            $requestedDue->toDateString(),

            'extension_reason' =>
            $validated['reason']
                ?? "Perpanjangan peminjaman {$extensionDays} hari",
        ]);

        $borrowing->load('details.book');

        $bookTitle =
            $borrowing->details->first()?->book?->title
            ?? 'Buku';

        // Notifikasi ke Admin
        AppNotification::notifyAdmin(
            'extension_request',
            'Permintaan Perpanjangan Peminjaman',
            "{$user->name} mengajukan perpanjangan peminjaman buku \"{$bookTitle}\" hingga {$requestedDue->format('d M Y')}.",
            [
                'borrowing_id' => $borrowing->id
            ]
        );

        return back()->with(
            'success',
            'Permintaan perpanjangan berhasil diajukan dan sedang menunggu persetujuan Admin.'
        );
    }

    public function extend(
        Request $request,
        Borrowing $borrowing
    ) {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || $borrowing->member_id !== $member->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($borrowing->status !== 'dipinjam') {
            return response()->json([
                'message' => 'Buku tidak sedang dipinjam.'
            ], 422);
        }

        $currentDue = $borrowing->due_at
            ? \Carbon\Carbon::parse($borrowing->due_at)
            : null;

        if ($currentDue?->isPast()) {
            return response()->json([
                'message' =>
                'Buku tidak dapat diperpanjang karena sudah melewati tanggal jatuh tempo.'
            ], 422);
        }

        $bookIds = $borrowing
            ->details()
            ->pluck('book_id');

        $hasQueue = Reservation::whereIn(
            'book_id',
            $bookIds
        )
            ->where(
                'member_id',
                '!=',
                $member->id
            )
            ->whereIn('status', [
                'menunggu',
                'disetujui'
            ])
            ->exists();

        if ($hasQueue) {
            return response()->json([
                'message' =>
                'Buku tidak dapat diperpanjang karena sedang dalam antrean reservasi.'
            ], 409);
        }

        $maxExtensionDays = 14;

        $maxDate = $currentDue
            ->copy()
            ->addDays($maxExtensionDays);

        $validated = $request->validate([
            'new_due_date' => [
                'required',
                'date',
                'after:' . $currentDue->toDateString(),
                'before_or_equal:' . $maxDate->toDateString(),
            ],
        ], [
            'new_due_date.required' =>
            'Tanggal pengembalian baru wajib dipilih.',

            'new_due_date.date' =>
            'Format tanggal tidak valid.',

            'new_due_date.after' =>
            'Tanggal baru harus setelah tanggal jatuh tempo saat ini.',

            'new_due_date.before_or_equal' =>
            "Perpanjangan maksimal adalah {$maxExtensionDays} hari.",
        ]);

        $newDueDate = \Carbon\Carbon::parse(
            $validated['new_due_date']
        );

        $extensionDays = (int) $currentDue->diffInDays(
            $newDueDate
        );

        $borrowing->update([
            'due_at' => $newDueDate,
            'extension_status' => 'disetujui',
            'extension_reason' =>
            "Perpanjangan mandiri oleh pengguna (+{$extensionDays} hari)",
        ]);

        $borrowing->load('details.book');

        $bookTitle =
            $borrowing->details->first()?->book?->title
            ?? 'Buku';

        $user = Auth::user();

        if (
            $user &&
            method_exists($user, 'notificationsAllowed') &&
            $user->notificationsAllowed('extension')
        ) {
            AppNotification::notifyAdmin(
                'extension_approved',
                'Perpanjangan Peminjaman Mandiri',
                "{$user->name} memperpanjang peminjaman buku \"{$bookTitle}\" (+{$extensionDays} hari) hingga {$newDueDate->format('d M Y')}.",
                [
                    'borrowing_id' => $borrowing->id
                ]
            );
        }

        $activeBorrowings = Borrowing::where(
            'member_id',
            $member->id
        )
            ->where('status', 'dipinjam')
            ->get();

        NotificationService::extensionSelfApproved(
            $borrowing,
            $user
        );

        return response()->json([
            'message' =>
            'Peminjaman berhasil diperpanjang.',

            'due_at' =>
            $borrowing->due_at->format('d M Y'),

            'days_remaining' =>
            (int) now()->diffInDays(
                $borrowing->due_at,
                false
            ),

            'extension_days' =>
            $extensionDays,

            'near_due_count' =>
            $activeBorrowings->filter(
                fn($item) => ($item->due_at
                    ? now()->diffInDays(
                        $item->due_at,
                        false
                    )
                    : 99
                ) >= 0
                    &&
                    ($item->due_at
                        ? now()->diffInDays(
                            $item->due_at,
                            false
                        )
                        : 99
                    ) <= 3
            )->count(),

            'overdue_count' =>
            $activeBorrowings->filter(
                fn($item) => ($item->due_at
                    ? now()->diffInDays(
                        $item->due_at,
                        false
                    )
                    : 0
                ) < 0
            )->count(),
        ]);
    }
}
