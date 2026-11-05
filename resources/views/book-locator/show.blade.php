
@extends('layouts.app')

@section('title', 'Lokasi Buku')

@section('content')

@php
    $bookTitle = $reservation->book->title
        ?? $reservation->book->judul_buku
        ?? 'Buku';

    $hasLocation = $targetShelf && $targetZone && $targetFloor;
@endphp

<section class="page" style="max-width: 900px; margin: 0 auto; padding: 28px 18px;">

    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 1.7rem; font-weight: 700;">
            Lokasi Buku
        </h1>

        <p style="color: #64748b;">
            Informasi alamat fisik buku yang kamu reservasi.
        </p>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px;">

        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px;">
            {{ $bookTitle }}
        </h2>

        <div style="margin-bottom: 12px;">
            <span style="color: #64748b;">Barcode</span>
            <div>{{ $targetCopy->barcode ?? '-' }}</div>
        </div>

        <div style="margin-bottom: 12px;">
            <span style="color: #64748b;">Status</span>
            <div>{{ ucfirst($targetCopy->status ?? '-') }}</div>
        </div>

        <hr style="margin: 20px 0; border-color: #e2e8f0;">

        @if($hasLocation)

            <h3 style="font-weight: 700; margin-bottom: 15px;">
                Alamat Rak Buku
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px;">

                <div>
                    <span style="color: #64748b;">Lantai</span>
                    <div>{{ $targetFloor->name ?? '-' }}</div>
                </div>

                <div>
                    <span style="color: #64748b;">Zona</span>
                    <div>{{ $targetZone->name ?? '-' }}</div>
                </div>

                <div>
                    <span style="color: #64748b;">Kode Rak</span>
                    <div>{{ $targetShelf->code ?? '-' }}</div>
                </div>

                <div>
                    <span style="color: #64748b;">Baris</span>
                    <div>{{ $targetCopy->row ?? '-' }}</div>
                </div>

                <div>
                    <span style="color: #64748b;">Kolom</span>
                    <div>{{ $targetCopy->column ?? '-' }}</div>
                </div>

            </div>

        @else

            <div style="background: #fff7ed; padding: 15px; border-radius: 8px; color: #9a3412;">
                <strong>Lokasi belum terdata</strong>
                <p>
                    Data buku tetap tersimpan, tetapi alamat rak belum tersedia.
                    Silakan hubungi petugas perpustakaan.
                </p>
            </div>

        @endif

    </div>

</section>

@endsection
