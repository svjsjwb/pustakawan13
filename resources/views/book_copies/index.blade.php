@extends('layouts.app')

@section('title', 'Eksemplar Buku')

@section('content')

<div class="page-header">

    <div>
        <h1>Eksemplar Buku</h1>

        <p>
            {{ $book->title }}
        </p>
    </div>

    <a
        href="{{ route('books.copies.create', $book) }}"
        class="btn btn-primary">
        + Tambah Eksemplar
    </a>

</div>


@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif


<div class="table-container">

    <table>

        <thead>

            <tr>
                <th>#</th>
                <th>Barcode</th>
                <th>Status</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($copies as $copy)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $copy->barcode ?? '-' }}
                </td>

                <td>

                    @php
                    $statusLabel = [
                    'available' => 'Tersedia',
                    'reserved' => 'Direservasi',
                    'borrowed' => 'Dipinjam',
                    'lost' => 'Hilang',
                    'damaged' => 'Rusak',
                    'maintenance' => 'Maintenance',
                    ];
                    @endphp

                    {{ $statusLabel[$copy->status] ?? $copy->status }}

                </td>

                <td>

                    @if($copy->shelf)

                    {{ $copy->shelf->zone->floor->name }}
                    /
                    {{ $copy->shelf->zone->name }}
                    /
                    {{ $copy->shelf->code }}
                    /
                    Baris {{ $copy->row }}
                    /
                    Kolom {{ $copy->column }}

                    @else

                    <span>
                        Lokasi belum diatur
                    </span>

                    @endif

                </td>

                <td>

                    <a
                        href="{{ route(
                                'books.copies.edit',
                                [$book, $copy]
                            ) }}">
                        Edit
                    </a>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5">
                    Belum ada eksemplar.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection