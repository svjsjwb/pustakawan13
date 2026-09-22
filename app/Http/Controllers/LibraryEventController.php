<?php

namespace App\Http\Controllers;

use App\Mail\LibraryEventMail;
use App\Models\LibraryEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LibraryEventController extends Controller
{
    public function broadcast(Request $request): RedirectResponse
    {
        $event = LibraryEvent::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ]) + ['created_by' => $request->user()->id]);

        User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->chunkById(100, function ($users) use ($event): void {
                foreach ($users as $user) {
                    Mail::to($user->email)->queue(new LibraryEventMail($event));
                }
            });

        \App\Services\NotificationService::notifyAdmins(new \App\Mail\AdminActivityAlertMail(
            eventTitle: 'Pengumuman Event Perpustakaan Baru',
            messageText: "Event perpustakaan \"{$event->name}\" telah dibuat dan disiarkan ke seluruh anggota.",
            details: [
                'Nama Acara' => $event->name,
                'Tanggal'    => $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') : '-',
                'Lokasi'     => $event->location,
                'Dibuat Oleh'=> $request->user()->name,
            ],
            actionUrl: url('/dashboard'),
            actionLabel: 'Buka Dashboard'
        ));

        return back()->with('success', 'Informasi acara berhasil dikirim melalui antrean email.');
    }
}
