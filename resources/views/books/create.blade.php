@extends('layouts.app')

@section('title', 'Tambah Buku')
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
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                {{-- Judul --}}
                <div class="form-group">
                    <label for="title">Judul</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="input"
                        placeholder="cth. Sejarah Bumi"
                        required>
                </div>

                {{-- Penulis --}}
                <div class="form-group">
                    <label for="author">Penulis</label>
                    <input
                        type="text"
                        id="author"
                        name="author"
                        class="input"
                        placeholder="cth. Anwar Bumi"
                        required>
                </div>

                {{-- ISBN --}}
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        class="input"
                        placeholder="cth. 978-000-000-000-0"
                        required>
                </div>

                {{-- Kategori --}}
                <div class="form-group">
                    <label for="category_id">Kategori</label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="input"
                        required>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jumlah Stok --}}
                <div class="form-group">
                    <label for="stock">Jumlah Stok</label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        class="input"
                        placeholder="cth. 9"
                        min="1"
                        required>
                </div>

                {{-- Tahun Terbit --}}
                <div class="form-group">
                    <label for="publication_year">Tahun Terbit</label>
                    <input
                        type="number"
                        id="publication_year"
                        name="publication_year"
                        class="input"
                        placeholder="cth. 2026"
                        required>
                </div>

                {{-- Penerbit --}}
                <div class="form-group">
                    <label for="publisher">Penerbit</label>
                    <input
                        type="text"
                        id="publisher"
                        name="publisher"
                        class="input"
                        placeholder="cth. Gramedia Pustaka Utama"
                        required>
                </div>

                {{-- No. Panggil --}}
                <div class="form-group">
                    <label for="call_number">No. Panggil</label>
                    <input
                        type="text"
                        id="call_number"
                        name="call_number"
                        class="input"
                        placeholder="cth. 813.01"
                        required>
                </div>

                {{-- Deskripsi --}}
                <div class="form-group form-full">
                    <label for="description">Deskripsi</label>

                    <textarea
                        id="description"
                        name="description"
                        class="input"
                        rows="4"
                        placeholder="ringkasan singkat mengenai isi buku.."></textarea>
                </div>

                {{-- Cover --}}
                <div class="form-group form-full">
                    <label for="cover">Cover Buku</label>

                    <input
                        type="file"
                        id="cover"
                        name="cover">
                </div>

            </div>

            <div class="form-actions">

                <a
                    href="{{ route('books.index') }}"
                    class="btn-cancel">
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn-save">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection