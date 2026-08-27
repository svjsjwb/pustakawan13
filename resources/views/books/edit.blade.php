@extends('layouts.app')

@section('title', 'Edit Buku - ' . $book->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')

<div class="book-form-page">

    {{-- Breadcrumb --}}
    <nav class="book-breadcrumb">
        <a href="{{ route('books.index') }}">Manajemen Buku</a>
        <span class="separator">/</span>
        <span class="current">Edit Buku</span>
    </nav>

    <div class="book-form-card">

        {{-- Header Form --}}
        <div class="book-form-header">
            <h2>Edit Data Buku</h2>
            <p class="book-form-subtitle">
                Perbarui rincian informasi, ketersediaan stok, atau sampul untuk buku <strong>{{ $book->title }}</strong>.
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
            action="{{ route('books.update', $book) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

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
                        value="{{ old('title', $book->title) }}"
                        required>
                    @error('title')
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
                        value="{{ old('author', $book->author) }}"
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
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
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
                        value="{{ old('isbn', $book->isbn) }}"
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
                        value="{{ old('call_number', $book->call_number) }}"
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
                        value="{{ old('publisher', $book->publisher) }}"
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
                        min="1900"
                        max="{{ date('Y') + 1 }}"
                        value="{{ old('publication_year', $book->publication_year) }}"
                        required>
                    @error('publication_year')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Jumlah Stok --}}
                <div class="book-form-group">
                    <label for="stock">
                        Jumlah Total Stok Buku <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        class="input @error('stock') is-invalid @enderror"
                        min="1"
                        value="{{ old('stock', $book->stock) }}"
                        required>
                    @error('stock')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No. Inventaris --}}
                <div class="book-form-group">
                    <label for="no_iventaris">
                        No. Inventaris
                    </label>
                    <input
                        type="text"
                        id="no_iventaris"
                        name="no_iventaris"
                        class="input @error('no_iventaris') is-invalid @enderror"
                        value="{{ old('no_iventaris', $book->no_iventaris) }}"
                        placeholder="cth. INV-2024-001">
                    @error('no_iventaris')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kode Buku --}}
                <div class="book-form-group">
                    <label for="kode_buku">
                        Kode Buku
                    </label>
                    <input
                        type="text"
                        id="kode_buku"
                        name="kode_buku"
                        class="input @error('kode_buku') is-invalid @enderror"
                        value="{{ old('kode_buku', $book->kode_buku) }}"
                        placeholder="cth. BK-001">
                    @error('kode_buku')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- DDC --}}
                <div class="book-form-group">
                    <label for="ddc">
                        Kode DDC
                    </label>
                    <input
                        type="text"
                        id="ddc"
                        name="ddc"
                        class="input @error('ddc') is-invalid @enderror"
                        value="{{ old('ddc', $book->ddc) }}"
                        placeholder="cth. 005.13">
                    @error('ddc')
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
                        <option value="" disabled {{ old('rak', $book->rak) ? '' : 'selected' }}>-- Pilih Rak --</option>
                        @foreach($racks as $rack)
                            <option value="{{ $rack->code }}" {{ old('rak', $book->rak) == $rack->code ? 'selected' : '' }}>
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
                        Edisi
                    </label>
                    <input
                        type="text"
                        id="edition"
                        name="edition"
                        class="input @error('edition') is-invalid @enderror"
                        value="{{ old('edition', $book->edition) }}"
                        placeholder="cth. Edisi ke-3">
                    @error('edition')
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
                        rows="4">{{ old('description', $book->description) }}</textarea>
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
                            @if($book->cover)
                                <img id="coverPreviewImage" src="{{ asset('storage/' . $book->cover) }}" alt="Cover {{ $book->title }}">
                                <div class="cover-placeholder-icon" id="coverPlaceholder" style="display: none;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                    <span>No Cover</span>
                                </div>
                            @else
                                <div class="cover-placeholder-icon" id="coverPlaceholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                    <span>No Cover</span>
                                </div>
                                <img id="coverPreviewImage" src="#" alt="Preview Cover" style="display: none;">
                            @endif
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
                                <span>{{ $book->cover ? 'Ganti Berkas Gambar...' : 'Pilih Berkas Gambar...' }}</span>
                            </label>

                            <p class="book-cover-help">
                                Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong>. Ukuran maksimum: <strong>2 MB</strong>. Kosongkan jika tidak ingin mengubah cover.
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
                    <span>Simpan Perubahan</span>
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
            }
        });
    }
});
</script>
@endpush

@endsection