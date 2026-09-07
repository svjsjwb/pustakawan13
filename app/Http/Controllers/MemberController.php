<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar anggota
     */
    public function index()
    {
        $members = Member::orderBy('id', 'desc')->get();

        return view('members.index', compact('members'));
    }

    /**
     * Endpoint data anggota JSON untuk auto-refresh / real-time update
     */
    public function getMembersJson()
    {
        $members = Member::orderBy('id', 'desc')->get()->map(function ($member) {
            return [
                'id'         => $member->id,
                'name'       => $member->name,
                'email'      => $member->email ?: '-',
                'division'   => $member->division ?: 'Anggota',
                'phone'      => $member->phone ?: '-',
                'status'     => ucfirst(strtolower($member->status)),
                'joined_at'  => $member->created_at ? $member->created_at->translatedFormat('d M Y') : '-',
                'edit_url'   => route('members.edit', $member->id),
                'delete_url' => route('members.destroy', $member->id),
            ];
        });

        return response()->json([
            'success' => true,
            'members' => $members,
            'total'   => $members->count(),
        ]);
    }


    /**
     * Form tambah karyawan
     */
    public function create()
    {
        return view('members.create');
    }


    /**
     * Menyimpan karyawan baru
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
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

        'status' => [
            'required',
            'in:Aktif,Nonaktif',
        ],
    ], [
        'name.required' => 'Nama karyawan wajib diisi.',
        'division.required' => 'Divisi wajib dipilih.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'status.required' => 'Status wajib dipilih.',
    ]);

    Member::create($validated);

    return redirect()
        ->route('members.index')
        ->with('success', 'Karyawan berhasil ditambahkan.');
}


    /**
     * Menampilkan detail karyawan
     */
    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }


    /**
     * Form edit karyawan
     */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }


    /**
     * Update data karyawan
     */
    public function update(Request $request, Member $member)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'division' => 'required|string|max:100',
        'phone' => 'required|string|max:20',
        'status' => 'required|in:Aktif,Nonaktif',
    ]);

    $member->update($validated);

    return redirect()
        ->route('members.index')
        ->with('success', 'Data karyawan berhasil diperbarui.');
}


    /**
     * Hapus karyawan
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}