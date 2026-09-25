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
        $members = Member::orderBy('name')->get();

        /*
         * Semua buku tetap ditampilkan,
         * termasuk yang stok tersedia = 0.
         */
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(judul_buku, ' ', -1) AS UNSIGNED)"
        )->get();

        $reservations = Reservation::with([
            'member',
            'book',
            'bookCopy',
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
            'member',
            'details.book',
            'details.bookCopy.shelf.zone.floor',
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
            'reservation_id' => [
                'nullable',
                'exists:reservations,id'
            ],

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

            $reservation = null;
            if (!empty($validated['reservation_id'])) {
                $reservation = Reservation::lockForUpdate()->find($validated['reservation_id']);
            }

            // Jika ada reservasi terkait, gunakan bookCopy yang sudah di-reserve
            $bookCopy = null;
            if ($reservation && $reservation->book_copy_id) {
                $bookCopy = BookCopy::lockForUpdate()->find($reservation->book_copy_id);
            }

            // Jika belum ada copy dari reservasi, cari satu copy available
            if (!$bookCopy) {
                $bookCopy = BookCopy::where('book_id', $book->id)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();
            }

            if (!$bookCopy) {
                abort(422, 'Eksemplar buku tidak tersedia.');
            }

            // Cek ketersediaan jika peminjaman baru (bukan dari reservasi)
            if (!$reservation && $book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            /*
             * Buat transaksi peminjaman.
             */
            $createdBorrowing = Borrowing::create([
                'user_id'        => $reservation?->user_id ?? $member->user_id,
                'member_id'      => $validated['member_id'],
                'reservation_id' => $reservation?->id,
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

            /*
             * Jika peminjaman biasa (bukan dari reservasi), kurangi stok buku.
             * Jika dari reservasi, stok sudah dikurangi saat reservasi dibuat.
             */
            if (!$reservation) {
                $book->decrement('stok');
            } else {
                $reservation->update([
                    'status'       => 'selesai',
                    'borrowing_id' => $createdBorrowing->id,
                ]);
            }

            /*
             * Sinkronisasi status member.
             */
            app(MemberStatusService::class)->sync($member);
        });

        if ($createdBorrowing) {
            NotificationService::borrowingApproved($createdBorrowing);
        }

        return redirect()
            ->route('circulation')
            ->with('success', 'Peminjaman berhasil diproses.');
    }


    /**
     * PENGEMBALIAN BUKU
     */
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        DB::transaction(function () use ($borrowing) {

            /*
             * Ambil detail peminjaman.
             */
            $borrowing->load('details');

            foreach ($borrowing->details as $detail) {

                /*
                 * Kalau book_copy_id tersedia,
                 * kembalikan copy fisik tersebut.
                 */
                if ($detail->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($detail->book_copy_id);

                    if ($bookCopy) {
                        $bookCopy->update(['status' => 'available']);
                    }
                } else {

                    /*
                     * Data lama belum memiliki book_copy_id.
                     * Tetap gunakan sistem stok lama.
                     */
                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);

                    $book->increment('stok', $detail->quantity);
                }

                /*
                 * Untuk peminjaman baru,
                 * stok lama juga harus dikembalikan.
                 */
                if ($detail->book_copy_id) {
                    $book = Book::lockForUpdate()
                        ->findOrFail($detail->book_id);

                    $book->increment('stok', $detail->quantity);
                }
            }

            /*
             * Update status peminjaman.
             */
            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'status'      => 'dikembalikan',
            ]);

            /*
             * Update status reservasi terkait menjadi selesai jika ada.
             */
            if ($borrowing->reservation_id) {
                Reservation::where('id', $borrowing->reservation_id)
                    ->whereNotIn('status', ['ditolak', 'dibatalkan'])
                    ->update(['status' => 'selesai']);
            }

            /*
             * Sinkronisasi status member.
             */
            app(MemberStatusService::class)->sync(
                Member::findOrFail($borrowing->member_id)
            );
        });

        return redirect()
            ->route('circulation')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    public function extend(Request $request, Borrowing $borrowing)
    {
        /*
     * Peminjaman yang sudah dikembalikan tidak dapat diperpanjang.
     */
        if ($borrowing->status === 'dikembalikan') {
            return back()->with(
                'error',
                'Peminjaman sudah selesai dan tidak dapat diperpanjang.'
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

        /*
     * Pastikan tanggal jatuh tempo tersedia.
     */
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
            ->route('circulation')
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
