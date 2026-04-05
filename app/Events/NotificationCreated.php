<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $icon,
        public readonly string $color,
        public readonly string $type,
        public readonly string $url = '/super-admin/notifications',
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("App.Models.User.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'icon'    => $this->icon,
            'color'   => $this->color,
            'type'    => $this->type,
            'url'     => $this->url,
        ];
    }
}
