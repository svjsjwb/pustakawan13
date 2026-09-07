<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    public function index(Request $request)
    {
        // Buku terbaru
        $latestBooks = Book::with('category')
            ->latest()
            ->take(8)
            ->get();

        // Buku populer
        // Untuk sementara berdasarkan jumlah stok.
        // Nanti bisa diganti berdasarkan histori peminjaman.
        $popularBooks = Book::with('category')
            ->where('available_stock', '>', 0)
            ->orderByDesc('available_stock')
            ->take(8)
            ->get();

        // Kategori
        $categories = Category::orderBy('name')
            ->take(8)
            ->get();

        // =====================================================
        // PENGUMUMAN (AKTIVITAS MANUAL) YANG BELUM DIBACA
        // =====================================================
        //
        // Hanya aktivitas MANUAL yang dianggap announcement.
        // Aktivitas otomatis (buku/anggota/peminjaman/reservasi)
        // TIDAK memunculkan popup.
        //
        // Jika user sudah menutup/membaca announcement,
        // popup TIDAK muncul lagi untuk user tersebut.
        //
        // Status "sudah dibaca" disimpan di tabel activity_reads,
        // bukan localStorage/Cookie.

        $user = $request->user();

        $unreadAnnouncement = null;

        if ($user) {

            $unreadAnnouncement =
                Activity::whereDoesntHave('readers', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->latest()
                ->first();
        }

        return view('user.home', compact(
            'latestBooks',
            'popularBooks',
            'categories',
            'unreadAnnouncement'
        ));
    }
}