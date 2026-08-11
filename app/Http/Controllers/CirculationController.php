<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    public function index()
    {
        $members = Member::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // Semua buku tetap ditampilkan,
        // termasuk yang stok tersedia = 0.
        $books = Book::orderByRaw(
            "CAST(SUBSTRING_INDEX(title, ' ', -1) AS UNSIGNED)"
        )->get();

        $borrowings = Borrowing::with([
            'member',
            'details.book'
        ])
            ->latest()
            ->get();

        return view('circulation.index', compact(
            'members',
            'books',
            'borrowings'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'book_id' => ['required', 'exists:books,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
        ]);

        DB::transaction(function () use ($validated) {

            $book = Book::lockForUpdate()
                ->findOrFail($validated['book_id']);

            // Pengaman backend.
            // Walaupun option di frontend disabled,
            // tetap wajib dicek di server.
            if ($book->available_stock < 1) {
                abort(422, 'Buku sedang tidak tersedia.');
            }

            $borrowing = Borrowing::create([
                'member_id' => $validated['member_id'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'],
                'status' => 'dipinjam',
            ]);

            BorrowingDetail::create([
                'borrowing_id' => $borrowing->id,
                'book_id' => $book->id,
                'quantity' => 1,
            ]);

            $book->decrement('available_stock');
        });

        return redirect()
            ->route('circulation')
            ->with('success', 'Peminjaman berhasil diproses.');
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with(
                'error',
                'Peminjaman ini sudah dikembalikan.'
            );
        }

        DB::transaction(function () use ($borrowing) {

            $borrowing->load('details');

            foreach ($borrowing->details as $detail) {

                $book = Book::lockForUpdate()
                    ->findOrFail($detail->book_id);

                $book->increment(
                    'available_stock',
                    $detail->quantity
                );
            }

            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'status' => 'dikembalikan',
            ]);
        });

        return redirect()
            ->route('circulation')
            ->with('success', 'Buku berhasil dikembalikan.');
    }
}
