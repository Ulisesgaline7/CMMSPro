<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'status',
        'deployment_type',
        'base_price_monthly',
        'modules_cost',
        'users_cost',
        'total_monthly',
        'admin_count',
        'supervisor_count',
        'technician_count',
        'reader_count',
        'asset_count',
        'current_period_start',
        'current_period_end',
        'trial_end',
        'cancel_at_period_end',
        'modules_json',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'base_price_monthly' => 'decimal:2',
            'modules_cost' => 'decimal:2',
            'users_cost' => 'decimal:2',
            'total_monthly' => 'decimal:2',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'trial_end' => 'datetime',
            'cancel_at_period_end' => 'boolean',
            'modules_json' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
