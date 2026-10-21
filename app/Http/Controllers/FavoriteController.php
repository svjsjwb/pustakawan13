<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Menampilkan daftar buku favorit pengguna
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua buku yang difavoritkan oleh user saat ini
        $favorites = UserFavorite::with(['book.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.favorites', compact('favorites'));
    }

    /**
     * Menambahkan buku ke daftar favorit (atau toggle)
     * Mengarahkan (redirect) ke /favorit-saya jika request standar, atau mengembalikan JSON jika AJAX
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = Auth::id();
        $bookId = (int) $request->book_id;

        $favorite = UserFavorite::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if (!$favorite) {
            UserFavorite::create([
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);
            $action = 'added';
            $message = 'Buku berhasil ditambahkan ke Favorit Saya.';
        } else {
            $action = 'already_exists';
            $message = 'Buku sudah ada di daftar Favorit Saya.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'action'  => $action,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('favorites.index')
            ->with('success', $message);
    }

    /**
     * Toggle status favorit (Tambah / Hapus)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = Auth::id();
        $bookId = (int) $request->book_id;

        $favorite = UserFavorite::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $action = 'removed';
            $message = 'Buku berhasil dihapus dari Favorit Saya.';
        } else {
            UserFavorite::create([
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);
            $action = 'added';
            $message = 'Buku berhasil ditambahkan ke Favorit Saya.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'action'  => $action,
                'message' => $message,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Menghapus buku dari daftar favorit
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::id();

        // Cari berdasarkan id relasi atau id buku
        $favorite = UserFavorite::where('user_id', $userId)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('book_id', $id);
            })
            ->first();

        if ($favorite) {
            $favorite->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'action'  => 'removed',
                'message' => 'Buku berhasil dihapus dari favorit.',
            ]);
        }

        return redirect()
            ->route('favorites.index')
            ->with('success', 'Buku berhasil dihapus dari Favorit Saya.');
    }
}
