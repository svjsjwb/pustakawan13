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

                @if ($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('members.update', $member->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- NAMA --}}
                    <div class="form-group">
                        <label for="name">
                            Nama
                        </label>

                        <input type="text" name="name" id="name" value="{{ old('name', $member->name) }}"
                            placeholder="Masukkan nama Anggota" required>
                    </div>

                    {{-- EMAIL --}}
                    <div class="form-group">
                        <label for="email">
                            Email
                        </label>

                        <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}"
                            placeholder="Contoh: nama@email.com">
                    </div>

                    {{-- DIVISI --}}
                    <div class="form-group">
                        <label for="division">
                            Divisi
                        </label>

                        <select name="division" id="division" required>
                            <option value="">
                                -- Pilih Divisi --
                            </option>

                            <option value="Center Of Excellence"
                                {{ old('division', $member->division) == 'Center Of Excellence' ? 'selected' : '' }}>
                                Center Of Excellence
                            </option>

                            <option value="Digital Business"
                                {{ old('division', $member->division) == 'Digital Business' ? 'selected' : '' }}>
                                Digital Business
                            </option>

                            <option value="E-Publishing"
                                {{ old('division', $member->division) == 'E-Publishing' ? 'selected' : '' }}>
                                E-Publishing
                            </option>

                            <option value="Finance" {{ old('division', $member->division) == 'Finance' ? 'selected' : '' }}>
                                Finance
                            </option>

                            <option value="General Trading"
                                {{ old('division', $member->division) == 'General Trading' ? 'selected' : '' }}>
                                General Trading
                            </option>

                            <option value="HR & GA"
                                {{ old('division', $member->division) == 'HR & GA' ? 'selected' : '' }}>
                                HR & GA
                            </option>

                            <option value="HSE" {{ old('division', $member->division) == 'HSE' ? 'selected' : '' }}>
                                HSE
                            </option>

                            <option value="IQA" {{ old('division', $member->division) == 'IQA' ? 'selected' : '' }}>
                                IQA
                            </option>

                            <option value="IT" {{ old('division', $member->division) == 'IT' ? 'selected' : '' }}>
                                IT
                            </option>

                            <option value="Marketing"
                                {{ old('division', $member->division) == 'Marketing' ? 'selected' : '' }}>
                                Marketing
                            </option>

                            <option value="MTIS Perpuskita dan Tisera"
                                {{ old('division', $member->division) == 'MTIS Perpuskita dan Tisera' ? 'selected' : '' }}>
                                MTIS Perpuskita dan Tisera
                            </option>

                            <option value="MTIS Planning and Development"
                                {{ old('division', $member->division) == 'MTIS Planning and Development' ? 'selected' : '' }}>
                                MTIS Planning and Development
                            </option>

                            <option value="People Development Center"
                                {{ old('division', $member->division) == 'People Development Center' ? 'selected' : '' }}>
                                People Development Center
                            </option>

                            <option value="Production"
                                {{ old('division', $member->division) == 'Production' ? 'selected' : '' }}>
                                Production
                            </option>

                            <option value="School Book Sales"
                                {{ old('division', $member->division) == 'School Book Sales' ? 'selected' : '' }}>
                                School Book Sales
                            </option>

                            <option value="School Book Publishing"
                                {{ old('division', $member->division) == 'School Book Publishing' ? 'selected' : '' }}>
                                School Book Publishing
                            </option>

                            <option value="SCM" {{ old('division', $member->division) == 'SCM' ? 'selected' : '' }}>
                                SCM
                            </option>

                            <option value="TAX" {{ old('division', $member->division) == 'TAX' ? 'selected' : '' }}>
                                TAX
                            </option>
                        </select>
                    </div>

                    {{-- NO TELEPON --}}
                    <div class="form-group">
                        <label for="phone">
                            No. Telepon
                        </label>

                        <input type="text" name="phone" id="phone" value="{{ old('phone', $member->phone) }}"
                            placeholder="Contoh: 081234567890">
                    </div>

                    {{-- STATUS --}}
                    {{-- Status tidak dapat diedit secara manual.
                    Status dikelola otomatis berdasarkan aktivitas peminjaman. --}}

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
