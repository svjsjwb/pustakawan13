@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books.css') }}">
@endpush

@section('content')
    @include('books.create', ['isBookModal' => false])
@endsection
