@extends('layouts.app')

@section('title', 'Sirkulasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/circulation.css') }}">
@endpush

@section('content')

<section class="page" id="page-circulation">

  {{-- SUCCESS MESSAGE --}}
  @if(session('success'))
  <div class="alert-success">
    {{ session('success') }}
  </div>
  @endif

  {{-- ERROR MESSAGE --}}
  @if(session('error'))
  <div class="alert-error">
    {{ session('error') }}
  </div>
  @endif

  {{-- VALIDATION ERROR --}}
  @if($errors->any())
  <div class="alert-error">
    <ul class="error-list">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif


  <div class="two-col">

    {{-- =========================================
             FORM PEMINJAMAN
        ========================================== --}}

    <div class="card card-pad">

      <h3>
        Pinjamkan Buku
      </h3>

      <p class="loan-description">
        Pilih anggota dan buku untuk memproses peminjaman baru.
      </p>


      <form
        action="{{ route('circulation.store') }}"
        method="POST">

        @csrf


        {{-- ANGGOTA --}}
        <div class="field">

          <label>Anggota</label>

          <select name="member_id" required>

            <option value="">
              Pilih Anggota
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


        {{-- BUKU --}}
        <div class="field">

          <label>Buku</label>

          <select name="book_id" required>

            <option value="">
              Pilih Buku
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


        {{-- TANGGAL --}}
        <div class="field-row">

          <div class="field">

            <label>Tanggal Pinjam</label>

            <input
              type="date"
              name="borrowed_at"
              value="{{ old('borrowed_at', now()->format('Y-m-d')) }}"
              required>

          </div>


          <div class="field">

            <label>Jatuh Tempo</label>

            <input
              type="date"
              name="due_at"
              value="{{ old('due_at', now()->addDays(7)->format('Y-m-d')) }}"
              required>

          </div>

        </div>


        <button
          type="submit"
          class="btn btn-primary loan-submit">

          Proses Peminjaman

        </button>

      </form>

    </div>


    {{-- =========================================
             DAFTAR PEMINJAMAN
        ========================================== --}}

    <div>

      <div class="card">

        <div class="table-wrap">

          <table>

            <thead>

              <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>

            </thead>


            <tbody>

              @forelse($borrowings as $borrowing)

              @php

              $isLate =
              $borrowing->status === 'dipinjam'
              && now()->startOfDay()->gt($borrowing->due_at);

              $displayStatus = $borrowing->status;

              if ($isLate) {
              $displayStatus = 'terlambat';
              }

              @endphp


              <tr>

                {{-- ANGGOTA --}}
                <td>
                  {{ $borrowing->member->name }}
                </td>


                {{-- BUKU --}}
                <td>

                  @foreach($borrowing->details as $detail)

                  {{ $detail->book->title }}

                  @if(!$loop->last)
                  <br>
                  @endif

                  @endforeach

                </td>


                {{-- TANGGAL PINJAM --}}
                <td>
                  {{ $borrowing->borrowed_at->format('d/m/Y') }}
                </td>


                {{-- JATUH TEMPO --}}
                <td>
                  {{ $borrowing->due_at->format('d/m/Y') }}
                </td>


                {{-- STATUS --}}
                <td>

                  @if($displayStatus === 'dipinjam')

                  <span class="status-badge aktif">
                    Aktif
                  </span>

                  @elseif($displayStatus === 'terlambat')

                  <span class="status-badge terlambat">
                    Terlambat
                  </span>

                  @else

                  <span class="status-badge kembali">
                    Selesai
                  </span>

                  @endif

                </td>


                {{-- AKSI --}}
                <td>

                  @if($borrowing->status !== 'dikembalikan')

                  <form
                    action="{{ route('circulation.return', $borrowing) }}"
                    method="POST"
                    class="return-form">

                    @csrf
                    @method('PATCH')

                    <button
                      type="submit"
                      class="btn btn-secondary">

                      Kembalikan

                    </button>

                  </form>

                  @else

                  <span class="completed-text">
                    Selesai
                  </span>

                  @endif

                </td>

              </tr>


              @empty

              <tr>

                <td
                  colspan="6"
                  class="empty-state">

                  Belum ada transaksi peminjaman.

                </td>

              </tr>

              @endforelse

            </tbody>

          </table>

        </div>

      </div>

    </div>

  </div>

</section>

@endsection