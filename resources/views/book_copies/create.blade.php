@extends('layouts.app')

@section('title', 'Tambah Eksemplar')

@section('content')

<div class="page-header">

    <div>

        <h1>Tambah Eksemplar</h1>

        <p>
            {{ $book->title }}
        </p>

    </div>

</div>


<form
    action="{{ route('books.copies.store', $book) }}"
    method="POST">

    @csrf

    @include('book_copies._form')

    <button type="submit">
        Simpan Eksemplar
    </button>

    <a href="{{ route('books.copies.index', $book) }}">
        Batal
    </a>

</form>

@endsection