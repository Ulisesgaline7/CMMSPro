<?php

namespace App\Notifications;

use App\Events\NotificationCreated;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTenantRegistered extends Notification
{
    use Queueable;

    public function __construct(public readonly Tenant $tenant) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $data = [
            'title'       => "Nuevo cliente: {$this->tenant->name}",
            'message'     => "El tenant «{$this->tenant->name}» ({$this->tenant->plan->label()}) acaba de registrarse en la plataforma.",
            'type'        => 'new_tenant',
            'icon'        => 'building-2',
            'color'       => '#22c55e',
            'url'         => "/super-admin/tenants/{$this->tenant->id}",
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'plan'        => $this->tenant->plan->value,
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
