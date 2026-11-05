
<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class BookLocatorController extends Controller
{
    public function show(Reservation $reservation)
    {
        $reservation->load([
            'member',
            'book',
            'bookCopy.shelf.zone.floor',
        ]);

        $targetCopy = $reservation->bookCopy;

        if (!$targetCopy) {
            abort(404, 'Eksemplar buku tidak ditemukan.');
        }

        $targetShelf = $targetCopy->shelf;
        $targetZone = $targetShelf?->zone;
        $targetFloor = $targetZone?->floor;

        return view('book-locator.show', [
            'reservation' => $reservation,
            'targetCopy' => $targetCopy,
            'targetShelf' => $targetShelf,
            'targetZone' => $targetZone,
            'targetFloor' => $targetFloor,
        ]);
    }
}
