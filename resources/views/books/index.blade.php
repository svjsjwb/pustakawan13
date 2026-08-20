@extends('layouts.app')

@section('title', 'Manajemen Buku')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush
@section('content')

<div class="page-header">
  <div>
    <span class="page-label">Data koleksi buku</span>
    <h1>Manajemen Buku</h1>
    <p>Tambahkan, ubah, atau hapus data buku dalam koleksi perpustakaan.</p>
  </div>
</div>

<div class="book-toolbar">

  <div class="toolbar-left">
    <a href="{{ route('books.index') }}" class="btn btn-secondary">
      Daftar Buku
    </a>

    <a href="{{ route('books.create') }}" class="btn btn-primary">
      Tambah Buku Baru
    </a>
  </div>

  <div class="toolbar-right">
    <select id="books-filter-kategori">
      <option value="">Semua Kategori</option>

      @foreach($books->pluck('category')->unique('id')->filter() as $category)
      <option value="{{ $category->id }}">
        {{ $category->name }}
      </option>
      @endforeach
    </select>

    <span class="book-total">
      Total: {{ $books->count() }} Buku
    </span>
  </div>

</div>


<div class="table-wrapper">

  <table class="books-table">

    <thead>
      <tr>
        <th>Judul</th>
        <th>Penulis</th>
        <th>ISBN</th>
        <th>Kategori</th>
        <th>Stok</th>
        <th>Aksi</th>
      </tr>
    </thead>

    <tbody>

      @forelse($books as $book)

      <tr data-category="{{ $book->category_id }}">

        <td>
          <strong>{{ $book->title }}</strong>
        </td>

        <td>
          {{ $book->author }}
        </td>

        <td>
          {{ $book->isbn ?? '-' }}
        </td>

        <td>
          {{ $book->category->name ?? '-' }}
        </td>

        <td>
          {{ $book->available_stock ?? 0 }}
        </td>

        <td>

          <div class="book-actions">

            <a
              href="{{ route('books.copies.index', $book) }}"
              class="action-copy"
              title="Kelola eksemplar">
              📚
            </a>

            <a
              href="{{ route('books.edit', $book) }}"
              class="action-edit"
              title="Edit buku">
              ✎
            </a>

            <form
              action="{{ route('books.destroy', $book) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
              @csrf
              @method('DELETE')

              <button
                type="submit"
                class="action-delete"
                title="Hapus buku">
                🗑
              </button>
            </form>

          </div>

        </td>

      </tr>

      @empty

      <tr>
        <td colspan="6" class="empty-books">
          Belum ada data buku.
        </td>
      </tr>

      @endforelse

    </tbody>

  </table>

</div>


<script>
  document.addEventListener('DOMContentLoaded', function() {

    const filter = document.getElementById('books-filter-kategori');
    const rows = document.querySelectorAll('.books-table tbody tr[data-category]');

    filter.addEventListener('change', function() {

      const selectedCategory = this.value;

      rows.forEach(function(row) {

        if (
          selectedCategory === '' ||
          row.dataset.category === selectedCategory
        ) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }

      });

    });

  });
</script>

@endsection