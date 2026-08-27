<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
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
                'status' => 'Tersedia',
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
     * TENTUKAN PERIODE DARI CALENDAR
     *
     * day   = 1 hari
     * week  = Senin - Minggu
     * month = tanggal 1 - akhir bulan
     * ============================================================
     */
    /**
     * ============================================================
     * TENTUKAN PERIODE DARI CALENDAR / DATE RANGE PICKER
     * ============================================================
     */
    private function getPeriod(Request $request)
    {
        $startDateInput = $request->query('start_date');
        $endDateInput = $request->query('end_date');

        if ($startDateInput && $endDateInput) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDateInput)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $endDateInput)->endOfDay();

                if ($startDate->gt($endDate)) {
                    $temp = $startDate;
                    $startDate = $endDate->copy()->startOfDay();
                    $endDate = $temp->copy()->endOfDay();
                }

                $diffDays = $startDate->diffInDays($endDate) + 1;
                $label = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');

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
                // Lanjut ke default jika parsing error
            }
        }

        /*
         * Default periode: Awal bulan s/d Akhir bulan ini
         */
        $startDate = now()->startOfMonth()->startOfDay();
        $endDate = now()->endOfMonth()->endOfDay();
        $label = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');

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
         * PERIODE CALENDAR
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
         * DATA PEMINJAMAN BUKU
         * ========================================================
         *
         * Filter query database menggunakan whereBetween pada kolom borrowed_at / created_at.
         */
        $borrowings = Borrowing::with(['member', 'details.book'])
            ->whereBetween(
                'borrowed_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->latest('borrowed_at')
            ->get();

        $borrowedBooks = $borrowings->count();

        // Ringkasan metrik peminjaman
        $totalBorrowed = $borrowings->count();
        $totalReturned = $borrowings->where('status', 'dikembalikan')->count();
        $totalActiveBorrow = $borrowings->where('status', 'dipinjam')->count();
        $totalLate = $borrowings->where('status', 'dipinjam')
            ->filter(function ($item) {
                return $item->due_at && Carbon::parse($item->due_at)->isPast();
            })->count();


        /*
         * Grafik peminjaman.
         */
        [
            $borrowChartLabels,
            $borrowChartBars
        ] = $this->generateBorrowChart(
            $startDate,
            $endDate,
            $periodType
        );


        /*
         * ========================================================
         * KETERLAMBATAN
         * ========================================================
         */
        $lateBorrowings =
            Borrowing::where(
                'status',
                'dipinjam'
            )
            ->where(
                'due_at',
                '<',
                now()
            )
            ->whereBetween(
                'due_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->count();


        [
            $lateChartLabels,
            $lateChartBars
        ] = $this->generateLateChart(
            $startDate,
            $endDate,
            $periodType
        );


        /*
         * ========================================================
         * KOLEKSI BUKU
         * ========================================================
         *
         * Buku yang tercatat pada periode tersebut.
         */
        $totalBooks =
            Book::whereBetween(
                'created_at',
                [
                    $startDate,
                    $endDate
                ]
            )->count();


        [
            $collectionChartLabels,
            $collectionChartBars
        ] = $this->generateCollectionChart(
            $startDate,
            $endDate,
            $periodType
        );


        /*
         * ========================================================
         * ANGGOTA AKTIF
         * ========================================================
         */
        $activeMembers =
            Member::where(
                'status',
                'aktif'
            )
            ->whereBetween(
                'created_at',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->count();


        [
            $memberChartLabels,
            $memberChartBars
        ] = $this->generateMemberChart(
            $startDate,
            $endDate,
            $periodType
        );


        /*
         * Format data untuk kebutuhan ekspor PDF / Excel
         */
        $borrowingsExportData = $borrowings->map(function ($item, $index) {
            $bookTitles = $item->details->map(function ($d) {
                return ($d->book->title ?? 'Buku') . ($d->quantity > 1 ? ' (' . $d->quantity . 'x)' : '');
            })->implode(', ');

            $isLate = ($item->status === 'dipinjam' && $item->due_at && Carbon::parse($item->due_at)->isPast());
            $statusText = $item->status === 'dikembalikan' ? 'Dikembalikan' : ($isLate ? 'Terlambat' : 'Dipinjam');

            return [
                'no' => $index + 1,
                'member_name' => $item->member->name ?? ('Anggota #' . $item->member_id),
                'member_code' => $item->member->member_code ?? '-',
                'books' => $bookTitles ?: 'Tidak ada rincian',
                'borrowed_at' => Carbon::parse($item->borrowed_at)->translatedFormat('d M Y'),
                'due_at' => $item->due_at ? Carbon::parse($item->due_at)->translatedFormat('d M Y') : '-',
                'returned_at' => $item->returned_at ? Carbon::parse($item->returned_at)->translatedFormat('d M Y') : '-',
                'status' => $statusText,
            ];
        })->values()->toArray();


        /*
         * ========================================================
         * KIRIM DATA KE VIEW
         * ========================================================
         */
        return view(
            'reports.index',
            compact(
                'reports',

                'borrowings',
                'borrowingsExportData',
                'totalBorrowed',
                'totalReturned',
                'totalActiveBorrow',
                'totalLate',

                'totalBooks',
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
        return $this->generateDateChart(
            Borrowing::query()
                ->where(
                    'status',
                    'dipinjam'
                )
                ->where(
                    'due_at',
                    '<',
                    now()
                ),
            'due_at',
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
     * GRAFIK KOLEKSI
     * ============================================================
     */
    private function generateCollectionChart(
        $startDate,
        $endDate,
        $type
    ) {
        return $this->generateDateChart(
            Book::query(),
            'created_at',
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
     * GRAFIK MEMBER
     * ============================================================
     */
    private function generateMemberChart(
        $startDate,
        $endDate,
        $type
    ) {
        return $this->generateDateChart(
            Member::query()
                ->where(
                    'status',
                    'aktif'
                ),
            'created_at',
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
     * GENERATE GRAFIK BERDASARKAN PERIODE
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


        /*
         * ========================================================
         * HARIAN
         *
         * Dibagi menjadi 6 bagian waktu.
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
                    (clone $query)
                        ->whereBetween(
                            $column,
                            [
                                $start,
                                $end
                            ]
                        )
                        ->count();


                $bars[] = $count;
            }


            return [
                $labels,
                $this->normalizeBars(
                    $bars
                )
            ];
        }


        /*
         * ========================================================
         * MINGGUAN
         *
         * Satu bar = satu hari.
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
                    $cursor->copy()
                        ->startOfDay();

                $dayEnd =
                    $cursor->copy()
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
                $this->normalizeBars(
                    $bars
                )
            ];
        }


        /*
         * ========================================================
         * BULANAN
         *
         * Dibagi menjadi maksimal 5 minggu.
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
                $cursor->copy()
                    ->startOfDay();

            $weekEnd =
                $cursor->copy()
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
            $this->normalizeBars(
                $bars
            )
        ];
    }


    /**
     * ============================================================
     * NORMALISASI NILAI GRAFIK
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


        /*
         * Jika semua data 0.
         */
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
                ],
                [
                    'jenis.required' =>
                        'Jenis laporan wajib dipilih.',

                    'kategori.required' =>
                        'Kategori buku wajib dipilih.',

                    'status.required' =>
                        'Status wajib dipilih.',

                    'anggota.required' =>
                        'Anggota wajib dipilih.',

                    'urutan.required' =>
                        'Urutan wajib dipilih.',

                    'tanggal_mulai.required' =>
                        'Tanggal mulai wajib diisi.',

                    'tanggal_selesai.required' =>
                        'Tanggal selesai wajib diisi.',

                    'tanggal_selesai.after_or_equal' =>
                        'Tanggal selesai tidak boleh sebelum tanggal mulai.',
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