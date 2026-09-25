<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\MemberStatusService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'division' => ['required', 'string', 'max:100'],
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

        $members = Member::orderBy('id')->get();

        return view('members.index', compact('members'));
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
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
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
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );


        $member->update(
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | SINKRONISASI STATUS
        |--------------------------------------------------------------------------
        */

        app(MemberStatusService::class)->sync($member);


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