@extends('layouts.app')

@section('title', 'Katalog Buku')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
@endpush
@section('content')

<section class="page" id="page-catalog">

    <div class="catalog-header">

        <div>
            <div class="catalog-count">
                {{ $books->total() }} buku terdaftar
            </div>

            <h1>Katalog Buku</h1>

            <p>
                Telusuri seluruh koleksi perpustakaan berdasarkan judul,
                kategori, atau ketersediaan.
            </p>
        </div>

    </div>


    <div class="filters">

        <input
            type="text"
            placeholder="Cari judul atau pengarang...">

        <select>
            <option>Semua Kategori</option>
        </select>

        <select>
            <option>Semua Status</option>
            <option>Tersedia</option>
            <option>Sedang Dipinjam</option>
        </select>

    </div>


    <div class="book-grid">

        @forelse($books as $book)

        <div class="book-card">

            <div class="book-cover">
                BOOK
            </div>

            <div class="book-info">

                <strong>
                    {{ $book->title }}
                </strong>

                <span>
                    {{ $book->author }}
                </span>

                <div class="book-meta">

                    <span>
                        {{ $book->category->name }}
                    </span>

                    @if($book->available_stock > 0)

                    <span class="book-status available">
                        Tersedia
                    </span>

                    @else

                    <span class="book-status borrowed">
                        Dipinjam
                    </span>

                    @endif

                </div>

            </div>

        </div>

        @empty

        <div class="catalog-empty">
            Belum ada buku.
        </div>

        @endforelse

    </div>


    {{-- Pagination --}}

    @if($books->hasPages())

    <div class="catalog-pagination">

        {{ $books->links() }}

    </div>

    @endif

</section>

@endsection