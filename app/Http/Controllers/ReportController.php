<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Data laporan default.
     *
     * Data ini hanya dipakai saat session
     * belum memiliki data laporan.
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
                'tanggal_mulai' => '2026-04-01',
                'tanggal_selesai' => '2026-08-31',
            ],

            2 => [
                'id' => 2,
                'jenis' => 'Laporan Keterlambatan',
                'kategori' => 'Semua Buku',
                'status' => 'Terlambat',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => '2026-04-01',
                'tanggal_selesai' => '2026-08-31',
            ],

            3 => [
                'id' => 3,
                'jenis' => 'Laporan Koleksi Buku',
                'kategori' => 'Semua Buku',
                'status' => 'Tersedia',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-08-31',
            ],

            4 => [
                'id' => 4,
                'jenis' => 'Laporan Anggota Aktif',
                'kategori' => 'Semua Buku',
                'status' => 'Aktif',
                'anggota' => 'Semua Anggota',
                'urutan' => 'Terbaru - Terlama',
                'tanggal_mulai' => '2026-04-01',
                'tanggal_selesai' => '2026-08-31',
            ],
        ];
    }


    /**
     * Ambil data laporan dari session.
     *
     * Kalau belum ada, gunakan data default.
     */
    private function getReports(Request $request)
    {
        if (!$request->session()->has('reports')) {
            $request->session()->put(
                'reports',
                $this->defaultReports()
            );
        }

        return $request->session()->get('reports', []);
    }


    /**
     * Halaman utama laporan.
     */
    public function index(Request $request)
    {
        $reports = $this->getReports($request);

        return view(
            'reports.index',
            compact('reports')
        );
    }


    /**
     * Halaman tambah laporan.
     */
    public function create()
    {
        return view('reports.create');
    }


    /**
     * Simpan laporan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
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


        $reports = $this->getReports($request);


        /*
         * Cari ID baru.
         */
        $newId = empty($reports)
            ? 1
            : max(array_keys($reports)) + 1;


        $validated['id'] = $newId;


        $reports[$newId] = $validated;


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route('reports.index')
            ->with(
                'success',
                'Laporan berhasil ditambahkan.'
            );
    }


    /**
     * Halaman edit laporan.
     */
    public function edit(
        Request $request,
        $id
    ) {
        $reports = $this->getReports($request);


        if (!isset($reports[$id])) {
            abort(404);
        }


        $report = $reports[$id];


        return view(
            'reports.edit',
            compact('report')
        );
    }


    /**
     * Update laporan.
     */
    public function update(
        Request $request,
        $id
    ) {
        $reports = $this->getReports($request);


        if (!isset($reports[$id])) {
            abort(404);
        }


        $validated = $request->validate(
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


        $validated['id'] = $id;


        $reports[$id] = $validated;


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route('reports.index')
            ->with(
                'success',
                'Laporan berhasil diperbarui.'
            );
    }


    /**
     * Hapus laporan.
     */
    public function destroy(
        Request $request,
        $id
    ) {
        $reports = $this->getReports($request);


        if (!isset($reports[$id])) {
            abort(404);
        }


        unset($reports[$id]);


        $request->session()->put(
            'reports',
            $reports
        );


        return redirect()
            ->route('reports.index')
            ->with(
                'success',
                'Laporan berhasil dihapus.'
            );
    }
}