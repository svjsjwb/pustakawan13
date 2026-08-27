@extends('layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<section class="page" id="page-settings">
    <div class="settings-grid">
        <div class="settings-nav" id="settings-nav">
            <button class="active" data-tab="general" onclick="setSettingsTab('general',this)">Umum</button>
            <button data-tab="policy" onclick="setSettingsTab('policy',this)">Kebijakan Peminjaman</button>
            <button data-tab="notif" onclick="setSettingsTab('notif',this)">Notifikasi</button>
            <button data-tab="users" onclick="setSettingsTab('users',this)">Pengguna &amp; Peran</button>
        </div>
        <div class="card card-pad" id="settings-body"></div>
    </div>
</section>
@endsection