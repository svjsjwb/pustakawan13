<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::latest()->get();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_number' => 'required|string|max:50|unique:members,member_number',
            'name' => 'required|string|max:255',
            'nis_nip' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'registered_at' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        Member::create($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'member_number' => 'required|string|max:50|unique:members,member_number,' . $member->id,
            'name' => 'required|string|max:255',
            'nis_nip' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'registered_at' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}