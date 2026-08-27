@extends('layouts.app')

@section('title', 'Edit Laporan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/report-form.css') }}">
@endpush

@section('content')

<section class="report-form-page">

    <div class="report-form-card">

        <h1>
            Edit Laporan
        </h1>


        @if($errors->any())

            <div class="report-form-alert">

                <strong>
                    Periksa kembali data berikut:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route('reports.update', $report['id']) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- JENIS LAPORAN --}}

            <div class="report-form-group">

                <label for="jenis">
                    Jenis Laporan
                </label>


                <select
                    id="jenis"
                    name="jenis"
                    required
                >

                    <option value="">
                        Pilih Jenis Laporan
                    </option>


                    <option
                        value="Laporan Peminjaman Bulanan"
                        {{ $report['jenis'] === 'Laporan Peminjaman Bulanan' ? 'selected' : '' }}
                    >
                        Laporan Peminjaman Bulanan
                    </option>


                    <option
                        value="Laporan Keterlambatan"
                        {{ $report['jenis'] === 'Laporan Keterlambatan' ? 'selected' : '' }}
                    >
                        Laporan Keterlambatan
                    </option>


                    <option
                        value="Laporan Koleksi Buku"
                        {{ $report['jenis'] === 'Laporan Koleksi Buku' ? 'selected' : '' }}
                    >
                        Laporan Koleksi Buku
                    </option>


                    <option
                        value="Laporan Anggota Aktif"
                        {{ $report['jenis'] === 'Laporan Anggota Aktif' ? 'selected' : '' }}
                    >
                        Laporan Anggota Aktif
                    </option>

                </select>

            </div>


            {{-- KATEGORI --}}

            <div class="report-form-group">

                <label for="kategori">
                    Kategori Buku
                </label>


                <select
                    id="kategori"
                    name="kategori"
                    required
                >

                    <option
                        value="Semua Buku"
                        {{ $report['kategori'] === 'Semua Buku' ? 'selected' : '' }}
                    >
                        Semua Buku
                    </option>


                    <option
                        value="Novel"
                        {{ $report['kategori'] === 'Novel' ? 'selected' : '' }}
                    >
                        Novel
                    </option>


                    <option
                        value="Teknik"
                        {{ $report['kategori'] === 'Teknik' ? 'selected' : '' }}
                    >
                        Teknik
                    </option>


                    <option
                        value="Referensi"
                        {{ $report['kategori'] === 'Referensi' ? 'selected' : '' }}
                    >
                        Referensi
                    </option>


                    <option
                        value="Umum"
                        {{ $report['kategori'] === 'Umum' ? 'selected' : '' }}
                    >
                        Umum
                    </option>

                </select>

            </div>


            {{-- STATUS --}}

            <div class="report-form-group">

                <label for="status">
                    Status
                </label>


                <select
                    id="status"
                    name="status"
                    required
                >

                    <option
                        value="Semua Status"
                        {{ $report['status'] === 'Semua Status' ? 'selected' : '' }}
                    >
                        Semua Status
                    </option>


                    <option
                        value="Tersedia"
                        {{ $report['status'] === 'Tersedia' ? 'selected' : '' }}
                    >
                        Tersedia
                    </option>


                    <option
                        value="Dipinjam"
                        {{ $report['status'] === 'Dipinjam' ? 'selected' : '' }}
                    >
                        Dipinjam
                    </option>


                    <option
                        value="Terlambat"
                        {{ $report['status'] === 'Terlambat' ? 'selected' : '' }}
                    >
                        Terlambat
                    </option>


                    <option
                        value="Aktif"
                        {{ $report['status'] === 'Aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                </select>

            </div>


            {{-- ANGGOTA --}}

            <div class="report-form-group">

                <label for="anggota">
                    Anggota
                </label>


                <select
                    id="anggota"
                    name="anggota"
                    required
                >

                    <option
                        value="Semua Anggota"
                        {{ $report['anggota'] === 'Semua Anggota' ? 'selected' : '' }}
                    >
                        Semua Anggota
                    </option>


                    <option
                        value="Siswa"
                        {{ $report['anggota'] === 'Siswa' ? 'selected' : '' }}
                    >
                        Siswa
                    </option>


                    <option
                        value="Guru"
                        {{ $report['anggota'] === 'Guru' ? 'selected' : '' }}
                    >
                        Guru
                    </option>


                    <option
                        value="Umum"
                        {{ $report['anggota'] === 'Umum' ? 'selected' : '' }}
                    >
                        Umum
                    </option>

                </select>

            </div>


            {{-- URUTAN --}}

            <div class="report-form-group">

                <label for="urutan">
                    Urutan
                </label>


                <select
                    id="urutan"
                    name="urutan"
                    required
                >

                    <option
                        value="Terbaru - Terlama"
                        {{ $report['urutan'] === 'Terbaru - Terlama' ? 'selected' : '' }}
                    >
                        Terbaru - Terlama
                    </option>


                    <option
                        value="Terlama - Terbaru"
                        {{ $report['urutan'] === 'Terlama - Terbaru' ? 'selected' : '' }}
                    >
                        Terlama - Terbaru
                    </option>

                </select>

            </div>


            {{-- PERIODE --}}

            <div class="report-form-group">

                <label>
                    Periode
                </label>


                <div class="report-date-row">

                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="{{ $report['tanggal_mulai'] }}"
                        required
                    >


                    <span>
                        s/d
                    </span>


                    <input
                        type="date"
                        name="tanggal_selesai"
                        value="{{ $report['tanggal_selesai'] }}"
                        required
                    >

                </div>

            </div>


            {{-- ACTION --}}

            <div class="report-form-actions">

                <a
                    href="{{ route('reports.index') }}"
                    class="report-form-cancel"
                >
                    Batal
                </a>


                <button
                    type="submit"
                    class="report-form-submit"
                >
                    Simpan
                </button>

            </div>

        </form>

    </div>

</section>

@endsection