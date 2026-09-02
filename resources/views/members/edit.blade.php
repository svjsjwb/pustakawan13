@extends('layouts.app')

@section('title', 'Edit Karyawan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush

@section('content')

<div class="member-form-page">

    <div class="page-header">
        <div>
            <h1>Edit Anggota</h1>
            <p>Ubah data Anggota perpustakaan.</p>
        </div>

        <a href="{{ route('members.index') }}" class="btn-back">
            ← Kembali
        </a>
    </div>


    <div class="member-form-card">

        <h2>Data Anggota</h2>
        <p>Perbarui informasi Anggota dengan lengkap.</p>


        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form
            action="{{ route('members.update', $member->id) }}"
            method="POST"
        >
            @csrf
            @method('PUT')


            {{-- NAMA --}}
            <div class="form-group">
                <label for="name">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $member->name) }}"
                    placeholder="Masukkan nama Anggota"
                    required
                >
            </div>


            {{-- DIVISI --}}
            <div class="form-group">
                <label for="division">
                    Divisi
                </label>

                <select
                    name="division"
                    id="division"
                    required
                >
                    <option value="">
                        -- Pilih Divisi --
                    </option>

                    <option
                        value="CEO"
                        {{ old('division', $member->division) == 'CEO' ? 'selected' : '' }}
                    >
                        CEO
                    </option>

                    <option
                        value="COO"
                        {{ old('division', $member->division) == 'COO' ? 'selected' : '' }}
                    >
                        COO
                    </option>

                    <option
                        value="CFO"
                        {{ old('division', $member->division) == 'CFO' ? 'selected' : '' }}
                    >
                        CFO
                    </option>

                    <option
                        value="FINANCE DIRECTOR"
                        {{ old('division', $member->division) == 'FINANCE DIRECTOR' ? 'selected' : '' }}
                    >
                        FINANCE DIRECTOR
                    </option>

                    <option
                        value="HROD DIRECTOR"
                        {{ old('division', $member->division) == 'HROD DIRECTOR' ? 'selected' : '' }}
                    >
                        HROD DIRECTOR
                    </option>

                    <option
                        value="PDC"
                        {{ old('division', $member->division) == 'PDC' ? 'selected' : '' }}
                    >
                        PDC
                    </option>
                </select>
            </div>


            {{-- NO TELEPON --}}
            <div class="form-group">
                <label for="phone">
                    No. Telepon
                </label>

                <input
                    type="text"
                    name="phone"
                    id="phone"
                    value="{{ old('phone', $member->phone) }}"
                    placeholder="Contoh: 081234567890"
                    required
                >
            </div>


            {{-- STATUS --}}
            <div class="form-group">
                <label for="status">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    required
                >
                    <option
                        value="Aktif"
                        {{ old('status', $member->status) == 'Aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="Tidak Aktif"
                        {{ old('status', $member->status) == 'Tidak Aktif' ? 'selected' : '' }}
                    >
                        Tidak Aktif
                    </option>
                </select>
            </div>


            {{-- TOMBOL --}}
<div class="form-footer">
    <div class="form-actions">
        <a href="{{ route('members.index') }}" class="btn-cancel">
            Batal
        </a>

        <button type="submit" class="btn-submit">
            Simpan Perubahan
        </button>
    </div>
</div>

        </form>

    </div>

</div>

@endsection