<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RealtimeService
{
    /**
     * Broadcast an event to the realtime_events table.
     */
    public static function publish(string $event, array $payload): void
    {
        try {
            DB::table('realtime_events')->insert([
                'event' => $event,
                'payload' => json_encode($payload),
                'created_at' => now(),
            ]);

            // Clean up events older than 24 hours randomly (1 in 50 chance)
            if (mt_rand(1, 50) === 1) {
                DB::table('realtime_events')
                    ->where('created_at', '<', now()->subHours(24))
                    ->delete();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
