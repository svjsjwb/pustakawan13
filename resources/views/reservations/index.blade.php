@extends('layouts.app')

@section('title', 'Reservasi Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reservations.css') }}">
@endpush

@section('content')

<section class="page" id="page-reservations">

    {{-- =========================
         ALERT
    ========================= --}}

    @if(session('success'))
    <div class="reservation-alert success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="reservation-alert error">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="reservation-alert error">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="reservation-layout">

        {{-- =========================
             FORM RESERVASI
        ========================= --}}

        <div class="reservation-form-card">

            <h3>Reservasi Buku</h3>

            <p>
                Atur reservasi buku untuk anggota perpustakaan.
            </p>

            <form
                action="{{ route('reservations.store') }}"
                method="POST">

                @csrf


                {{-- =========================
                     ANGGOTA
                ========================= --}}

                <div class="reservation-field">

                    <label>Anggota</label>

                    <select name="member_id" required>

                        <option value="">
                            Pilih nama anggota...
                        </option>

                        @foreach($members as $member)

                        <option
                            value="{{ $member->id }}"
                            @selected(old('member_id')==$member->id)
                            >
                            {{ $member->name }}
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- =========================
                     BUKU
                ========================= --}}

                <div class="reservation-field">

                    <label>Buku</label>

                    <select name="book_id" required>

                        <option value="">
                            Pilih buku yang tersedia...
                        </option>

                        @foreach($books as $book)

                        <option
                            value="{{ $book->id }}"
                            @selected(old('book_id')==$book->id)
                            @disabled($book->available_stock <= 0)>
                                {{ $book->title }}
                                —
                                @if($book->available_stock > 0)
                                Stok {{ $book->available_stock }}
                                @else
                                STOK HABIS
                                @endif
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- =========================
                     TANGGAL
                ========================= --}}

                <div class="reservation-date-row">

                    <div class="reservation-field">

                        <label>Tanggal Reservasi</label>

                        <input
                            type="date"
                            name="reserved_at"
                            value="{{ old(
                                'reserved_at',
                                now()->format('Y-m-d')
                            ) }}"
                            required>

                    </div>


                    <div class="reservation-field">

                        <label>Berlaku Sampai</label>

                        <input
                            type="date"
                            name="expires_at"
                            value="{{ old(
                                'expires_at',
                                now()->addDays(3)->format('Y-m-d')
                            ) }}">

                    </div>

                </div>


                {{-- =========================
                     SUBMIT
                ========================= --}}

                <button
                    type="submit"
                    class="reservation-submit">
                    Proses Reservasi
                </button>

            </form>

        </div>


        {{-- =========================
             DAFTAR RESERVASI
        ========================= --}}

        <div class="reservation-list-card">

            <div class="reservation-list-top">

                <h3>Daftar Reservasi</h3>

                <input
                    type="text"
                    id="reservation-search"
                    placeholder="Cari anggota / buku...">

            </div>


            <div class="reservation-table-wrap">

                <table class="reservation-table">

                    <thead>

                        <tr>

                            <th>ANGGOTA</th>

                            <th>BUKU</th>

                            <th>RESERVASI</th>

                            <th>BERLAKU SAMPAI</th>

                            <th>STATUS</th>

                            <th>AKSI</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reservations as $reservation)

                        <tr>

                            {{-- =========================
                                 ANGGOTA
                            ========================= --}}

                            <td>
                                {{ $reservation->member->name }}
                            </td>


                            {{-- =========================
                                 BUKU
                            ========================= --}}

                            <td>
                                {{ $reservation->book->title }}
                            </td>


                            {{-- =========================
                                 TANGGAL RESERVASI
                            ========================= --}}

                            <td>
                                {{ $reservation->reserved_at->format('d/m/Y') }}
                            </td>


                            {{-- =========================
                                 BERLAKU SAMPAI
                            ========================= --}}

                            <td>

                                @if($reservation->expires_at)

                                {{ $reservation->expires_at->format('d/m/Y') }}

                                @else

                                —

                                @endif

                            </td>


                            {{-- =========================
                                 STATUS
                            ========================= --}}

                            <td>

                                @if($reservation->status === 'menunggu')

                                <span class="reservation-status waiting">
                                    Menunggu
                                </span>

                                @elseif($reservation->status === 'disetujui')

                                <span class="reservation-status approved">
                                    Disetujui
                                </span>

                                @elseif($reservation->status === 'ditolak')

                                <span class="reservation-status rejected">
                                    Ditolak
                                </span>

                                @elseif($reservation->status === 'dibatalkan')

                                <span class="reservation-status cancelled">
                                    Dibatalkan
                                </span>

                                @elseif($reservation->status === 'selesai')

                                <span class="reservation-status finished">
                                    Selesai
                                </span>

                                @endif

                            </td>


                            {{-- =========================
                                 AKSI
                            ========================= --}}

                            <td>

                                <div class="reservation-actions">


                                    {{-- =========================
                                         SETUJUI
                                    ========================= --}}

                                    @if($reservation->status === 'menunggu')

                                    <form
                                        action="{{ route(
                                                'reservations.approve',
                                                $reservation
                                            ) }}"
                                        method="POST">

                                        @csrf

                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="disetujui">

                                        <button
                                            type="submit"
                                            class="btn-approve">
                                            Setujui
                                        </button>

                                    </form>

                                    @endif


                                    {{-- =========================
                                         BOOK LOCATOR
                                    ========================= --}}

                                    @if(
                                    $reservation->book_copy_id &&
                                    $reservation->bookCopy?->shelf_id &&
                                    in_array(
                                    $reservation->status,
                                    [
                                    'menunggu',
                                    'disetujui'
                                    ]
                                    )
                                    )

                                    <a
                                        href="{{ route(
                                                'reservations.locator',
                                                $reservation
                                            ) }}"
                                        class="btn btn-primary">
                                        📍 Temukan Buku
                                    </a>

                                    @endif


                                    {{-- =========================
                                         TOLAK
                                    ========================= --}}

                                    @if($reservation->status === 'menunggu')

                                    <form
                                        action="{{ route(
                                                'reservations.reject',
                                                $reservation
                                            ) }}"
                                        method="POST">

                                        @csrf

                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="ditolak">

                                        <button
                                            type="submit"
                                            class="btn-reject">
                                            Tolak
                                        </button>

                                    </form>

                                    @endif


                                    {{-- =========================
                                         TIDAK ADA AKSI
                                    ========================= --}}

                                    @if(
                                    $reservation->status !== 'menunggu' &&
                                    !(
                                    $reservation->book_copy_id &&
                                    $reservation->bookCopy?->shelf_id &&
                                    in_array(
                                    $reservation->status,
                                    [
                                    'menunggu',
                                    'disetujui'
                                    ]
                                    )
                                    )
                                    )

                                    <span class="action-done">
                                        —
                                    </span>

                                    @endif


                                </div>

                            </td>

                        </tr>


                        @empty

                        <tr>

                            <td
                                colspan="6"
                                class="reservation-empty">
                                Belum ada reservasi.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>

@endsection