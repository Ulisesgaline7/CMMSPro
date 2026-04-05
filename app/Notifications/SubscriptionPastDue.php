<?php

namespace App\Notifications;

use App\Events\NotificationCreated;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionPastDue extends Notification
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
        $mrr  = $this->tenant->subscription?->total_monthly ?? 0;
        $data = [
            'title'       => "Pago vencido: {$this->tenant->name}",
            'message'     => "La suscripción de «{$this->tenant->name}» está vencida. MRR en riesgo: \${$mrr}/mes.",
            'type'        => 'past_due',
            'icon'        => 'alert-circle',
            'color'       => '#ef4444',
            'url'         => '/super-admin/subscriptions',
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'mrr'         => $mrr,
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
