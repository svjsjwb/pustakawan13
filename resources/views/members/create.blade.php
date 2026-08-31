@extends('layouts.app')

@section('title', 'Tambah Anggota')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/members.css') }}">
@endpush

@section('content')

<div class="member-page">

    <div class="page-header">

        <div>
            <h2>Tambah Anggota</h2>
            <p>Tambahkan data anggota baru ke perpustakaan.</p>
        </div>

        <a href="{{ route('members.index') }}" class="btn-secondary">
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

        <div class="form-title">

            <div class="form-icon">
                👤
            </div>

            <div>
                <h3>Data Anggota</h3>
                <p>Isi informasi anggota dengan lengkap.</p>
            </div>

        </div>


        <form
            action="{{ route('members.store') }}"
            method="POST"
        >

            @csrf


            <div class="form-grid">

                {{-- NO ANGGOTA --}}
                <div class="form-group">

                    <label for="member_number">
                        No. Anggota <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="member_number"
                        name="member_number"
                        value="{{ old('member_number') }}"
                        placeholder="Contoh: AGT-001"
                        required
                    >

                </div>


                {{-- NAMA --}}
                <div class="form-group">

                    <label for="name">
                        Nama Lengkap <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>


                {{-- NIS/NIP --}}
                <div class="form-group">

                    <label for="nis_nip">
                        NIS / NIP
                    </label>

                    <input
                        type="text"
                        id="nis_nip"
                        name="nis_nip"
                        value="{{ old('nis_nip') }}"
                        placeholder="Masukkan NIS / NIP"
                    >

                </div>


                {{-- JENIS KELAMIN --}}
                <div class="form-group">

                    <label for="gender">
                        Jenis Kelamin
                    </label>

                    <select
                        name="gender"
                        id="gender"
                    >

                        <option value="">
                            -- Pilih Jenis Kelamin --
                        </option>

                        <option
                            value="Laki-laki"
                            @selected(old('gender') === 'Laki-laki')
                        >
                            Laki-laki
                        </option>

                        <option
                            value="Perempuan"
                            @selected(old('gender') === 'Perempuan')
                        >
                            Perempuan
                        </option>

                    </select>

                </div>


                {{-- KELAS --}}
                <div class="form-group">

                    <label for="class">
                        Kelas
                    </label>

                    <input
                        type="text"
                        id="class"
                        name="class"
                        value="{{ old('class') }}"
                        placeholder="Contoh: XII RPL 1"
                    >

                </div>


                {{-- TELEPON --}}
                <div class="form-group">

                    <label for="phone">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Contoh: 081234567890"
                    >

                </div>


                {{-- EMAIL --}}
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="contoh@email.com"
                    >

                </div>


                {{-- TANGGAL DAFTAR --}}
                <div class="form-group">

                    <label for="registered_at">
                        Tanggal Daftar <span>*</span>
                    </label>

                    <input
                        type="date"
                        id="registered_at"
                        name="registered_at"
                        value="{{ old('registered_at', now()->format('Y-m-d')) }}"
                        required
                    >

                </div>


                {{-- ALAMAT --}}
                <div class="form-group full">

                    <label for="address">
                        Alamat
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                        placeholder="Masukkan alamat lengkap"
                    >{{ old('address') }}</textarea>

                </div>


                {{-- STATUS --}}
                <div class="form-group">

                    <label for="status">
                        Status <span>*</span>
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option value="Aktif">
                            Aktif
                        </option>

                        <option value="Tidak Aktif">
                            Tidak Aktif
                        </option>

                    </select>

                </div>

            </div>


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
                    + Simpan Anggota
                </button>

            </div>

        </form>

    </div>

</div>

@endsection