@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')

<div class="page-header">
    <h2>Tambah Kategori</h2>
</div>

<div class="card">

    <form
        action="{{ route('categories.store') }}"
        method="POST"
        class="category-form"
    >
        @csrf

        <div class="field">
            <label>Kategori Utama</label>

            <select id="category_level_1">
                <option value="">Pilih Kategori</option>
                <option value="Pendidikan">Pendidikan</option>
                <option value="Anak-anak">Anak-anak</option>
                <option value="Remaja">Remaja</option>
                <option value="Dewasa">Dewasa</option>
            </select>
        </div>

        <div class="field">
            <label>Subkategori</label>

            <select id="category_level_2">
                <option value="">Pilih Subkategori</option>
            </select>
        </div>

        <div class="field">
            <label>Detail</label>

            <select
                id="category_level_3"
                name="name"
            >
                <option value="">Pilih Detail</option>
            </select>
        </div>

        <div class="field">
            <label>Deskripsi</label>

            <textarea
                name="description"
                rows="4"
            ></textarea>
        </div>

        <button
            type="submit"
            class="btn-save"
        >
            Simpan Kategori
        </button>

    </form>

</div>

@endsection