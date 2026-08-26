@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="book-form-page">

    {{-- Breadcrumb --}}
    <nav class="book-breadcrumb">
        <a href="{{ route('books.index') }}">Manajemen Buku</a>
        <span class="separator">/</span>
        <span class="current">Tambah Buku Baru</span>
    </nav>

    <div class="book-form-card">

        {{-- Header Form --}}
        <div class="book-form-header">
            <h2>Tambah Buku Baru</h2>
            <p class="book-form-subtitle">
                Lengkapi formulir di bawah ini untuk menambahkan koleksi buku baru ke sistem perpustakaan.
            </p>
        </div>

        {{-- Alert Error Global --}}
        @if ($errors->any())
            <div class="form-alert-error">
                <strong>Terdapat beberapa kesalahan pengisian:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('books.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="book-form-grid">

                {{-- SECTION 1: INFORMASI UTAMA BUKU --}}
                <div class="form-section-heading">
                    <div class="form-section-icon">1</div>
                    <span class="form-section-title">Informasi Utama Buku</span>
                </div>

                {{-- Judul Buku (Full Width) --}}
                <div class="book-form-group book-form-full">
                    <label for="title">
                        Judul Buku <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="input @error('title') is-invalid @enderror"
                        placeholder="cth. Laskar Pelangi / Sejarah Peradaban Dunia"
                        value="{{ old('title') }}"
                        required>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No Iventaris --}}
                <div class="book-form-group">
                    <label for="no_iventaris">
                        No. Iventaris <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="no_iventaris"
                        name="no_iventaris"
                        class="input @error('no_iventaris') is-invalid @enderror"
                        placeholder="cth. 123456789"
                        value="{{ old('no_iventaris') }}"
                        required>
                    @error('author')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kode Buku --}}
                <div class="book-form-group">
                    <label for="kode_buku">
                        Kode Buku <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="kode_buku"
                        name="kode_buku"
                        class="input @error('kode_buku') is-invalid @enderror"
                        placeholder="cth. 808.123456789"
                        value="{{ old('kode_buku') }}"
                        required>
                    @error('author')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Penulis --}}
                <div class="book-form-group">
                    <label for="author">
                        Penulis / Pengarang <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="author"
                        name="author"
                        class="input @error('author') is-invalid @enderror"
                        placeholder="cth. Andrea Hirata / Prof. Dr. Soekarno"
                        value="{{ old('author') }}"
                        required>
                    @error('author')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- DDC --}}
                <div class="book-form-group">
                    <label for="ddc">
                        DDC <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="ddc"
                        name="ddc"
                        class="input @error('ddc') is-invalid @enderror"
                        placeholder="cth. 808.123456789"
                        value="{{ old('ddc') }}"
                        required>
                    @error('author')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div class="book-form-group">
                    <label for="category_id">
                        Kategori Buku <span class="required">*</span>
                    </label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="input @error('category_id') is-invalid @enderror"
                        required>
                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Rak --}}
                <div class="book-form-group">
                    <label for="rak">
                        Rak <span class="required">*</span>
                    </label>
                    <select
                        id="rak"
                        name="rak"
                        class="input @error('rak') is-invalid @enderror"
                        required>
                        <option value="" disabled {{ old('rak') ? '' : 'selected' }}>-- Pilih Rak --</option>
                        @foreach($racks as $rack)
                            <option value="{{ $rack->code }}" {{ old('rak') == $rack->code ? 'selected' : '' }}>
                                {{ $rack->code }} – {{ $rack->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('rak')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Edisi --}}
                <div class="book-form-group">
                    <label for="edition">
                        Edisi    <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="edition"
                        name="edition"
                        class="input @error('edition') is-invalid @enderror"
                        placeholder="cth. Edisi 1"
                        value="{{ old('edition') }}"
                        required>
                    @error('edition')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ISBN --}}
                <div class="book-form-group">
                    <label for="isbn">
                        ISBN <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        class="input @error('isbn') is-invalid @enderror"
                        placeholder="cth. 978-602-03-8591-2"
                        value="{{ old('isbn') }}"
                        required>
                    @error('isbn')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No. Panggil --}}
                <div class="book-form-group">
                    <label for="call_number">
                        No. Panggil (Call Number) <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="call_number"
                        name="call_number"
                        class="input @error('call_number') is-invalid @enderror"
                        placeholder="cth. 813.01 HIR l"
                        value="{{ old('call_number') }}"
                        required>
                    @error('call_number')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>


                {{-- SECTION 2: PENERBITAN & STOK --}}
                <div class="form-section-heading">
                    <div class="form-section-icon">2</div>
                    <span class="form-section-title">Penerbitan & Persediaan</span>
                </div>

                {{-- Penerbit --}}
                <div class="book-form-group">
                    <label for="publisher">
                        Penerbit <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="publisher"
                        name="publisher"
                        class="input @error('publisher') is-invalid @enderror"
                        placeholder="cth. PT Gramedia Pustaka Utama"
                        value="{{ old('publisher') }}"
                        required>
                    @error('publisher')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tahun Terbit --}}
                <div class="book-form-group">
                    <label for="publication_year">
                        Tahun Terbit <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="publication_year"
                        name="publication_year"
                        class="input @error('publication_year') is-invalid @enderror"
                        placeholder="cth. 2024"
                        min="1900"
                        max="{{ date('Y') + 1 }}"
                        value="{{ old('publication_year', date('Y')) }}"
                        required>
                    @error('publication_year')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Jumlah Stok --}}
                <div class="book-form-group">
                    <label for="stock">
                        Jumlah Stok Buku <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        class="input @error('stock') is-invalid @enderror"
                        placeholder="cth. 10"
                        min="1"
                        value="{{ old('stock', 1) }}"
                        required>
                    @error('stock')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>


                {{-- SECTION 3: DESKRIPSI & COVER --}}
                <div class="form-section-heading">
                    <div class="form-section-icon">3</div>
                    <span class="form-section-title">Sinopsis & Sampul Buku</span>
                </div>

                {{-- Deskripsi / Sinopsis --}}
                <div class="book-form-group book-form-full">
                    <label for="description">
                        Sinopsis / Deskripsi Buku
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="input @error('description') is-invalid @enderror"
                        rows="4"
                        placeholder="Tuliskan ringkasan singkat atau poin penting mengenai isi buku ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Cover Buku dengan Upload Box & Live Preview --}}
                <div class="book-form-group book-form-full">
                    <label for="cover">
                        Cover / Sampul Buku
                    </label>

                    <div class="cover-upload-area">
                        <div class="cover-preview-box" id="coverPreviewContainer">
                            <div class="cover-placeholder-icon" id="coverPlaceholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                                <span>No Cover</span>
                            </div>
                            <img id="coverPreviewImage" src="#" alt="Preview Cover" style="display: none;">
                        </div>

                        <div class="cover-upload-details">
                            <input
                                type="file"
                                id="cover"
                                name="cover"
                                class="cover-file-input"
                                accept="image/jpeg,image/png,image/jpg,image/webp">
                            
                            <label for="cover" class="cover-upload-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" x2="12" y1="3" y2="15"/>
                                </svg>
                                <span>Pilih Berkas Gambar...</span>
                            </label>

                            <p class="book-cover-help">
                                Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Ukuran maksimum: <strong>2 MB</strong>.
                            </p>
                            @error('cover')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="book-form-actions">
                <a
                    href="{{ route('books.index') }}"
                    class="book-btn-cancel">
                    Batal
                </a>

                <button
                    type="submit"
                    class="book-btn-save">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Simpan Buku</span>
                </button>
            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const coverInput = document.getElementById('cover');
    const previewImage = document.getElementById('coverPreviewImage');
    const placeholder = document.getElementById('coverPlaceholder');

    if (coverInput && previewImage) {
        coverInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    previewImage.style.display = 'block';
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            }
        });
    }
});
</script>
@endpush

@endsection