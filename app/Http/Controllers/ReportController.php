<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
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

    private function getReports(Request $request)
    {
        if (!$request->session()->has('reports')) {
            $request->session()->put('reports', $this->defaultReports());
        }

        return $request->session()->get('reports', []);
    }

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
                $label = $startDate->locale('id')->translatedFormat('d M Y') . ' - ' . $endDate->locale('id')->translatedFormat('d M Y');

                if ($diffDays <= 1) {
                    $rangeType = 'day';
                } elseif ($diffDays <= 14) {
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
                // Fallback jika format error
            }
        }

        $startDate = now()->startOfMonth()->startOfDay();
        $endDate = now()->endOfMonth()->endOfDay();
        $label = $startDate->locale('id')->translatedFormat('d M Y') . ' - ' . $endDate->locale('id')->translatedFormat('d M Y');

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

    public function index(Request $request)
    {
        $reports = $this->getReports($request);
        $period = $this->getPeriod($request);

        $startDate = $period['start'];
        $endDate = $period['end'];
        $periodLabel = $period['label'];
        $periodType = $period['type'];
        $selectedDate = $period['selectedDate'];
        $startDateInput = $period['startDateInput'];
        $endDateInput = $period['endDateInput'];

        // Badge pojok kanan: Selalu periode 1 bulan kalender penuh bulan ini
        $monthlyPeriodBadge = now()->locale('id')->startOfMonth()->translatedFormat('d M Y') . ' - ' . now()->locale('id')->endOfMonth()->translatedFormat('d M Y');

        /*
         * 1. DATA PEMINJAMAN
         */
        $borrowings = Borrowing::with(['member', 'details.book'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->latest('borrowed_at')
            ->get();

        $totalBorrowed = $borrowings->count();
        $totalReturned = $borrowings->where('status', 'dikembalikan')->count();
        $totalActiveBorrow = $borrowings->where('status', 'dipinjam')->count();

        // Grafik Peminjaman (Data Riil & Normalisasi Bar)
        [$borrowChartLabels, $borrowChartBarsRaw, $borrowChartBarsNorm] = $this->generateDateChart(
            Borrowing::query(),
            'borrowed_at',
            $startDate,
            $endDate,
            $periodType
        );

        /*
         * 2. DATA KETERLAMBATAN
         */
        $lateBorrowingsQuery = Borrowing::where('status', 'dipinjam')
            ->where('due_at', '<', now())
            ->whereBetween('due_at', [$startDate, $endDate]);

        $lateBorrowings = (clone $lateBorrowingsQuery)->count();

        [$lateChartLabels, $lateChartBarsRaw, $lateChartBarsNorm] = $this->generateDateChart(
            Borrowing::query()->where('status', 'dipinjam')->where('due_at', '<', now()),
            'due_at',
            $startDate,
            $endDate,
            $periodType
        );

        /*
         * 3. DATA KOLEKSI BUKU (Masuk / Ditambah di rentang waktu tersebut)
         */
        $totalBooks = Book::whereBetween('created_at', [$startDate, $endDate])->count();

        [$collectionChartLabels, $collectionChartBarsRaw, $collectionChartBarsNorm] = $this->generateDateChart(
            Book::query(),
            'created_at',
            $startDate,
            $endDate,
            $periodType
        );

        /*
         * 4. DATA ANGGOTA AKTIF (Terdaftar aktif di rentang waktu tersebut)
         */
        $activeMembers = Member::where('status', 'aktif')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        [$memberChartLabels, $memberChartBarsRaw, $memberChartBarsNorm] = $this->generateDateChart(
            Member::query()->where('status', 'aktif'),
            'created_at',
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

        return view(
            'reports.index',
            compact(
                'reports',
                'borrowings',
                'borrowingsExportData',
                'totalBorrowed',
                'totalReturned',
                'totalActiveBorrow',
                'lateBorrowings',
                'totalBooks',
                'activeMembers',
                'periodLabel',
                'monthlyPeriodBadge',
                'periodType',
                'selectedDate',
                'startDateInput',
                'endDateInput',
                'startDate',
                'endDate',
                'borrowChartLabels',
                'borrowChartBarsRaw',
                'borrowChartBarsNorm',
                'lateChartLabels',
                'lateChartBarsRaw',
                'lateChartBarsNorm',
                'collectionChartLabels',
                'collectionChartBarsRaw',
                'collectionChartBarsNorm',
                'memberChartLabels',
                'memberChartBarsRaw',
                'memberChartBarsNorm'
            )
        );
    }

    /**
     * Helper membuat data grafik harian / mingguan / bulanan
     */
    private function generateDateChart($query, $column, $startDate, $endDate, $type)
    {
        $labels = [];
        $bars = [];

        if ($type === 'day') {
            $hours = ['00', '04', '08', '12', '16', '20'];
            foreach ($hours as $hour) {
                $start = $startDate->copy()->setHour((int) $hour)->startOfHour();
                $end = $start->copy()->addHours(3)->endOfHour();

                $labels[] = $hour . ':00';
                $bars[] = (clone $query)->whereBetween($column, [$start, $end])->count();
            }
        } elseif ($type === 'week') {
            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();

                $labels[] = $cursor->locale('id')->translatedFormat('d M');
                $bars[] = (clone $query)->whereBetween($column, [$dayStart, $dayEnd])->count();

                $cursor->addDay();
            }
        } else {
            $cursor = $startDate->copy();
            $weekNumber = 1;

            while ($cursor->lte($endDate)) {
                $weekStart = $cursor->copy()->startOfDay();
                $weekEnd = $cursor->copy()->addDays(6)->endOfDay();

                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }

                $labels[] = 'M' . $weekNumber;
                $bars[] = (clone $query)->whereBetween($column, [$weekStart, $weekEnd])->count();

                $cursor = $weekEnd->copy()->addSecond();
                $weekNumber++;
            }
        }

        // Hitung normalisasi persen untuk bar kecil di card (agar tidak flat 0%)
        $max = max($bars) ?: 1;
        $norm = array_map(function ($val) use ($max) {
            return $val > 0 ? max(18, round(($val / $max) * 100)) : 8;
        }, $bars);

        return [$labels, $bars, $norm];
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:100'],
            'anggota' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $reports = $this->getReports($request);
        $newId = empty($reports) ? 1 : max(array_keys($reports)) + 1;
        $validated['id'] = $newId;
        $reports[$newId] = $validated;
        $request->session()->put('reports', $reports);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $reports = $this->getReports($request);
        if (!isset($reports[$id]))
            abort(404);
        $report = $reports[$id];
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $reports = $this->getReports($request);
        if (!isset($reports[$id]))
            abort(404);

        $validated = $request->validate([
            'jenis' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:100'],
            'anggota' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $validated['id'] = $id;
        $reports[$id] = $validated;
        $request->session()->put('reports', $reports);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $reports = $this->getReports($request);
        if (!isset($reports[$id]))
            abort(404);

        unset($reports[$id]);
        $request->session()->put('reports', $reports);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
