<?php

namespace App\Notifications;

use App\Events\NotificationCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly string $severity = 'info',
        public readonly string $url = '/super-admin',
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $colorMap = ['info' => '#6366f1', 'warning' => '#f59e0b', 'error' => '#ef4444', 'success' => '#22c55e'];
        $iconMap  = ['info' => 'info', 'warning' => 'alert-triangle', 'error' => 'x-circle', 'success' => 'check-circle'];

        $data = [
            'title'    => $this->title,
            'message'  => $this->message,
            'type'     => 'system_alert',
            'severity' => $this->severity,
            'icon'     => $iconMap[$this->severity] ?? 'info',
            'color'    => $colorMap[$this->severity] ?? '#6366f1',
            'url'      => $this->url,
        ];

        NotificationCreated::dispatch(
            $notifiable->id,
            $data['title'],
            $data['message'],
            $data['icon'],
            $data['color'],
            $data['type'],
            $data['url'],
        );

        return $data;
    }
}
