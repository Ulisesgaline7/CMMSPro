<?php

namespace App\Notifications;

use App\Events\NotificationCreated;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TenantSuspended extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly string $reason = '',
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $data = [
            'title'       => "Tenant suspendido: {$this->tenant->name}",
            'message'     => "El cliente «{$this->tenant->name}» ha sido suspendido." . ($this->reason ? " Motivo: {$this->reason}" : ''),
            'type'        => 'tenant_suspended',
            'icon'        => 'circle-slash',
            'color'       => '#f59e0b',
            'url'         => "/super-admin/tenants/{$this->tenant->id}",
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'reason'      => $this->reason,
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
