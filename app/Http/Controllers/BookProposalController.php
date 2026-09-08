<?php

namespace App\Http\Controllers;

use App\Models\BookProposal;
use App\Models\AppNotification;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookProposalController extends Controller
{
    /**
     * User: Tampilkan daftar usulan buku
     */
    public function index()
    {
        $user = Auth::user();
        $proposals = BookProposal::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('user.proposals', compact('proposals'));
    }

    /**
     * User: Ajukan usulan buku baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'author'        => 'required|string|max:255',
            'publisher'     => 'nullable|string|max:255',
            'year'          => 'nullable|string|max:10',
            'category_name' => 'nullable|string|max:100',
            'reason'        => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $member = Member::where('email', $user->email)->first();

        $proposal = BookProposal::create([
            'user_id'       => $user->id,
            'member_id'     => $member?->id,
            'title'         => $validated['title'],
            'author'        => $validated['author'],
            'publisher'     => $validated['publisher'] ?? null,
            'year'          => $validated['year'] ?? null,
            'category_name' => $validated['category_name'] ?? null,
            'reason'        => $validated['reason'] ?? null,
            'status'        => 'menunggu',
        ]);

        // Notifikasi ke Admin
        AppNotification::notifyAdmin(
            'book_proposal_new',
            'Pengajuan Buku Baru',
            "{$user->name} mengusulkan pengadaan buku baru: \"{$proposal->title}\".",
            ['proposal_id' => $proposal->id]
        );

        return redirect()->back()->with('success', 'Usulan buku berhasil dikirim dan sedang menunggu peninjauan Admin.');
    }

    /**
     * Admin: Review & update status pengajuan buku (Setujui / Tolak)
     */
    public function updateStatus(Request $request, BookProposal $proposal)
    {
        $validated = $request->validate([
            'status'      => 'required|in:disetujui,ditolak',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $proposal->update([
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        // Notifikasi ke User pengusul
        $statusLabel = $validated['status'] === 'disetujui' ? 'disetujui' : 'ditolak';
        $message = "Usulan buku \"{$proposal->title}\" Anda telah {$statusLabel} oleh Admin.";
        if (!empty($validated['admin_notes'])) {
            $message .= " Catatan: {$validated['admin_notes']}";
        }

        AppNotification::notifyUser(
            $proposal->user_id,
            $validated['status'] === 'disetujui' ? 'book_proposal_approved' : 'book_proposal_rejected',
            'Status Usulan Buku: ' . ucfirst($statusLabel),
            $message,
            ['proposal_id' => $proposal->id]
        );

        return redirect()->back()->with('success', "Usulan buku berhasil di-{$statusLabel}.");
    }
}
