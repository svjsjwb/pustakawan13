<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use App\Services\NotificationService;
use App\Services\MemberStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    /**
     * DAFTAR PEMINJAMAN / SIRKULASI BUKU
     */
    public function index(Request $request)
    {
        $members = Member::select(['id', 'member_number', 'name'])->orderBy('name')->get();

        $extensionRequests = Borrowing::with([
            'member:id,member_number,name',
            'detail:id,borrowing_id,book_id,book_copy_id,quantity',
            'detail.book:id,judul_buku,isbn',
            'detail.bookCopy',
        ])
            ->whereIn('status', ['dipinjam', 'diperpanjang'])
            ->where('extension_status', 'menunggu')
            ->latest()
            ->get();

        /*
         * Semua buku tetap ditampilkan,
         * termasuk yang stok tersedia = 0.
         */
        $cacheFile = storage_path('framework/cache/books_dropdown.json');
        $orderRaw = "
    COALESCE(
        NULLIF(
            substring(judul_buku from '([0-9]+)$'),
            ''
        )::BIGINT,
        0
    ) ASC
";

        if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > 600)) {
            $booksData = Book::select(['id', 'judul_buku', 'stok'])
                ->orderByRaw($orderRaw)
                ->get()
                ->map(function ($b) {
                    return (object) [
                        'id' => $b->id,
                        'title' => $b->title,
                        'available_stock' => $b->available_stock,
                    ];
                });
            @file_put_contents($cacheFile, json_encode($booksData));
            $books = $booksData;
        } else {
            $books = json_decode(file_get_contents($cacheFile));
        }

        $reservations = Reservation::with([
            'member:id,member_number,name',
            'book:id,judul_buku,isbn',
        ])
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NAVIGASI BULAN (dari Pandu — fitur baru)
        |--------------------------------------------------------------------------
        */

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $currentPeriod = Carbon::createFromDate($year, $month, 1)->locale('id');
        $monthLabel    = $currentPeriod->translatedFormat('F Y');

        $prevPeriod = $currentPeriod->copy()->subMonth();
        $prevMonth  = $prevPeriod->month;
        $prevYear   = $prevPeriod->year;

        $nextPeriod = $currentPeriod->copy()->addMonth();
        $nextMonth  = $nextPeriod->month;
        $nextYear   = $nextPeriod->year;

        $isCurrentMonth = $month === now()->month && $year === now()->year;

        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        $borrowings = Borrowing::with([
            'member:id,member_number,name',
            'detail:id,borrowing_id,book_id,book_copy_id,quantity',
            'detail.book:id,judul_buku,isbn',
            'detail.bookCopy',
        ])
            ->whereYear('borrowed_at', $year)
            ->whereMonth('borrowed_at', $month)
            ->latest()
            ->get();

        return view('circulation.index', compact(
            'members',
            'books',
            'reservations',
            'borrowings',
            'extensionRequests',
            'monthLabel',
            'month',
            'year',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'isCurrentMonth'
        ));
    }


    /**
     * PEMINJAMAN BUKU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => ['prohibited'],

            'member_id' => [
                'required',
                'exists:members,id'
            ],

            'book_id' => [
                'required',
                'exists:books,id'
            ],

            'borrowed_at' => [
                'required',
                'date'
            ],

            'due_at' => [
                'required',
                'date',
                'after_or_equal:borrowed_at'
            ],
        ]);


        $createdBorrowing = null;

        DB::transaction(function () use ($validated, &$createdBorrowing) {
            $book = Book::lockForUpdate()->findOrFail($validated['book_id']);
            $member = Member::findOrFail($validated['member_id']);

            $bookCopy = BookCopy::where('book_id', $book->id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (!$bookCopy || $book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            /*
             * Buat transaksi peminjaman.
             */
            $createdBorrowing = Borrowing::create([
                'user_id' => $member->user_id,
                'member_id'      => $validated['member_id'],
                'book_id'        => $book->id,
                'borrowed_at'    => $validated['borrowed_at'],
                'due_at'         => $validated['due_at'],
                'status'         => 'dipinjam',
            ]);

            /*
             * Simpan BookCopy yang benar-benar dipinjam.
             */
            BorrowingDetail::create([
                'borrowing_id' => $createdBorrowing->id,
                'book_id'      => $book->id,
                'book_copy_id' => $bookCopy->id,
                'quantity'     => 1,
            ]);

            /*
             * Ubah status fisik buku menjadi borrowed.
             */
            $bookCopy->update(['status' => 'borrowed']);

            $book->decrement('stok');

            /*
             * Sinkronisasi status member.
             */
            app(MemberStatusService::class)->sync($member);
        });

        if ($createdBorrowing) {
            NotificationService::borrowingApproved($createdBorrowing);
        }

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil diproses.');
    }



    public function returnBook(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {

            // Ambil ulang transaksi dan kunci agar tidak diproses bersamaan.
            $lockedBorrowing = Borrowing::whereKey($borrowing->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBorrowing->status === 'dikembalikan') {
                abort(422, 'Peminjaman ini sudah dikembalikan.');
            }

            if (in_array($lockedBorrowing->status, ['dibatalkan', 'ditolak'], true)) {
                abort(422, 'Transaksi ini sudah dibatalkan atau tidak aktif.');
            }

            // Ambil satu detail sesuai relasi HasOne.
            $detail = $lockedBorrowing->detail()
                ->lockForUpdate()
                ->first();

            if (!$detail || !$detail->book_copy_id) {
                abort(422, 'Detail atau eksemplar fisik peminjaman tidak ditemukan.');
            }

            // Kunci eksemplar fisik yang benar-benar dipinjam.
            $bookCopy = BookCopy::whereKey($detail->book_copy_id)
                ->lockForUpdate()
                ->first();

            if (!$bookCopy || $bookCopy->status !== 'borrowed') {
                abort(422, 'Status eksemplar tidak sesuai dengan transaksi peminjaman.');
            }

            // Tentukan status berdasarkan kondisi fisik buku.
            $bookCopy->update([
                'status' => 'available',
            ]);

            $bookCopy->syncStatus();

            // Stok hanya bertambah jika eksemplar benar-benar tersedia.
            if ($bookCopy->status === 'available') {
                $book = Book::whereKey($detail->book_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $book->increment('stok', $detail->quantity ?? 1);
            }

            // Tandai peminjaman sudah dikembalikan.
            $lockedBorrowing->update([
                'returned_at' => now()->toDateString(),
                'status' => 'dikembalikan',
            ]);

            // Sinkronisasi status anggota.
            app(MemberStatusService::class)->sync(
                Member::findOrFail($lockedBorrowing->member_id)
            );
        });

        $borrowing->refresh();

        NotificationService::borrowingReturned($borrowing);

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }


    public function extend(Request $request, Borrowing $borrowing)
    {

        /*
 * Hanya peminjaman aktif yang dapat diperpanjang.
 */
        if (!in_array($borrowing->status, ['dipinjam', 'diperpanjang'], true)) {
            return back()->with(
                'error',
                'Peminjaman sudah tidak aktif dan tidak dapat diperpanjang.'
            );
        }

        /*
 * Cegah pengajuan perpanjangan ganda.
 */
        if ($borrowing->extension_status === 'menunggu') {
            return back()->with(
                'error',
                'Permintaan perpanjangan masih menunggu persetujuan Admin.'
            );
        }


        /*
     * Validasi jumlah hari perpanjangan.
     */
        $validated = $request->validate([
            'extension_days' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],
        ]);


        if (!$borrowing->due_at) {
            return back()->with(
                'error',
                'Tanggal pengembalian belum tersedia.'
            );
        }

        /*
     * Pastikan extension_days menjadi integer.
     */
        $extensionDays = (int) $validated['extension_days'];

        /*
     * Ambil tanggal jatuh tempo lama.
     */
        $currentDueDate = Carbon::parse(
            $borrowing->due_at
        )->startOfDay();

        /*
 * Tanggal saat perpanjangan dilakukan.
 */
        $extensionDate = now()->startOfDay();

        if ($extensionDate->gt($currentDueDate)) {

            $newDueDate = $extensionDate
                ->copy()
                ->addDays($extensionDays);
        } else {

            $newDueDate = $currentDueDate
                ->copy()
                ->addDays($extensionDays);
        }

        $extensionHistory = $borrowing->extension_history ?? [];

        /*
     * Tambahkan riwayat perpanjangan baru.
     */
        $extensionHistory[] = [
            'extension_date' => $extensionDate->toDateString(),
            'old_due_at' => $currentDueDate->toDateString(),
            'new_due_at' => $newDueDate->toDateString(),
            'extension_days' => $extensionDays,
        ];

        /*
     * Simpan perpanjangan.
     */
        $borrowing->update([
            'due_at' => $newDueDate,

            'status' => 'diperpanjang',

            'extension_status' => 'disetujui',

            'extension_reason' =>
            "Perpanjangan oleh Admin (+{$extensionDays} hari)",

            'extension_admin_notes' =>
            'Perpanjangan disetujui oleh Admin.',

            'extension_history' => $extensionHistory,
        ]);

        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                "Peminjaman berhasil diperpanjang {$extensionDays} hari."
            );
    }

    /**
     * Setujui Permintaan Perpanjangan
     */
    public function approveExtension(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['dipinjam', 'diperpanjang'], true)) {
            return back()->with(
                'error',
                'Peminjaman sudah tidak aktif. Perpanjangan tidak dapat disetujui.'
            );
        }

        if ($borrowing->extension_status !== 'menunggu') {
            return back()->with(
                'error',
                'Tidak ada permintaan perpanjangan yang menunggu persetujuan.'
            );
        }

        /*
     * Ambil tanggal jatuh tempo sebelum diperpanjang.
     */
        if (!$borrowing->due_at) {
            return back()->with(
                'error',
                'Tanggal pengembalian belum tersedia.'
            );
        }

        $currentDueDate = Carbon::parse(
            $borrowing->due_at
        )->startOfDay();

        $extensionDate = now()->startOfDay();

        if ($borrowing->extension_requested_due_at) {

            $requestedDueDate = Carbon::parse(
                $borrowing->extension_requested_due_at
            )->startOfDay();

            $extensionDays = $currentDueDate->diffInDays(
                $requestedDueDate
            );

            $extensionDays = max(
                1,
                $extensionDays
            );
        } else {

            $extensionDays = 7;
        }

        if ($extensionDate->gt($currentDueDate)) {

            $newDue = $extensionDate
                ->copy()
                ->addDays($extensionDays);
        } else {

            $newDue = $currentDueDate
                ->copy()
                ->addDays($extensionDays);
        }

        /*
     * Ambil history sebelumnya.
     */
        $extensionHistory = $borrowing->extension_history ?? [];

        /*
     * Tambahkan history perpanjangan baru.
     */
        $extensionHistory[] = [
            'extension_date' => $extensionDate->toDateString(),
            'old_due_at' => $currentDueDate->toDateString(),
            'new_due_at' => $newDue->toDateString(),
            'extension_days' => $extensionDays,
        ];

        /*
     * Simpan hasil approval.
     */
        $borrowing->update([
            'due_at' => $newDue,

            'status' => 'diperpanjang',

            'extension_status' => 'disetujui',

            'extension_admin_notes' => 'Disetujui oleh Admin',

            'extension_history' => $extensionHistory,
        ]);

        NotificationService::extensionApproved($borrowing);

        return back()->with(
            'success',
            'Perpanjangan peminjaman berhasil disetujui.'
        );
    }

    /**
     * Tolak Permintaan Perpanjangan
     */
    public function rejectExtension(Request $request, Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['dipinjam', 'diperpanjang'], true)) {
            return back()->with(
                'error',
                'Peminjaman sudah tidak aktif. Permintaan perpanjangan tidak dapat diproses.'
            );
        }

        if ($borrowing->extension_status !== 'menunggu') {
            return back()->with('error', 'Tidak ada permintaan perpanjangan yang menunggu.');
        }

        $notes = $request->input('admin_notes', 'Ditolak oleh Admin');

        $borrowing->update([
            'extension_status'      => 'ditolak',
            'extension_admin_notes' => $notes,
        ]);

        NotificationService::extensionRejected($borrowing, null, $notes);

        return back()->with('success', 'Perpanjangan peminjaman telah ditolak.');
    }
}
