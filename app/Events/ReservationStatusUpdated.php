<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $reservation;
    private ?int $targetUserId;

    public function __construct(Reservation $reservation)
    {
        $this->targetUserId = $reservation->user_id ? (int) $reservation->user_id : null;

        $this->reservation = [
            'id'                        => $reservation->id,
            'status'                    => $reservation->status,
            'status_label'              => $reservation->display_status,
            'cancellation_requested_at' => $reservation->cancellation_requested_at?->toIso8601String(),
            'rejection_reason'          => $reservation->rejection_reason,
        ];
    }

    public function broadcastOn(): array
    {
        if ($this->targetUserId) {
            return [new PrivateChannel('user.reservations.' . $this->targetUserId)];
        }

        return [];
    }

    public function broadcastAs(): string
    {
        return 'reservation.updated';
    }
}
