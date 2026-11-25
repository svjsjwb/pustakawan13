<?php

namespace App\Events;

use App\Models\AppNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $notification;
    private ?int $targetUserId;
    private string $targetRole;

    public function __construct(AppNotification $notification)
    {
        $this->targetUserId = $notification->user_id ? (int) $notification->user_id : null;
        $this->targetRole = (string) ($notification->role ?? 'user');

        $this->notification = [
            'id'         => $notification->id,
            'type'       => $notification->type,
            'title'      => $notification->title,
            'message'    => $notification->message,
            'data'       => $notification->data,
            'is_read'    => (bool) $notification->is_read,
            'created_at' => $notification->created_at?->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        if ($this->targetRole === 'admin') {
            return [new PrivateChannel('admin.notifications')];
        }

        if ($this->targetUserId) {
            return [new PrivateChannel('user.notifications.' . $this->targetUserId)];
        }

        return [];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }
}
