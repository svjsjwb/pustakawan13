@extends('layouts.app')

@section('title', 'Edit Eksemplar')

@section('content')

<div class="page-header">

    <div>

        <h1>Edit Eksemplar</h1>

        <p>
            {{ $book->title }}
        </p>

    </div>

</div>


<form
    action="{{ route(
        'books.copies.update',
        [$book, $copy]
    ) }}"
    method="POST">

    @csrf

    @method('PUT')

    @include('book_copies._form')

    <button type="submit">
        Simpan Perubahan
    </button>

    <a href="{{ route('books.copies.index', $book) }}">
        Batal
    </a>

</form>

@endsection