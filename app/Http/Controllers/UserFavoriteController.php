<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFavoriteController extends Controller
{
    public function index()
    {
        $ids   = session('user_favorites', []);
        $books = collect();

        if (!empty($ids)) {
            $books = Book::with('category')
                ->whereIn('id', $ids)
                ->get();
        }

        return view('user.favorites', compact('books'));
    }

    public function toggle(Request $request)
    {
        $bookId    = $request->input('book_id');
        $favorites = session('user_favorites', []);

        $favorited = false;
        if (in_array($bookId, $favorites)) {
            $favorites = array_filter($favorites, fn($id) => $id != $bookId);
            $message   = 'Buku dihapus dari favorit.';
        } else {
            $favorites[] = $bookId;
            $favorited   = true;
            $message     = 'Buku ditambahkan ke favorit!';
        }

        session(['user_favorites' => array_values($favorites)]);

        return response()->json([
            'favorited' => $favorited,
            'message'   => $message,
            'count'     => count($favorites),
        ]);
    }

    public function check(Request $request)
    {
        $bookId    = $request->input('book_id');
        $favorites = session('user_favorites', []);

        return response()->json([
            'favorited' => in_array($bookId, $favorites),
        ]);
    }
}
