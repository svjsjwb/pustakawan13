<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\CollectionWithdrawal;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * ============================================================
     * DATA LAPORAN DEFAULT
     * ============================================================
     */
    private function defaultReports()
    {
        return [
            1 => [
                'id' => 1,
                'jenis' => 'Laporan Peminjaman Bulanan',
                'kategori' => 'Semua Buku',
                'status' => 'Semua Status',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => now()->startOfMonth()->format('Y-m-d'),
                'tanggal_selesai' => now()->endOfMonth()->format('Y-m-d'),
            ],

            2 => [
                'id' => 2,
                'jenis' => 'Laporan Keterlambatan',
                'kategori' => 'Semua Buku',
                'status' => 'Terlambat',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => now()->startOfMonth()->format('Y-m-d'),
                'tanggal_selesai' => now()->endOfMonth()->format('Y-m-d'),
            ],

            3 => [
                'id' => 3,
                'jenis' => 'Laporan Koleksi Buku',
                'kategori' => 'Semua Buku',
                'status' => 'Semua Kondisi',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => now()->startOfMonth()->format('Y-m-d'),
                'tanggal_selesai' => now()->endOfMonth()->format('Y-m-d'),
            ],

            4 => [
                'id' => 4,
                'jenis' => 'Laporan Anggota Aktif',
                'kategori' => 'Semua Buku',
                'status' => 'Aktif',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => now()->startOfMonth()->format('Y-m-d'),
                'tanggal_selesai' => now()->endOfMonth()->format('Y-m-d'),
            ],
        ];
    }


    /**
     * ============================================================
     * AMBIL DATA REPORT DARI SESSION
     * ============================================================
     */
    private function getReports(Request $request)
    {
        if (!$request->session()->has('reports')) {
            $request->session()->put(
                'reports',
                $this->defaultReports()
            );
        }

        return $request->session()->get(
            'reports',
            []
        );
    }


    /**
     * ============================================================
     * TENTUKAN PERIODE
     * ============================================================
     */
    private function getPeriod(Request $request)
    {
        $startDateInput = $request->query('start_date');
        $endDateInput = $request->query('end_date');

        if ($startDateInput && $endDateInput) {

            try {

                $startDate = Carbon::createFromFormat(
                    'Y-m-d',
                    $startDateInput
                )->startOfDay();

                $endDate = Carbon::createFromFormat(
                    'Y-m-d',
                    $endDateInput
                )->endOfDay();


                if ($startDate->gt($endDate)) {

                    $temp = $startDate;

                    $startDate =
                        $endDate
                        ->copy()
                        ->startOfDay();

                    $endDate =
                        $temp
                        ->copy()
                        ->endOfDay();
                }


                $diffDays =
                    $startDate->diffInDays($endDate) + 1;


                $label =
                    $startDate->translatedFormat('d M Y')
                    . ' - '
                    . $endDate->translatedFormat('d M Y');


                if ($diffDays <= 1) {

                    $rangeType = 'day';
                } elseif ($diffDays <= 7) {

                    $rangeType = 'week';
                } else {

                    $rangeType = 'month';
                }


                return [
                    'type' => $rangeType,
                    'label' => $label,
                    'start' => $startDate,
                    'end' => $endDate,
                    'selectedDate' => $startDate->format('Y-m-d'),
                    'startDateInput' => $startDate->format('Y-m-d'),
                    'endDateInput' => $endDate->format('Y-m-d'),
                ];
            } catch (\Exception $e) {
                // Gunakan default.
            }
        }


        $startDate =
            now()
            ->startOfMonth()
            ->startOfDay();


        $endDate =
            now()
            ->endOfMonth()
            ->endOfDay();


        $label =
            $startDate->translatedFormat('d M Y')
            . ' - '
            . $endDate->translatedFormat('d M Y');


        return [
            'type' => 'month',
            'label' => $label,
            'start' => $startDate,
            'end' => $endDate,
            'selectedDate' => now()->format('Y-m-d'),
            'startDateInput' => $startDate->format('Y-m-d'),
            'endDateInput' => $endDate->format('Y-m-d'),
        ];
    }


    /**
     * ============================================================
     * HALAMAN UTAMA LAPORAN
     * ============================================================
     */
    public function index(Request $request)
    {
        $reports =
            $this->getReports($request);


        /*
         * ========================================================
         * PERIODE
         * ========================================================
         */
        $period =
            $this->getPeriod($request);


        $startDate =
            $period['start'];

        $endDate =
            $period['end'];

        $periodLabel =
            $period['label'];

        $periodType =
            $period['type'];

        $selectedDate =
            $period['selectedDate'];

        $startDateInput =
            $period['startDateInput'];

        $endDateInput =
            $period['endDateInput'];


        /*
         * ========================================================
         * BADGE PERIODE
         * ========================================================
         */
        $reportMonthStart =
            now()->startOfMonth();

        $reportMonthEnd =
            now()->endOfMonth();

        $reportMonthLabel =
            $reportMonthStart->translatedFormat('d M Y')
            . ' - '
            . $reportMonthEnd->translatedFormat('d M Y');


        /*
         * ========================================================
         * PEMINJAMAN
         * ========================================================
         */
        $borrowings =
            Borrowing::with([
                'member',
                'details.book'
            ])
            ->whereBetween(
                'borrowed_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->latest('borrowed_at')
            ->get();


        $borrowedBooks =
            $borrowings->count();


        $totalBorrowed =
            $borrowings->count();


        $totalReturned =
            $borrowings
            ->where(
                'status',
                'dikembalikan'
            )
            ->count();


        $totalActiveBorrow =
            $borrowings
            ->where(
                'status',
                'dipinjam'
            )
            ->count();


        /**
         * ========================================================
         * TOTAL KETERLAMBATAN
         * ========================================================
         */
        $historicalLateBorrowings =
            $this->getHistoricalLateBorrowings(
                $startDate,
                $endDate
            );

        $totalLate =
            $historicalLateBorrowings->count();


        [
            $borrowChartLabels,
            $borrowChartBars
        ] =
            $this->generateBorrowChart(
                $startDate,
                $endDate,
                $periodType
            );


        /**
         * ========================================================
         * KETERLAMBATAN
         * ========================================================
         */
        $lateBorrowings =
            $historicalLateBorrowings->count();


        [
            $lateChartLabels,
            $lateChartBars
        ] =
            $this->generateLateChart(
                $startDate,
                $endDate,
                $periodType
            );


        /*
         * ========================================================
         * KOLEKSI
         * ========================================================
         *
         * 1. Buku Ditambahkan
         * 2. Buku Ditarik
         * 3. Eksemplar Dihapus
         * 4. Kondisi Rusak
         */
        $collectionReportData =
            $this->getCollectionReportData(
                $startDate,
                $endDate
            );


        /*
         * ========================================================
         * RINGKASAN KOLEKSI
         * ========================================================
         */
        $collectionAddedCount =
            $collectionReportData
            ->where(
                'type',
                'added'
            )
            ->count();


        $collectionWithdrawnCount =
            $collectionReportData
            ->where(
                'type',
                'withdrawn'
            )
            ->sum('quantity');


        $collectionDeletedCopyCount =
            $collectionReportData
            ->where(
                'type',
                'copy_deleted'
            )
            ->count();


        $collectionDamagedCount =
            $collectionReportData
            ->where(
                'type',
                'damaged'
            )
            ->count();


        $totalCollectionChanges =
            $collectionReportData->count();


        /*
         * ========================================================
         * GRAFIK KOLEKSI
         * ========================================================
         */
        [
            $collectionChartLabels,
            $collectionChartBars
        ] =
            $this->generateCollectionChart(
                $startDate,
                $endDate,
                $periodType
            );


        /*
         * ========================================================
         * ANGGOTA AKTIF
         * ========================================================
         */
        $activeMemberIds =
            $this->getActiveMemberIds(
                $startDate,
                $endDate
            );


        $activeMembers =
            count($activeMemberIds);


        [
            $memberChartLabels,
            $memberChartBars
        ] =
            $this->generateMemberChart(
                $startDate,
                $endDate,
                $periodType
            );


        /*
         * ========================================================
         * DATA EXPORT PEMINJAMAN
         * ========================================================
         */
        $borrowingsExportData =
            $borrowings
            ->map(function ($item, $index) {

                $bookTitles =
                    $item->details
                    ->map(function ($d) {

                        return ($d->book->judul_buku ?? 'Buku')
                            .
                            (
                                $d->quantity > 1
                                ? ' (' . $d->quantity . 'x)'
                                : ''
                            );
                    })
                    ->implode(', ');


                $lateInfo =
                    $this->getLateInfo($item);

                $isLate =
                    $lateInfo['is_late'];


                $statusText =
                    $item->status === 'dikembalikan'
                    ? 'Dikembalikan'
                    : (
                        $isLate
                        ? 'Terlambat'
                        : (
                            $item->status === 'diperpanjang'
                            ? 'Diperpanjang'
                            : 'Dipinjam'
                        )
                    );


                return [
                    'no' =>
                    $index + 1,

                    'member_name' =>
                    $item->member->name
                        ??
                        ('Anggota #' . $item->member_id),

                    'member_code' =>
                    $item->member->member_code
                        ??
                        '-',

                    'judul_buku' =>
                    $bookTitles
                        ?:
                        'Tidak ada rincian',

                    'borrowed_at' =>
                    Carbon::parse(
                        $item->borrowed_at
                    )->translatedFormat(
                        'd M Y'
                    ),

                    'due_at' =>
                    $item->due_at
                        ? Carbon::parse(
                            $item->due_at
                        )->translatedFormat(
                            'd M Y'
                        )
                        : '-',

                    'returned_at' =>
                    $item->returned_at
                        ? Carbon::parse(
                            $item->returned_at
                        )->translatedFormat(
                            'd M Y'
                        )
                        : '-',

                    'status' =>
                    $statusText,
                ];
            })
            ->values()
            ->toArray();


        /*
 * ========================================================
 * DATA EXPORT KETERLAMBATAN
 * ========================================================
 *
 * Menampilkan semua peminjaman yang secara historis
 * pernah terlambat.
 *
 * Termasuk:
 * - masih dipinjam
 * - sudah dikembalikan
 * - pernah diperpanjang
 * - diperpanjang berkali-kali
 */
        $lateBorrowingsExportData =
            $historicalLateBorrowings
            ->map(function ($item, $index) {

                $bookTitles =
                    $item->details
                    ->map(function ($d) {

                        return ($d->book->judul_buku ?? 'Buku')
                            .
                            (
                                $d->quantity > 1
                                ? ' (' . $d->quantity . 'x)'
                                : ''
                            );
                    })
                    ->implode(', ');

                $lateInfo =
                    $this->getLateInfo($item);

                $extensionHistory =
                    collect(
                        $lateInfo['extension_history']
                    );

                /*
         * ====================================================
         * TENGGAT AWAL
         * ====================================================
         */
                if ($extensionHistory->isNotEmpty()) {

                    $originalDueDate =
                        Carbon::parse(
                            $extensionHistory->first()['old_due_at']
                        );
                } else {

                    $originalDueDate =
                        Carbon::parse(
                            $item->due_at
                        );
                }

                /*
         * ====================================================
         * TANGGAL PERPANJANGAN
         * ====================================================
         */
                $extensionDates =
                    $extensionHistory
                    ->map(function ($extension) {

                        return Carbon::parse(
                            $extension['extension_date']
                                ?? $extension['new_due_at']
                        )->translatedFormat('d M Y');
                    })
                    ->implode('<br>');

                /*
         * ====================================================
         * TENGGAT BARU
         * ====================================================
         */
                $newDueDates =
                    $extensionHistory
                    ->map(function ($extension) {

                        return Carbon::parse(
                            $extension['new_due_at']
                        )->translatedFormat('d M Y');
                    })
                    ->implode('<br>');

                /*
         * ====================================================
         * STATUS
         * ====================================================
         *
         * Kalau pernah diperpanjang,
         * histori status tetap Diperpanjang.
         */
                if ($extensionHistory->isNotEmpty()) {

                    $statusText =
                        'Diperpanjang';
                } else {

                    $statusText =
                        $item->status === 'dikembalikan'
                        ? 'Dikembalikan'
                        : 'Terlambat';
                }

                /*
         * ====================================================
         * KETERANGAN
         * ====================================================
         */
                $keterangan =
                    'Terlambat '
                    . $lateInfo['late_days']
                    . ' hari';

                return [

                    'no' =>
                    $index + 1,

                    'member_name' =>
                    $item->member->name
                        ??
                        ('Anggota #' . $item->member_id),

                    'member_code' =>
                    $item->member->member_code
                        ??
                        '-',

                    'judul_buku' =>
                    $bookTitles
                        ?:
                        'Tidak ada rincian',

                    'borrowed_at' =>
                    Carbon::parse(
                        $item->borrowed_at
                    )->translatedFormat(
                        'd M Y'
                    ),

                    /*
             * Tenggat Pengembalian = deadline awal.
             */
                    'due_at' =>
                    $originalDueDate
                        ->translatedFormat(
                            'd M Y'
                        ),

                    /*
             * Status historis.
             */
                    'status' =>
                    $statusText,

                    /*
             * Bisa berisi beberapa tanggal.
             */
                    'extension_date' =>
                    $extensionDates
                        ?:
                        '-',

                    /*
             * Bisa berisi beberapa deadline baru.
             */
                    'new_due_at' =>
                    $newDueDates
                        ?:
                        '-',

                    'returned_at' =>
                    $item->returned_at
                        ? Carbon::parse(
                            $item->returned_at
                        )->translatedFormat(
                            'd M Y'
                        )
                        : '-',

                    'keterangan' =>
                    $keterangan,
                ];
            })
            ->values()
            ->toArray();


        /*
         * ========================================================
         * DATA EXPORT ANGGOTA AKTIF
         * ========================================================
         *
         * Angka anggota aktif dihitung dari gabungan peminjaman
         * dan reservasi. PDF menggunakan sumber data yang sama.
         * Satu anggota ditampilkan satu kali.
         */
        $activeMembersExportData =
            Member::query()
            ->whereIn(
                'id',
                $activeMemberIds
            )
            ->get()
            ->map(function ($member, $index) use (
                $startDate,
                $endDate
            ) {

                $borrowing =
                    Borrowing::with([
                        'details.book'
                    ])
                    ->where(
                        'member_id',
                        $member->id
                    )
                    ->whereDate(
                        'borrowed_at',
                        '<=',
                        $endDate
                    )
                    ->where(function ($query) use (
                        $startDate
                    ) {
                        $query
                            ->whereNull('returned_at')
                            ->orWhereDate(
                                'returned_at',
                                '>=',
                                $startDate
                            );
                    })
                    ->latest('borrowed_at')
                    ->first();

                $reservation =
                    Reservation::with([
                        'book'
                    ])
                    ->where(
                        'member_id',
                        $member->id
                    )
                    ->whereNotIn(
                        'status',
                        [
                            'ditolak',
                            'dibatalkan',
                        ]
                    )
                    ->whereDate(
                        'reserved_at',
                        '<=',
                        $endDate
                    )
                    ->where(function ($query) use (
                        $startDate
                    ) {
                        $query
                            ->whereNull('expires_at')
                            ->orWhereDate(
                                'expires_at',
                                '>=',
                                $startDate
                            );
                    })
                    ->latest('reserved_at')
                    ->first();

                $borrowingDate =
                    $borrowing
                    ? Carbon::parse(
                        $borrowing->borrowed_at
                    )
                    : null;

                $reservationDate =
                    $reservation
                    ? Carbon::parse(
                        $reservation->reserved_at
                    )
                    : null;

                if (
                    $borrowing &&
                    (
                        !$reservationDate ||
                        $borrowingDate->gte(
                            $reservationDate
                        )
                    )
                ) {
                    $bookTitles =
                        $borrowing->details
                        ->map(function ($detail) {
                            return ($detail->book->judul_buku ?? 'Buku')
                                .
                                (
                                    $detail->quantity > 1
                                    ? ' (' . $detail->quantity . 'x)'
                                    : ''
                                );
                        })
                        ->implode(', ');

                    $isLate =
                        $borrowing->status === 'dipinjam'
                        &&
                        $borrowing->due_at
                        &&
                        Carbon::parse(
                            $borrowing->due_at
                        )->startOfDay()->addDay()->lte(now());

                    $statusText =
                        $borrowing->status === 'dikembalikan'
                        ? 'Dikembalikan'
                        : (
                            $isLate
                            ? 'Terlambat'
                            : 'Dipinjam'
                        );

                    return [
                        'no' => $index + 1,
                        'member_name' =>
                        $member->name
                            ??
                            ('Anggota #' . $member->id),
                        'member_code' =>
                        $member->member_code ?? '-',
                        'activity' => 'Peminjaman',
                        'judul_buku' =>
                        $bookTitles ?: 'Tidak ada rincian',
                        'date' =>
                        $borrowingDate->translatedFormat('d M Y'),
                        'status' => $statusText,
                    ];
                }

                if ($reservation) {
                    return [
                        'no' => $index + 1,
                        'member_name' =>
                        $member->name
                            ??
                            ('Anggota #' . $member->id),
                        'member_code' =>
                        $member->member_code ?? '-',
                        'activity' => 'Reservasi',
                        'judul_buku' =>
                        $reservation->book->judul_buku ?? 'Buku',
                        'date' =>
                        $reservationDate->translatedFormat('d M Y'),
                        'status' =>
                        ucfirst($reservation->status),
                    ];
                }

                return [
                    'no' => $index + 1,
                    'member_name' =>
                    $member->name
                        ??
                        ('Anggota #' . $member->id),
                    'member_code' =>
                    $member->member_code ?? '-',
                    'activity' => '-',
                    'judul_buku' => '-',
                    'date' => '-',
                    'status' => 'Aktif',
                ];
            })
            ->values()
            ->toArray();


        /*
         * ========================================================
         * DATA EXPORT KOLEKSI
         * ========================================================
         */
        $collectionExportData =
            $collectionReportData
            ->map(function ($item, $index) {

                return [
                    'no' =>
                    $index + 1,

                    'tanggal' =>
                    $item['date']
                        ->translatedFormat(
                            'd M Y'
                        ),

                    'jenis_perubahan' =>
                    $item['label'],

                    'judul_buku' =>
                    $item['judul_buku'],

                    'eksemplar_jumlah' =>
                    $item['display_quantity'],

                    'alasan_keterangan' =>
                    $item['reason'],

                ];
            })
            ->values()
            ->toArray();


        /*
         * ========================================================
         * KIRIM KE VIEW
         * ========================================================
         */
        return view(
            'reports.index',
            compact(
                'reports',

                'borrowings',
                'borrowingsExportData',
                'lateBorrowingsExportData',
                'activeMembersExportData',

                'collectionReportData',
                'collectionExportData',

                'collectionAddedCount',
                'collectionWithdrawnCount',
                'collectionDeletedCopyCount',
                'collectionDamagedCount',
                'totalCollectionChanges',

                'totalBorrowed',
                'totalReturned',
                'totalActiveBorrow',
                'totalLate',

                'borrowedBooks',
                'activeMembers',
                'lateBorrowings',

                'periodLabel',
                'periodType',
                'selectedDate',
                'startDateInput',
                'endDateInput',
                'startDate',
                'endDate',
                'reportMonthLabel',

                'borrowChartLabels',
                'borrowChartBars',

                'lateChartLabels',
                'lateChartBars',

                'collectionChartLabels',
                'collectionChartBars',

                'memberChartLabels',
                'memberChartBars'
            )
        );
    }


    /**
     * ============================================================
     * DATA LAPORAN KOLEKSI
     * ============================================================
     */
    private function getCollectionReportData(
        $startDate,
        $endDate
    ) {
        $data = collect();


        /*
         * ========================================================
         * 1. BUKU DITAMBAHKAN
         * ========================================================
         *
         * Hanya buku aktif yang dibuat dalam periode.
         */
        $addedBooks =
            Book::with('copies')
            ->whereBetween(
                'created_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->orderBy(
                'created_at',
                'desc'
            )
            ->get();


        foreach ($addedBooks as $book) {

            $copyCount =
                $book->copies->count();


            $data->push([
                'type' => 'added',

                'label' =>
                'Buku Ditambahkan',

                'date' =>
                $book->created_at,

                'judul_buku' =>
                $book->judul_buku,

                'barcode' =>
                null,

                'quantity' =>
                $copyCount > 0
                    ? $copyCount
                    : 1,

                'display_quantity' =>
                $copyCount > 0
                    ? $copyCount . ' eksemplar'
                    : '1 buku',

                'reason' =>
                'Koleksi baru',

                'sort_date' =>
                $book->created_at,
            ]);
        }


        /*
         * ========================================================
         * 2. BUKU DITARIK
         * ========================================================
         */
        $withdrawnBooks =
            CollectionWithdrawal::query()
            ->where(
                'type',
                'book'
            )
            ->whereBetween(
                'withdrawn_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->orderBy(
                'withdrawn_at',
                'desc'
            )
            ->get();


        foreach (
            $withdrawnBooks
            as $withdrawal
        ) {

            $data->push([
                'type' =>
                'withdrawn',

                'label' =>
                'Buku Ditarik',

                'date' =>
                $withdrawal->withdrawn_at,

                'judul_buku' =>
                $withdrawal->book_title,

                'barcode' =>
                null,

                'quantity' =>
                $withdrawal->quantity,

                'display_quantity' =>
                $withdrawal->quantity
                    . ' eksemplar',

                'reason' =>
                $withdrawal->reason,

                'sort_date' =>
                $withdrawal->withdrawn_at,
            ]);
        }


        /*
         * ========================================================
         * 3. EKSEMPLAR DIHAPUS
         * ========================================================
         */
        $deletedCopies =
            CollectionWithdrawal::query()
            ->where(
                'type',
                'copy'
            )
            ->whereBetween(
                'withdrawn_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->orderBy(
                'withdrawn_at',
                'desc'
            )
            ->get();


        foreach (
            $deletedCopies
            as $withdrawal
        ) {

            $data->push([
                'type' =>
                'copy_deleted',

                'label' =>
                'Eksemplar Dihapus',

                'date' =>
                $withdrawal->withdrawn_at,

                'judul_buku' =>
                $withdrawal->book_title,

                'barcode' =>
                $withdrawal->barcode,

                'quantity' =>
                1,

                'display_quantity' =>
                $withdrawal->barcode
                    ?? '1 eksemplar',

                'reason' =>
                $withdrawal->reason,

                'sort_date' =>
                $withdrawal->withdrawn_at,
            ]);
        }


        /*
         * ========================================================
         * 4. KONDISI RUSAK
         * ========================================================
         *
         * Saat ini database hanya menyimpan kondisi terakhir.
         *
         * Jadi bagian ini menampilkan eksemplar yang sekarang
         * mempunyai condition = rusak.
         *
         * Bukan histori perubahan kondisi.
         */
        $damagedCopies =
            BookCopy::with([
                'book'
            ])
            ->where(
                'condition',
                'rusak'
            )
            ->orderBy(
                'updated_at',
                'desc'
            )
            ->get();


        foreach (
            $damagedCopies
            as $copy
        ) {

            $data->push([
                'type' =>
                'damaged',

                'label' =>
                'Kondisi Rusak',

                'date' =>
                $copy->updated_at
                    ?? $copy->created_at,

                'book_title' =>
                $copy->book->judul_buku
                    ?? 'Buku',

                'barcode' =>
                $copy->barcode,

                'quantity' =>
                1,

                'display_quantity' =>
                $copy->barcode
                    ?? '1 eksemplar',

                'reason' =>
                'Kondisi fisik rusak',

                'sort_date' =>
                $copy->updated_at
                    ?? $copy->created_at,
            ]);
        }


        /*
         * ========================================================
         * URUTKAN TERBARU
         * ========================================================
         */
        return $data
            ->sortByDesc(
                'sort_date'
            )
            ->values();
    }


    /**
     * ============================================================
     * HITUNG RIWAYAT KETERLAMBATAN PEMINJAMAN
     * ============================================================
     *
     * Menghasilkan:
     *
     * - apakah pernah terlambat
     * - tanggal mulai terlambat
     * - tanggal akhir keterlambatan
     * - total hari terlambat
     * - riwayat perpanjangan
     *
     * Aturan:
     *
     * Peminjaman jatuh tempo tanggal 15
     * -> belum terlambat sepanjang tanggal 15
     * -> mulai terlambat 00:00 tanggal 16
     *
     * Jika diperpanjang:
     *
     * 15 Sep -> 20 Sep
     *
     * dan perpanjangan dilakukan 16 Sep,
     * maka tanggal 16 dihitung sebagai 1 hari terlambat
     * sebelum deadline baru berlaku.
     */
    private function getLateInfo(Borrowing $borrowing)
    {
        if (!$borrowing->due_at) {
            return [
                'is_late' => false,
                'late_start' => null,
                'late_end' => null,
                'late_days' => 0,
                'extension_history' => [],
            ];
        }

        /*
     * Ambil hanya history yang memiliki
     * deadline lama dan deadline baru.
     */
        $history = collect(
            $borrowing->extension_history ?? []
        )
            ->filter(function ($extension) {
                return !empty($extension['old_due_at'])
                    && !empty($extension['new_due_at']);
            })
            ->sortBy(function ($extension) {
                return $extension['extension_date']
                    ?? $extension['new_due_at'];
            })
            ->values();

        /*
     * ========================================================
     * DEADLINE AWAL
     * ========================================================
     */
        if ($history->isNotEmpty()) {

            $originalDueDate = Carbon::parse(
                $history->first()['old_due_at']
            )->startOfDay();
        } else {

            $originalDueDate = Carbon::parse(
                $borrowing->due_at
            )->startOfDay();
        }

        /*
     * ========================================================
     * TANGGAL AKHIR
     * ========================================================
     *
     * Sudah kembali:
     *   gunakan tanggal pengembalian.
     *
     * Belum kembali:
     *   gunakan hari ini.
     */
        $endDate = $borrowing->returned_at
            ? Carbon::parse(
                $borrowing->returned_at
            )->startOfDay()
            : now()->startOfDay();

        $lateDays = 0;
        $lateStart = null;
        $lateEnd = null;

        /*
     * Deadline aktif dimulai dari deadline awal.
     */
        $currentDueDate = $originalDueDate;

        /*
     * ========================================================
     * HITUNG SETIAP PERIODE SEBELUM PERPANJANGAN
     * ========================================================
     */
        foreach ($history as $extension) {

            $extensionDate = Carbon::parse(
                $extension['extension_date']
                    ?? $extension['new_due_at']
            )->startOfDay();

            /*
         * Keterlambatan dimulai H+1 dari deadline lama.
         */
            $latePeriodStart = $currentDueDate
                ->copy()
                ->addDay();

            /*
         * Kalau perpanjangan dilakukan setelah deadline,
         * tanggal perpanjangan tetap dihitung sebagai
         * hari terlambat.
         *
         * Contoh:
         *
         * Deadline : 27 Sep
         * Extend  : 28 Sep
         *
         * 28 Sep = 1 hari terlambat.
         */
            if ($extensionDate->gte($latePeriodStart)) {

                $periodEnd = $extensionDate->copy();

                /*
             * Jangan menghitung melewati tanggal kembali.
             */
                if ($periodEnd->gt($endDate)) {
                    $periodEnd = $endDate->copy();
                }

                if ($latePeriodStart->lte($periodEnd)) {

                    $days = $latePeriodStart->diffInDays(
                        $periodEnd
                    ) + 1;

                    $lateDays += $days;

                    if (!$lateStart) {
                        $lateStart = $latePeriodStart->copy();
                    }

                    $lateEnd = $periodEnd->copy();
                }
            }

            /*
         * Setelah extension disetujui,
         * deadline baru menjadi deadline aktif.
         */
            $currentDueDate = Carbon::parse(
                $extension['new_due_at']
            )->startOfDay();

            /*
         * Kalau sudah dikembalikan sebelum
         * periode berikutnya dimulai, berhenti.
         */
            if (
                $borrowing->returned_at
                &&
                $endDate->lt(
                    $currentDueDate->copy()->addDay()
                )
            ) {
                break;
            }
        }

        /*
     * ========================================================
     * HITUNG KETERLAMBATAN SETELAH EXTENSION TERAKHIR
     * ========================================================
     */
        $finalLateStart = $currentDueDate
            ->copy()
            ->addDay();

        if ($endDate->gte($finalLateStart)) {

            $days = $finalLateStart->diffInDays(
                $endDate
            ) + 1;

            $lateDays += $days;

            if (!$lateStart) {
                $lateStart = $finalLateStart->copy();
            }

            $lateEnd = $endDate->copy();
        }

        return [
            'is_late' => $lateDays > 0,

            'late_start' => $lateStart,

            'late_end' => $lateEnd,

            'late_days' => $lateDays,

            /*
         * Ini penting supaya PDF bisa membaca
         * semua tanggal perpanjangan.
         */
            'extension_history' => $history->all(),
        ];
    }


    /**
     * ============================================================
     * AMBIL PEMINJAMAN YANG PERNAH TERLAMBAT
     * ============================================================
     */
    private function getHistoricalLateBorrowings(
        $startDate,
        $endDate
    ) {
        $borrowings = Borrowing::with([
            'member',
            'details.book',
        ])
            ->whereNotNull('due_at')
            ->get();

        return $borrowings
            ->filter(function ($borrowing) use (
                $startDate,
                $endDate
            ) {

                $lateInfo =
                    $this->getLateInfo($borrowing);

                if (!$lateInfo['is_late']) {
                    return false;
                }

                $lateStart =
                    $lateInfo['late_start'];

                $lateEnd =
                    $lateInfo['late_end'];

                /*
             * Riwayat keterlambatan dianggap masuk periode
             * jika periode keterlambatannya bersinggungan
             * dengan filter tanggal laporan.
             */
                if (
                    $lateStart
                    &&
                    $lateEnd
                    &&
                    $lateStart->lte($endDate)
                    &&
                    $lateEnd->gte($startDate)
                ) {
                    return true;
                }

                return false;
            })
            ->sortByDesc(function ($borrowing) {

                $lateInfo =
                    $this->getLateInfo($borrowing);

                return $lateInfo['late_start']
                    ? $lateInfo['late_start']->timestamp
                    : 0;
            })
            ->values();
    }


    /**
     * ============================================================
     * GRAFIK PEMINJAMAN
     * ============================================================
     */
    private function generateBorrowChart(
        $startDate,
        $endDate,
        $type
    ) {
        return $this->generateDateChart(
            Borrowing::query(),
            'borrowed_at',
            $startDate,
            $endDate,
            $type,
            function ($query) {
                return $query;
            }
        );
    }


    /**
     * ============================================================
     * GRAFIK KETERLAMBATAN
     * ============================================================
     */
    private function generateLateChart(
        $startDate,
        $endDate,
        $type
    ) {
        $lateBorrowings =
            $this->getHistoricalLateBorrowings(
                $startDate,
                $endDate
            );

        /*
     * ========================================================
     * HARIAN
     * ========================================================
     */
        if ($type === 'day') {

            $labels = [
                '00',
                '04',
                '08',
                '12',
                '16',
                '20',
            ];

            $bars = [];

            foreach ($labels as $hour) {

                $start =
                    $startDate
                    ->copy()
                    ->setHour((int) $hour)
                    ->startOfHour();

                $end =
                    $start
                    ->copy()
                    ->addHours(3)
                    ->endOfHour();

                $count =
                    $lateBorrowings
                    ->filter(function ($borrowing) use (
                        $start,
                        $end
                    ) {

                        $lateInfo =
                            $this->getLateInfo(
                                $borrowing
                            );

                        if (!$lateInfo['is_late']) {
                            return false;
                        }

                        $lateStart =
                            $lateInfo['late_start'];

                        return $lateStart
                            && $lateStart->between(
                                $start,
                                $end
                            );
                    })
                    ->count();

                $bars[] =
                    $count;
            }

            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }

        /*
     * ========================================================
     * MINGGUAN
     * ========================================================
     */
        if ($type === 'week') {

            $labels = [];

            $bars = [];

            $cursor =
                $startDate->copy();

            while (
                $cursor->lte($endDate)
            ) {

                $dayStart =
                    $cursor
                    ->copy()
                    ->startOfDay();

                $dayEnd =
                    $cursor
                    ->copy()
                    ->endOfDay();

                $labels[] =
                    $cursor->translatedFormat('D');

                $bars[] =
                    $lateBorrowings
                    ->filter(function ($borrowing) use (
                        $dayStart,
                        $dayEnd
                    ) {

                        $lateInfo =
                            $this->getLateInfo(
                                $borrowing
                            );

                        if (!$lateInfo['is_late']) {
                            return false;
                        }

                        $lateStart =
                            $lateInfo['late_start'];

                        return $lateStart
                            && $lateStart->between(
                                $dayStart,
                                $dayEnd
                            );
                    })
                    ->count();

                $cursor->addDay();
            }

            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }

        /*
     * ========================================================
     * BULANAN
     * ========================================================
     */
        $labels = [];

        $bars = [];

        $cursor =
            $startDate->copy();

        $weekNumber = 1;

        while (
            $cursor->lte($endDate)
        ) {

            $weekStart =
                $cursor
                ->copy()
                ->startOfDay();

            $weekEnd =
                $cursor
                ->copy()
                ->addDays(6)
                ->endOfDay();

            if (
                $weekEnd->gt($endDate)
            ) {
                $weekEnd =
                    $endDate->copy();
            }

            $labels[] =
                'M' . $weekNumber;

            $bars[] =
                $lateBorrowings
                ->filter(function ($borrowing) use (
                    $weekStart,
                    $weekEnd
                ) {

                    $lateInfo =
                        $this->getLateInfo(
                            $borrowing
                        );

                    if (!$lateInfo['is_late']) {
                        return false;
                    }

                    $lateStart =
                        $lateInfo['late_start'];

                    return $lateStart
                        && $lateStart->between(
                            $weekStart,
                            $weekEnd
                        );
                })
                ->count();

            $cursor =
                $weekEnd
                ->copy()
                ->addSecond();

            $weekNumber++;
        }

        return [
            $labels,
            $this->normalizeBars($bars)
        ];
    }


    /**
     * ============================================================
     * GRAFIK KOLEKSI
     * ============================================================
     */
    private function generateCollectionChart(
        $startDate,
        $endDate,
        $type
    ) {
        /*
         * Grafik koleksi menggunakan jumlah seluruh perubahan
         * koleksi per bagian periode.
         */
        $data =
            $this->getCollectionReportData(
                $startDate,
                $endDate
            );


        /*
         * ========================================================
         * HARIAN
         * ========================================================
         */
        if ($type === 'day') {

            $labels = [
                '00',
                '04',
                '08',
                '12',
                '16',
                '20',
            ];

            $bars = [];


            foreach ($labels as $hour) {

                $start =
                    $startDate
                    ->copy()
                    ->setHour(
                        (int) $hour
                    )
                    ->startOfHour();


                $end =
                    $start
                    ->copy()
                    ->addHours(3)
                    ->endOfHour();


                $count =
                    $data
                    ->filter(function ($item) use (
                        $start,
                        $end
                    ) {

                        return $item['sort_date']
                            ->between(
                                $start,
                                $end
                            );
                    })
                    ->count();


                $bars[] =
                    $count;
            }


            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }


        /*
         * ========================================================
         * MINGGUAN
         * ========================================================
         */
        if ($type === 'week') {

            $labels = [];

            $bars = [];

            $cursor =
                $startDate->copy();


            while (
                $cursor->lte(
                    $endDate
                )
            ) {

                $dayStart =
                    $cursor
                    ->copy()
                    ->startOfDay();


                $dayEnd =
                    $cursor
                    ->copy()
                    ->endOfDay();


                $labels[] =
                    $cursor->translatedFormat(
                        'D'
                    );


                $bars[] =
                    $data
                    ->filter(function ($item) use (
                        $dayStart,
                        $dayEnd
                    ) {

                        return $item['sort_date']
                            ->between(
                                $dayStart,
                                $dayEnd
                            );
                    })
                    ->count();


                $cursor->addDay();
            }


            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }


        /*
         * ========================================================
         * BULANAN
         * ========================================================
         */
        $labels = [];

        $bars = [];

        $cursor =
            $startDate->copy();

        $weekNumber = 1;


        while (
            $cursor->lte(
                $endDate
            )
        ) {

            $weekStart =
                $cursor
                ->copy()
                ->startOfDay();


            $weekEnd =
                $cursor
                ->copy()
                ->addDays(6)
                ->endOfDay();


            if (
                $weekEnd->gt(
                    $endDate
                )
            ) {

                $weekEnd =
                    $endDate->copy();
            }


            $labels[] =
                'M' . $weekNumber;


            $bars[] =
                $data
                ->filter(function ($item) use (
                    $weekStart,
                    $weekEnd
                ) {

                    return $item['sort_date']
                        ->between(
                            $weekStart,
                            $weekEnd
                        );
                })
                ->count();


            $cursor =
                $weekEnd
                ->copy()
                ->addSecond();


            $weekNumber++;
        }


        return [
            $labels,
            $this->normalizeBars($bars)
        ];
    }


    /**
     * ============================================================
     * MEMBER AKTIF
     * ============================================================
     */
    private function getActiveMemberIds(
        $startDate,
        $endDate
    ) {
        $borrowingMemberIds =
            Borrowing::query()
            ->whereDate(
                'borrowed_at',
                '<=',
                $endDate
            )
            ->where(function ($query) use ($startDate) {

                $query
                    ->whereNull(
                        'returned_at'
                    )
                    ->orWhereDate(
                        'returned_at',
                        '>=',
                        $startDate
                    );
            })
            ->pluck(
                'member_id'
            );


        $reservationMemberIds =
            Reservation::query()
            ->whereNotIn(
                'status',
                [
                    'ditolak',
                    'dibatalkan',
                ]
            )
            ->whereDate(
                'reserved_at',
                '<=',
                $endDate
            )
            ->where(function ($query) use ($startDate) {

                $query
                    ->whereNull(
                        'expires_at'
                    )
                    ->orWhereDate(
                        'expires_at',
                        '>=',
                        $startDate
                    );
            })
            ->pluck(
                'member_id'
            );


        return $borrowingMemberIds
            ->merge(
                $reservationMemberIds
            )
            ->unique()
            ->values()
            ->all();
    }


    /**
     * ============================================================
     * GRAFIK MEMBER
     * ============================================================
     */
    private function generateMemberChart(
        $startDate,
        $endDate,
        $type
    ) {
        if ($type === 'day') {

            $memberIds =
                $this->getActiveMemberIds(
                    $startDate,
                    $endDate
                );


            return [
                ['Hari'],
                $this->normalizeBars([
                    count($memberIds)
                ])
            ];
        }


        if ($type === 'week') {

            $labels = [];

            $bars = [];

            $cursor =
                $startDate->copy();


            while (
                $cursor->lte(
                    $endDate
                )
            ) {

                $dayStart =
                    $cursor
                    ->copy()
                    ->startOfDay();

                $dayEnd =
                    $cursor
                    ->copy()
                    ->endOfDay();


                $memberIds =
                    $this->getActiveMemberIds(
                        $dayStart,
                        $dayEnd
                    );


                $labels[] =
                    $cursor->translatedFormat(
                        'D'
                    );


                $bars[] =
                    count($memberIds);


                $cursor->addDay();
            }


            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }


        $labels = [];

        $bars = [];

        $cursor =
            $startDate->copy();

        $weekNumber = 1;


        while (
            $cursor->lte(
                $endDate
            )
        ) {

            $weekStart =
                $cursor
                ->copy()
                ->startOfDay();


            $weekEnd =
                $cursor
                ->copy()
                ->addDays(6)
                ->endOfDay();


            if (
                $weekEnd->gt(
                    $endDate
                )
            ) {

                $weekEnd =
                    $endDate->copy();
            }


            $memberIds =
                $this->getActiveMemberIds(
                    $weekStart,
                    $weekEnd
                );


            $labels[] =
                'M' . $weekNumber;


            $bars[] =
                count($memberIds);


            $cursor =
                $weekEnd
                ->copy()
                ->addSecond();


            $weekNumber++;
        }


        return [
            $labels,
            $this->normalizeBars($bars)
        ];
    }


    /**
     * ============================================================
     * GRAFIK BERDASARKAN PERIODE
     * ============================================================
     */
    private function generateDateChart(
        $query,
        $column,
        $startDate,
        $endDate,
        $type,
        $callback
    ) {
        $query =
            $callback($query);


        if ($type === 'day') {

            $labels = [
                '00',
                '04',
                '08',
                '12',
                '16',
                '20',
            ];

            $bars = [];


            foreach ($labels as $hour) {

                $start =
                    $startDate
                    ->copy()
                    ->setHour(
                        (int) $hour
                    )
                    ->startOfHour();


                $end =
                    $start
                    ->copy()
                    ->addHours(3)
                    ->endOfHour();


                $bars[] =
                    (clone $query)
                    ->whereBetween(
                        $column,
                        [
                            $start,
                            $end
                        ]
                    )
                    ->count();
            }


            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }


        if ($type === 'week') {

            $labels = [];

            $bars = [];

            $cursor =
                $startDate->copy();


            while (
                $cursor->lte(
                    $endDate
                )
            ) {

                $dayStart =
                    $cursor
                    ->copy()
                    ->startOfDay();


                $dayEnd =
                    $cursor
                    ->copy()
                    ->endOfDay();


                $labels[] =
                    $cursor->translatedFormat(
                        'D'
                    );


                $bars[] =
                    (clone $query)
                    ->whereBetween(
                        $column,
                        [
                            $dayStart,
                            $dayEnd
                        ]
                    )
                    ->count();


                $cursor->addDay();
            }


            return [
                $labels,
                $this->normalizeBars($bars)
            ];
        }


        $labels = [];

        $bars = [];

        $cursor =
            $startDate->copy();

        $weekNumber = 1;


        while (
            $cursor->lte(
                $endDate
            )
        ) {

            $weekStart =
                $cursor
                ->copy()
                ->startOfDay();


            $weekEnd =
                $cursor
                ->copy()
                ->addDays(6)
                ->endOfDay();


            if (
                $weekEnd->gt(
                    $endDate
                )
            ) {

                $weekEnd =
                    $endDate->copy();
            }


            $labels[] =
                'M' . $weekNumber;


            $bars[] =
                (clone $query)
                ->whereBetween(
                    $column,
                    [
                        $weekStart,
                        $weekEnd
                    ]
                )
                ->count();


            $cursor =
                $weekEnd
                ->copy()
                ->addSecond();


            $weekNumber++;
        }


        return [
            $labels,
            $this->normalizeBars($bars)
        ];
    }


    /**
     * ============================================================
     * NORMALISASI GRAFIK
     * ============================================================
     */
    private function normalizeBars(
        array $values
    ) {
        if (empty($values)) {
            return [];
        }


        $max =
            max($values);


        if ($max <= 0) {

            return array_fill(
                0,
                count($values),
                8
            );
        }


        return array_map(
            function ($value) use ($max) {

                return max(
                    8,
                    round(
                        ($value / $max)
                            * 100
                    )
                );
            },
            $values
        );
    }


    /**
     * ============================================================
     * CREATE REPORT
     * ============================================================
     */
    public function create()
    {
        return view(
            'reports.create'
        );
    }


    /**
     * ============================================================
     * STORE REPORT
     * ============================================================
     */
    public function store(
        Request $request
    ) {
        $validated =
            $request->validate(
                [
                    'jenis' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'kategori' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'status' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'anggota' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'urutan' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'tanggal_mulai' => [
                        'required',
                        'date',
                    ],

                    'tanggal_selesai' => [
                        'required',
                        'date',
                        'after_or_equal:tanggal_mulai',
                    ],
                ]
            );


        $reports =
            $this->getReports(
                $request
            );


        $newId =
            empty($reports)
            ? 1
            : max(
                array_keys($reports)
            ) + 1;


        $validated['id'] =
            $newId;


        $reports[$newId] =
            $validated;


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route(
                'reports.index'
            )
            ->with(
                'success',
                'Laporan berhasil ditambahkan.'
            );
    }


    /**
     * ============================================================
     * EDIT REPORT
     * ============================================================
     */
    public function edit(
        Request $request,
        $id
    ) {
        $reports =
            $this->getReports(
                $request
            );


        if (
            !isset(
                $reports[$id]
            )
        ) {
            abort(404);
        }


        $report =
            $reports[$id];


        return view(
            'reports.edit',
            compact('report')
        );
    }


    /**
     * ============================================================
     * UPDATE REPORT
     * ============================================================
     */
    public function update(
        Request $request,
        $id
    ) {
        $reports =
            $this->getReports(
                $request
            );


        if (
            !isset(
                $reports[$id]
            )
        ) {
            abort(404);
        }


        $validated =
            $request->validate(
                [
                    'jenis' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'kategori' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'status' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'anggota' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'urutan' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'tanggal_mulai' => [
                        'required',
                        'date',
                    ],

                    'tanggal_selesai' => [
                        'required',
                        'date',
                        'after_or_equal:tanggal_mulai',
                    ],
                ]
            );


        $validated['id'] =
            $id;


        $reports[$id] =
            $validated;


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route(
                'reports.index'
            )
            ->with(
                'success',
                'Laporan berhasil diperbarui.'
            );
    }


    /**
     * ============================================================
     * DELETE REPORT
     * ============================================================
     */
    public function destroy(
        Request $request,
        $id
    ) {
        $reports =
            $this->getReports(
                $request
            );


        if (
            !isset(
                $reports[$id]
            )
        ) {
            abort(404);
        }


        unset(
            $reports[$id]
        );


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route(
                'reports.index'
            )
            ->with(
                'success',
                'Laporan berhasil dihapus.'
            );
    }
}
