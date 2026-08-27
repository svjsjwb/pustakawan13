@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="book-form-page">

    <div class="book-form-card">

        <h2>Tambah Buku Baru</h2>

        <form
            action="{{ route('books.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="book-form-grid">

                {{-- 1. NO INVENTARIS --}}
                <div class="book-form-group">
                    <label for="no_inventaris">
                        No Inventaris
                    </label>

                    <input
                        type="text"
                        id="no_inventaris"
                        name="no_inventaris"
                        placeholder="cth. INV-001"
                        value="{{ old('no_inventaris') }}"
                    >
                </div>


                {{-- 2. KODE --}}
                <div class="book-form-group">
                    <label for="kode">
                        Kode
                    </label>

                    <select id="kode" name="kode">
                        <option value="">-- Pilih Kode --</option>

                        <option value="Kode 1"
                            {{ old('kode') == 'Kode 1' ? 'selected' : '' }}>
                            Kode 1
                        </option>

                        <option value="Kode 2"
                            {{ old('kode') == 'Kode 2' ? 'selected' : '' }}>
                            Kode 2
                        </option>

                        <option value="Kode 3"
                            {{ old('kode') == 'Kode 3' ? 'selected' : '' }}>
                            Kode 3
                        </option>

                        <option value="Kode 4"
                            {{ old('kode') == 'Kode 4' ? 'selected' : '' }}>
                            Kode 4
                        </option>

                        <option value="Kode 5"
                            {{ old('kode') == 'Kode 5' ? 'selected' : '' }}>
                            Kode 5
                        </option>
                    </select>
                </div>


                {{-- 3. JUDUL --}}
                <div class="book-form-group">
                    <label for="judul">
                        Judul
                    </label>

                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        placeholder="cth. Laskar Pelangi"
                        value="{{ old('judul') }}"
                    >
                </div>


                {{-- 4. DDC --}}
                <div class="book-form-group">
                    <label for="ddc">
                        DDC
                    </label>

                    <input
                        type="text"
                        id="ddc"
                        name="ddc"
                        placeholder="cth. 813"
                        value="{{ old('ddc') }}"
                    >
                </div>


                {{-- 5. KATEGORI --}}
                <div class="book-form-group">
                    <label for="kategori">
                        Kategori - Nama Kategori
                    </label>

                    <select id="kategori" name="kategori">
                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        <option value="Novel"
                            {{ old('kategori') == 'Novel' ? 'selected' : '' }}>
                            Novel
                        </option>

                        <option value="Pendidikan"
                            {{ old('kategori') == 'Pendidikan' ? 'selected' : '' }}>
                            Pendidikan
                        </option>

                        <option value="Sejarah"
                            {{ old('kategori') == 'Sejarah' ? 'selected' : '' }}>
                            Sejarah
                        </option>

                        <option value="Teknologi"
                            {{ old('kategori') == 'Teknologi' ? 'selected' : '' }}>
                            Teknologi
                        </option>
                    </select>
                </div>


                {{-- 6. TAHUN TERBIT --}}
                <div class="book-form-group">
                    <label for="tahun_terbit">
                        Tahun Terbit
                    </label>

                    <input
                        type="number"
                        id="tahun_terbit"
                        name="tahun_terbit"
                        placeholder="cth. 2026"
                        value="{{ old('tahun_terbit') }}"
                    >
                </div>


                {{-- 7. ISBN --}}
                <div class="book-form-group">
                    <label for="isbn">
                        ISBN
                    </label>

                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        placeholder="cth. 978-000-000-000-0"
                        value="{{ old('isbn') }}"
                    >
                </div>


                {{-- 8. RAK --}}
                <div class="book-form-group">
                    <label for="rak">
                        Rak - Nama Rak
                    </label>

                    <select id="rak" name="rak">
                        <option value="">
                            -- Pilih Rak --
                        </option>

                        <option value="Rak A"
                            {{ old('rak') == 'Rak A' ? 'selected' : '' }}>
                            Rak A
                        </option>

                        <option value="Rak B"
                            {{ old('rak') == 'Rak B' ? 'selected' : '' }}>
                            Rak B
                        </option>

                        <option value="Rak C"
                            {{ old('rak') == 'Rak C' ? 'selected' : '' }}>
                            Rak C
                        </option>
                    </select>
                </div>


                {{-- 9. EDISI --}}
                <div class="book-form-group">
                    <label for="edisi">
                        Edisi
                    </label>

                    <input
                        type="text"
                        id="edisi"
                        name="edisi"
                        placeholder="cth. Edisi 1"
                        value="{{ old('edisi') }}"
                    >
                </div>


                {{-- 10. KOLASI --}}
                <div class="book-form-group">
                    <label for="kolasi">
                        Kolasi
                    </label>

                    <input
                        type="text"
                        id="kolasi"
                        name="kolasi"
                        placeholder="cth. 250 halaman"
                        value="{{ old('kolasi') }}"
                    >
                </div>


                {{-- 11. IMPRINT --}}
                <div class="book-form-group">
                    <label for="imprint">
                        Imprint
                    </label>

                    <input
                        type="text"
                        id="imprint"
                        name="imprint"
                        placeholder="cth. Gramedia Pustaka Utama"
                        value="{{ old('imprint') }}"
                    >
                </div>


                {{-- 12. SUBYEK --}}
                <div class="book-form-group">
                    <label for="subyek">
                        Subyek
                    </label>

                    <input
                        type="text"
                        id="subyek"
                        name="subyek"
                        placeholder="cth. Sastra Indonesia"
                        value="{{ old('subyek') }}"
                    >
                </div>


                {{-- 13. COVER IMAGE --}}
                <div class="book-form-group">
                    <label for="cover_image">
                        Cover Image
                    </label>

                    <div class="book-cover-input">
                        <input
                            type="file"
                            id="cover_image"
                            name="cover_image"
                            accept=".jpg,.jpeg,.png"
                        >
                    </div>

                    <p class="book-cover-help">
                        Format: JPG, JPEG, PNG. Maksimal 2 MB.
                    </p>
                </div>


                {{-- 14. TANGGAL INPUT --}}
                <div class="book-form-group">
                    <label for="tanggal_input">
                        Tanggal Input
                    </label>

                    <input
                        type="date"
                        id="tanggal_input"
                        name="tanggal_input"
                        value="{{ old('tanggal_input') }}"
                    >
                </div>


                {{-- 15. KATA KUNCI --}}
                <div class="book-form-group">
                    <label for="kata_kunci">
                        Kata Kunci
                    </label>

                    <input
                        type="text"
                        id="kata_kunci"
                        name="kata_kunci"
                        placeholder="cth. novel, sastra, persahabatan"
                        value="{{ old('kata_kunci') }}"
                    >
                </div>


                {{-- 16. STATUS BUKU --}}
                <div class="book-form-group">
                    <label for="status_buku">
                        Status Buku
                    </label>

                    <select id="status_buku" name="status_buku">

                        <option value="">
                            -- Pilih Status --
                        </option>

                        <option value="Baik"
                            {{ old('status_buku') == 'Baik' ? 'selected' : '' }}>
                            Baik
                        </option>

                        <option value="Rusak"
                            {{ old('status_buku') == 'Rusak' ? 'selected' : '' }}>
                            Rusak
                        </option>

                    </select>
                </div>


                {{-- 17. KELOMPOK --}}
                <div class="book-form-group">
                    <label for="kelompok">
                        Kelompok
                    </label>

                    <input
                        type="text"
                        id="kelompok"
                        name="kelompok"
                        placeholder="cth. Fiksi"
                        value="{{ old('kelompok') }}"
                    >
                </div>


                {{-- 18. SINOPSIS --}}
                <div class="book-form-group book-form-full">
                    <label for="sinopsis">
                        Sinopsis
                    </label>

                    <textarea
                        id="sinopsis"
                        name="sinopsis"
                        placeholder="Tuliskan sinopsis singkat mengenai buku..."
                    >{{ old('sinopsis') }}</textarea>
                </div>

            </div>


            {{-- BUTTON --}}
            <div class="book-form-actions">

                <a
                    href="{{ route('books.index') }}"
                    class="book-btn-cancel"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="book-btn-save"
                >
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection