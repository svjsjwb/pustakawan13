<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Reservation;
use App\Services\MemberStatusService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'division' => [
                'required',
                'string',
                'max:100',
                'in:Center Of Excellence,Digital Business,E-Publishing,Finance,General Trading,HR & GA,HSE,IQA,IT,Marketing,MTIS Perpuskita dan Tisera,MTIS Planning and Development,People Development Center,Production,School Book Sales,School Book Publishing,SCM,TAX'
            ],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => 'Nama karyawan wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'division.required' => 'Divisi wajib dipilih.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ];
    }

    /**
     * Menampilkan daftar anggota.
     */
    public function index()
    {
        app(MemberStatusService::class)->syncAll();

        $members = Member::with('user')
            ->orderByRaw("
                CASE
                    WHEN user_id IS NOT NULL
                         AND EXISTS (
                             SELECT 1
                             FROM users
                             WHERE users.id = members.user_id
                             AND users.role = 'guest'
                         )
                    THEN 0
                    ELSE 1
                END
            ")
            ->orderByDesc('id')
            ->get();

        return view(
            'members.index',
            compact('members')
        );
    }

    /**
     * Form tambah anggota.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Menyimpan anggota baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $validated['status'] = 'nonaktif';

        Member::create($validated);

        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Karyawan berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail anggota.
     */
    public function show(Member $member)
    {
        return view(
            'members.show',
            compact('member')
        );
    }

    /**
     * Form edit anggota.
     */
    public function edit(Member $member)
    {
        return view(
            'members.edit',
            compact('member')
        );
    }

    /**
     * Update data anggota.
     *
     * Status TIDAK berasal dari form.
     */
    public function update(
        Request $request,
        Member $member
    ) {
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $member->update($validated);

        app(MemberStatusService::class)->sync($member);

        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Data karyawan berhasil diperbarui.'
            );
    }

    /**
     * Menyetujui pendaftaran anggota.
     *
     * APPROVAL HANYA MENGUBAH ROLE USER:
     *
     * guest → member
     *
     * Status member TIDAK DIUBAH karena status
     * dikelola oleh MemberStatusService.
     */
    public function approve(Member $member)
    {
        if (!$member->user) {
            return back()->with(
                'error',
                'Akun pengguna untuk pendaftaran ini tidak ditemukan.'
            );
        }

        DB::transaction(function () use ($member) {
            $member->user->update([
                'role' => 'member',
            ]);
            $member->update([
                'status' => 'aktif',
            ]);
        });

        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Pendaftaran anggota berhasil disetujui.'
            );
    }

    /**
     * Menolak pendaftaran anggota.
     *
     * Pendaftar ditolak secara permanen:
     * - Member dihapus
     * - User dihapus
     *
     * Dengan begitu email dapat digunakan kembali
     * untuk melakukan pendaftaran baru.
     */
    public function reject(Member $member)
    {
        /*
        |--------------------------------------------------------------------------
        | HANYA GUEST YANG BOLEH DITOLAK
        |--------------------------------------------------------------------------
        */

        if ($member->user && $member->user->role !== 'guest') {
            return back()->with(
                'error',
                'Hanya pendaftaran yang masih menunggu persetujuan yang dapat ditolak.'
            );
        }

        DB::transaction(function () use ($member) {

            $user = $member->user;

            /*
            |--------------------------------------------------------------------------
            | HAPUS MEMBER
            |--------------------------------------------------------------------------
            */

            $member->delete();

            /*
            |--------------------------------------------------------------------------
            | HAPUS USER
            |--------------------------------------------------------------------------
            */

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Pendaftaran ditolak. Data anggota dan akun pengguna telah dihapus.'
            );
    }

    /**
     * Hapus anggota.
     *
     * Anggota TIDAK BOLEH dihapus jika masih memiliki
     * peminjaman aktif.
     *
     * Jika memiliki reservation aktif, BookCopy dan stok
     * dikembalikan terlebih dahulu.
     */
    public function destroy(Member $member)
    {
        /*
        |--------------------------------------------------------------------------
        | CEK PEMINJAMAN AKTIF
        |--------------------------------------------------------------------------
        |
        | Jika masih ada buku yang belum dikembalikan,
        | penghapusan diblokir.
        |
        */

        $hasActiveBorrowing = $member->borrowings()
            ->whereNull('returned_at')
            ->whereIn('status', [
                'dipinjam',
                'diperpanjang',
                'terlambat',
            ])
            ->exists();

        if ($hasActiveBorrowing) {
            return back()->with(
                'error',
                'Anggota tidak dapat dihapus karena masih memiliki buku yang sedang dipinjam. Silakan proses pengembalian buku terlebih dahulu.'
            );
        }

        DB::transaction(function () use ($member) {

            /*
            |--------------------------------------------------------------------------
            | RELEASE RESERVATION AKTIF
            |--------------------------------------------------------------------------
            |
            | Reservation aktif memegang BookCopy dengan status "reserved"
            | dan sudah mengurangi stok buku.
            |
            */

            $activeReservations = $member->reservations()
                ->whereNotIn('status', [
                    'ditolak',
                    'dibatalkan',
                    'selesai',
                ])
                ->lockForUpdate()
                ->get();

            foreach ($activeReservations as $reservation) {

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN BOOK COPY
                |--------------------------------------------------------------------------
                */

                if ($reservation->book_copy_id) {

                    $bookCopy = BookCopy::lockForUpdate()
                        ->find($reservation->book_copy_id);

                    if (
                        $bookCopy &&
                        $bookCopy->status === 'reserved'
                    ) {
                        $bookCopy->update([
                            'status' => 'available',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK
                |--------------------------------------------------------------------------
                */

                $book = Book::lockForUpdate()
                    ->find($reservation->book_id);

                if ($book) {
                    $book->increment('stok');
                }

                /*
                |--------------------------------------------------------------------------
                | HAPUS RESERVATION
                |--------------------------------------------------------------------------
                */

                $reservation->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS MEMBER
            |--------------------------------------------------------------------------
            |
            | FK cascade akan membersihkan data yang berkaitan
            | dengan member.
            |
            */

            $user = $member->user;

            $member->delete();

            /*
            |--------------------------------------------------------------------------
            | HAPUS USER
            |--------------------------------------------------------------------------
            */

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Data anggota dan akun pengguna berhasil dihapus.'
            );
    }
}