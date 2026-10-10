<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFavoriteController extends Controller
{
    public function index()
    {
        $books = Book::with('category')
            ->whereHas('favorites', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return view('user.favorites', compact('books'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $userId = Auth::id();
        $bookId = $request->book_id;

        $favorite = UserFavorite::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'favorited' => false,
                'message'   => 'Buku dihapus dari favorit.',
                'count'     => UserFavorite::where('user_id', $userId)->count(),
            ]);
        }

        UserFavorite::create([
            'user_id' => $userId,
            'book_id' => $bookId,
        ]);

        return response()->json([
            'favorited' => true,
            'message'   => 'Buku ditambahkan ke favorit!',
            'count'     => UserFavorite::where('user_id', $userId)->count(),
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $favorited = UserFavorite::where('user_id', Auth::id())
            ->where('book_id', $request->book_id)
            ->exists();

        return response()->json([
            'favorited' => $favorited,
        ]);
    }
}