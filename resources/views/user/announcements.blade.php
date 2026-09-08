@extends('layouts.user')
@section('title', 'Pengumuman – Perpustakaan Tiga Serangkai')
@section('page-title', 'Pengumuman')

@push('styles')
<style>
.uann-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
.uann-card { background:#fff; border:1px solid var(--line); border-radius:16px; padding:22px 24px; box-shadow:var(--shadow); display:grid; grid-template-columns:1fr auto; gap:16px; align-items:start; transition:box-shadow .2s; }
.uann-card:hover { box-shadow:var(--shadow-lg); }
.uann-card.pinned { border-left:4px solid var(--primary); }
.uann-card.pinned { background:linear-gradient(135deg,rgba(40,120,121,.02) 0%,#fff 100%); }
.uann-type-badge { font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; margin-bottom:8px; display:inline-block; }
.uann-type-info     { background:#e0f0f0; color:var(--primary); }
.uann-type-event    { background:#ede9fe; color:#6d28d9; }
.uann-type-update   { background:var(--success-soft); color:var(--success); }
.uann-type-reminder { background:var(--brass-soft); color:#7a5420; }
.uann-title { margin:0 0 10px; font-size:16px; font-weight:700; color:var(--ink); }
.uann-content { margin:0 0 14px; font-size:13.5px; color:var(--ink-soft); line-height:1.65; }
.uann-footer { display:flex; align-items:center; gap:12px; font-size:11.5px; color:var(--muted); }
.uann-pin { display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600; color:var(--primary); }
.uann-date-col { text-align:right; font-size:12px; color:var(--muted); white-space:nowrap; line-height:1.4; }
</style>
@endpush

@section('content')

<div class="ul-page-header">
    <div>
        <p class="ul-page-kicker">INFORMASI PERUSAHAAN</p>
        <h1 class="ul-page-h1">📢 Pengumuman</h1>
    </div>
</div>

{{-- Pinned --}}
@php $pinned = $announcements->filter(fn($a) => $a['pinned']); @endphp
@if($pinned->isNotEmpty())
<div style="margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--muted-soft);margin-bottom:12px">📌 DISEMATKAN</p>
    <div class="uann-grid">
        @foreach($pinned as $ann)
        <div class="uann-card pinned">
            <div>
                <span class="uann-type-badge uann-type-{{ $ann['type'] }}">{{ strtoupper($ann['category']) }}</span>
                <h2 class="uann-title">{{ $ann['title'] }}</h2>
                <p class="uann-content">{{ $ann['content'] }}</p>
                <div class="uann-footer">
                    <span>Oleh: {{ $ann['author'] }}</span>
                    <span>·</span>
                    <div class="uann-pin">📌 Disematkan</div>
                </div>
            </div>
            <div class="uann-date-col">
                <strong>{{ \Carbon\Carbon::parse($ann['date'])->format('d') }}</strong><br>
                {{ \Carbon\Carbon::parse($ann['date'])->format('M Y') }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Other announcements --}}
@php $others = $announcements->filter(fn($a) => !$a['pinned']); @endphp
@if($others->isNotEmpty())
<div>
    <p style="font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--muted-soft);margin-bottom:12px">📋 PENGUMUMAN LAINNYA</p>
    <div class="uann-grid">
        @foreach($others as $ann)
        <div class="uann-card">
            <div>
                <span class="uann-type-badge uann-type-{{ $ann['type'] }}">{{ strtoupper($ann['category']) }}</span>
                <h2 class="uann-title">{{ $ann['title'] }}</h2>
                <p class="uann-content">{{ $ann['content'] }}</p>
                <div class="uann-footer">
                    <span>Oleh: {{ $ann['author'] }}</span>
                </div>
            </div>
            <div class="uann-date-col">
                <strong>{{ \Carbon\Carbon::parse($ann['date'])->format('d') }}</strong><br>
                {{ \Carbon\Carbon::parse($ann['date'])->format('M Y') }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
