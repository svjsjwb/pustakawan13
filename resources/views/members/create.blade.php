@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1>Tambah Karyawan</h1>
        <p>Tambahkan data karyawan perpustakaan.</p>
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
        <h2>Data Karyawan</h2>
        <p>Isi informasi karyawan dengan lengkap.</p>
    </div>

    <form action="{{ route('members.store') }}" method="POST">

        @csrf

        {{-- NAMA --}}
        <div class="form-group">
            <label for="name">Nama</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Masukkan nama karyawan"
                required
            >
        </div>


        {{-- DIVISI --}}
        {{-- DIVISI --}}
<div class="form-group">
    <label for="division">Divisi</label>

    <select
        id="division"
        name="division"
        required
    >
        <option value="">-- Pilih Divisi --</option>

        <option value="CEO"
            {{ old('division') == 'CEO' ? 'selected' : '' }}>
            CEO
        </option>

        <option value="COO"
            {{ old('division') == 'COO' ? 'selected' : '' }}>
            COO
        </option>

        <option value="CFO"
            {{ old('division') == 'CFO' ? 'selected' : '' }}>
            CFO
        </option>

        <option value="FINANCE DIRECTOR"
            {{ old('division') == 'FINANCE DIRECTOR' ? 'selected' : '' }}>
            FINANCE DIRECTOR
        </option>

        <option value="HROD DIRECTOR"
            {{ old('division') == 'HROD DIRECTOR' ? 'selected' : '' }}>
            HROD DIRECTOR
        </option>

        <option value="PDC"
            {{ old('division') == 'PDC' ? 'selected' : '' }}>
            PDC
        </option>

    </select>
</div>


        {{-- NO TELEPON --}}
        <div class="form-group">
            <label for="phone">No. Telepon</label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Contoh: 081234567890"
                required
            >
        </div>


        {{-- STATUS --}}
        <div class="form-group">
            <label for="status">Status</label>

            <select
                id="status"
                name="status"
                required
            >
                <option value="Aktif"
                    {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="Nonaktif"
                    {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>
        </div>


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
                Simpan Karyawan
            </button>

        </div>

    </form>

</div>

@endsection