<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $member = Member::where('email', $user->email)->first();

        $borrowings   = collect();
        $reservations = collect();
        $stats = [
            'total_borrowed'    => 0,
            'active_borrowed'   => 0,
            'returned_borrowed' => 0,
        ];

        if ($member) {
            $query = Borrowing::with(['details.book.category'])
                ->where('member_id', $member->id);

            // Statistik peminjaman
            $allBorrowings = Borrowing::where('member_id', $member->id)->get();
            $stats['total_borrowed']    = $allBorrowings->count();
            $stats['active_borrowed']   = $allBorrowings->where('status', 'dipinjam')->count();
            $stats['returned_borrowed'] = $allBorrowings->where('status', 'dikembalikan')->count();

            if ($month = $request->input('month')) {
                $query->whereMonth('created_at', $month);
            }
            if ($year = $request->input('year')) {
                $query->whereYear('created_at', $year);
            }
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
            if ($search = $request->input('search')) {
                $query->whereHas('details.book', fn($q) =>
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%")
                );
            }

            $borrowings = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

            // Riwayat reservasi
            $reservations = Reservation::with('book')
                ->where('member_id', $member->id)
                ->orderByDesc('created_at')
                ->take(20)
                ->get();
        }

        return view('user.history', compact('borrowings', 'reservations', 'member', 'stats'));
    }
}
