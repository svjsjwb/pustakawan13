@extends('layouts.app')

@section('title', 'Edit Buku')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush
@section('content')

<div class="card card-pad">

    <h2>Edit Buku</h2>

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Judul Buku</label>
            <input
                type="text"
                name="title"
                class="input"
                value="{{ old('title', $book->title) }}"
                required>
        </div>

        <div class="form-group">
            <label>Kategori</label>

            <select name="category_id" class="input" required>

                @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    {{ $book->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>

                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label>Pengarang</label>
            <input
                type="text"
                name="author"
                class="input"
                value="{{ old('author', $book->author) }}"
                required>
        </div>

        <div class="form-group">
            <label>Penerbit</label>
            <input
                type="text"
                name="publisher"
                class="input"
                value="{{ old('publisher', $book->publisher) }}"
                required>
        </div>

        <div class="form-group">
            <label>Tahun Terbit</label>
            <input
                type="number"
                name="publication_year"
                class="input"
                value="{{ old('publication_year', $book->publication_year) }}"
                required>
        </div>

        <div class="form-group">
            <label>ISBN</label>
            <input
                type="text"
                name="isbn"
                class="input"
                value="{{ old('isbn', $book->isbn) }}"
                required>
        </div>

        <div class="form-group">
            <label>No. Panggil</label>
            <input
                type="text"
                name="call_number"
                class="input"
                value="{{ old('call_number', $book->call_number) }}"
                required>
        </div>

        <div class="form-group">
            <label>Jumlah Stok</label>
            <input
                type="number"
                name="stock"
                class="input"
                min="1"
                value="{{ old('stock', $book->stock) }}"
                required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea
                name="description"
                class="input"
                rows="4">{{ old('description', $book->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Cover Buku</label>
            <input
                type="file"
                name="cover"
                accept="image/*">
        </div>

        @if($book->cover)
        <div class="form-group">
            <label>Cover Saat Ini</label>
            <div>
                <img
                    src="{{ asset('storage/' . $book->cover) }}"
                    alt="Cover {{ $book->title }}"
                    style="max-width:120px;">
            </div>
        </div>
        @endif

        <br>

        <button type="submit" class="btn-add-top">
            Simpan Perubahan
        </button>

        <a href="{{ route('books.index') }}">
            Batal
        </a>

    </form>

</div>

@endsection