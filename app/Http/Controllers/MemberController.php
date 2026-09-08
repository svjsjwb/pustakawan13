<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Reservation;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Sinkronisasi status member berdasarkan aktivitas SAAT INI.
     *
     * Member AKTIF jika:
     * - memiliki peminjaman yang belum dikembalikan
     * ATAU
     * - memiliki reservasi yang masih berlaku
     *
     * Reservasi yang sudah melewati expires_at TIDAK dihitung aktif.
     */
    private function syncMemberStatus(Member $member): void
    {
        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN AKTIF
        |--------------------------------------------------------------------------
        |
        | Selama returned_at masih NULL, peminjaman dianggap aktif.
        |
        | Termasuk peminjaman yang sudah terlambat.
        |
        */

        $hasActiveBorrowing = Borrowing::where(
            'member_id',
            $member->id
        )
            ->whereNull('returned_at')
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | RESERVASI AKTIF
        |--------------------------------------------------------------------------
        |
        | Reservasi hanya dianggap aktif jika:
        |
        | 1. Bukan ditolak
        | 2. Bukan dibatalkan
        | 3. Bukan selesai
        | 4. expires_at masih hari ini atau setelah hari ini
        |
        */

        $hasActiveReservation = Reservation::where(
            'member_id',
            $member->id
        )
            ->whereNotIn('status', [
                'ditolak',
                'dibatalkan',
                'selesai',
            ])
            ->whereNotNull('expires_at')
            ->whereDate(
                'expires_at',
                '>=',
                now()->toDateString()
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS MEMBER
        |--------------------------------------------------------------------------
        */

        $member->update([
            'status' => (
                $hasActiveBorrowing ||
                $hasActiveReservation
            )
                ? 'aktif'
                : 'nonaktif',
        ]);
    }


    /**
     * Sinkronisasi seluruh member.
     */
    private function syncAllMemberStatuses(): void
    {
        Member::query()
            ->get()
            ->each(function (Member $member) {

                $this->syncMemberStatus(
                    $member
                );

            });
    }


    /**
     * Menampilkan daftar anggota.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SINKRONISASI STATUS
        |--------------------------------------------------------------------------
        */

        $this->syncAllMemberStatuses();


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA TERBARU
        |--------------------------------------------------------------------------
        */

        $members = Member::orderBy(
            'id',
            'asc'
        )->get();


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
        return view(
            'members.create'
        );
    }


    /**
     * Menyimpan anggota baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'division' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

        ], [

            'name.required' =>
                'Nama karyawan wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'division.required' =>
                'Divisi wajib dipilih.',

            'phone.required' =>
                'Nomor telepon wajib diisi.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | GENERATE NOMOR ANGGOTA
        |--------------------------------------------------------------------------
        */

        $lastMember =
            Member::orderByDesc('id')
                ->first();


        if (
            $lastMember &&
            $lastMember->member_number
        ) {

            $lastNumber =
                (int) substr(
                    $lastMember->member_number,
                    1
                );

            $nextNumber =
                $lastNumber + 1;

        } else {

            $nextNumber = 1;
        }


        $validated['member_number'] =
            'M' .
            str_pad(
                $nextNumber,
                3,
                '0',
                STR_PAD_LEFT
            );


        /*
        |--------------------------------------------------------------------------
        | STATUS AWAL
        |--------------------------------------------------------------------------
        */

        $validated['status'] =
            'nonaktif';


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        Member::create(
            $validated
        );


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
    public function show(
        Member $member
    ) {
        return view(
            'members.show',
            compact('member')
        );
    }


    /**
     * Form edit anggota.
     */
    public function edit(
        Member $member
    ) {
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
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'division' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

        ], [

            'name.required' =>
                'Nama karyawan wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'division.required' =>
                'Divisi wajib dipilih.',

            'phone.required' =>
                'Nomor telepon wajib diisi.',
        ]);


        $member->update(
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | SINKRONISASI STATUS
        |--------------------------------------------------------------------------
        */

        $this->syncMemberStatus(
            $member
        );


        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Data karyawan berhasil diperbarui.'
            );
    }


    /**
     * Hapus anggota.
     */
    public function destroy(
        Member $member
    ) {
        $member->delete();


        return redirect()
            ->route('members.index')
            ->with(
                'success',
                'Karyawan berhasil dihapus.'
            );
    }
}