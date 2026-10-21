<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealtimeEventController extends Controller
{
    /**
     * SSE stream endpoint.
     */
    public function stream(Request $request): StreamedResponse
    {
        // Close Laravel session and native PHP session to prevent blocking other HTTP requests
        if ($request->hasSession()) {
            $request->session()->save();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $lastId = (int) ($request->header('Last-Event-ID') ?: $request->query('last_id', 0));
        if ($lastId <= 0) {
            $lastId = (int) (DB::table('realtime_events')->max('id') ?? 0);
        }

        return response()->stream(function () use ($lastId) {
            // Disable time limit for the stream iteration
            set_time_limit(10);

            // Turn off output buffering if possible
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            $currentLastId = $lastId;
            $startTime = time();

            // Run for up to 3 seconds max to prevent worker thread starvation in PHP CLI/Apache
            while (time() - $startTime < 3) {
                if (connection_aborted()) {
                    break;
                }

                $events = DB::table('realtime_events')
                    ->where('id', '>', $currentLastId)
                    ->orderBy('id', 'asc')
                    ->get();

                if ($events->isNotEmpty()) {
                    foreach ($events as $evt) {
                        $currentLastId = (int) $evt->id;
                        echo "id: {$evt->id}\n";
                        echo "event: {$evt->event}\n";
                        echo "data: {$evt->payload}\n\n";
                        flush();
                    }
                } else {
                    echo ": heartbeat " . time() . "\n\n";
                    flush();
                }

                usleep(500000); // 0.5 second interval
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform, no-store, must-revalidate',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Fallback JSON polling endpoint.
     */
    public function poll(Request $request)
    {
        // Ensure session is saved immediately so subsequent navigation requests never wait
        if ($request->hasSession()) {
            $request->session()->save();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $lastId = (int) $request->query('last_id', 0);

        if ($lastId <= 0) {
            $lastId = (int) (DB::table('realtime_events')->max('id') ?? 0);
            return response()->json([
                'last_id' => $lastId,
                'events'  => [],
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        $events = DB::table('realtime_events')
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->limit(50)
            ->get();

        $newLastId = $events->isNotEmpty() ? (int) $events->last()->id : $lastId;

        $formatted = $events->map(function ($evt) {
            return [
                'id'      => $evt->id,
                'event'   => $evt->event,
                'payload' => json_decode($evt->payload, true),
            ];
        });

        return response()->json([
            'last_id' => $newLastId,
            'events'  => $formatted,
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }
}
