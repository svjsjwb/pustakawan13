@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush

@section('content')

<div class="member-form-page">
<div class="page-header">
    <div>
        <h1>Tambah Anggota</h1>
        <p>Tambahkan data Anggota perpustakaan.</p>
    </div>

    <a href="{{ route('members.index') }}" class="btn-back">
        ← Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="member-form-card">

    <div class="form-header">
        <h2>Data Anggota</h2>
        <p>Isi informasi Anggota dengan lengkap.</p>
    </div>

    <form action="{{ route('members.store') }}" method="POST">

        @csrf

        @include('members.partials.create-fields')

        {{-- STATUS --}}
        {{-- Status tidak diinput manual.
             Status otomatis berdasarkan aktivitas peminjaman. --}}

        {{-- BUTTON --}}
        <div class="form-actions">

            <a
                href="{{ route('members.index') }}"
                class="btn-cancel"
            >
                Batal
            </a>

            <button
                type="submit"
                class="btn-save"
            >
                Simpan
            </button>

        </div>

    </form>

</div>
</div>

@endsection
