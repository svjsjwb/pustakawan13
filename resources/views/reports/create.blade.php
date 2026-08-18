@extends('layouts.app')

@section('title', 'Tambah Laporan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/report-form.css') }}">
@endpush

@section('content')

<section class="report-form-page">

    <div class="report-form-card">

        <h1>
            Tambah Laporan
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
            action="{{ route('reports.store') }}"
            method="POST"
        >

            @csrf


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

                    <option value="Laporan Peminjaman Bulanan">
                        Laporan Peminjaman Bulanan
                    </option>

                    <option value="Laporan Keterlambatan">
                        Laporan Keterlambatan
                    </option>

                    <option value="Laporan Koleksi Buku">
                        Laporan Koleksi Buku
                    </option>

                    <option value="Laporan Anggota Aktif">
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

                    <option value="Semua Buku">
                        Semua Buku
                    </option>

                    <option value="Novel">
                        Novel
                    </option>

                    <option value="Teknik">
                        Teknik
                    </option>

                    <option value="Referensi">
                        Referensi
                    </option>

                    <option value="Umum">
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

                    <option value="Semua Status">
                        Semua Status
                    </option>

                    <option value="Tersedia">
                        Tersedia
                    </option>

                    <option value="Dipinjam">
                        Dipinjam
                    </option>

                    <option value="Terlambat">
                        Terlambat
                    </option>

                    <option value="Aktif">
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

                    <option value="Semua Anggota">
                        Semua Anggota
                    </option>

                    <option value="Siswa">
                        Siswa
                    </option>

                    <option value="Guru">
                        Guru
                    </option>

                    <option value="Umum">
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

                    <option value="Terbaru - Terlama">
                        Terbaru - Terlama
                    </option>

                    <option value="Terlama - Terbaru">
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
                        value="{{ old('tanggal_mulai') }}"
                        required
                    >


                    <span>
                        s/d
                    </span>


                    <input
                        type="date"
                        name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}"
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