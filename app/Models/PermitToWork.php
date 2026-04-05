<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\PermitRiskLevel;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermitToWork extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'permit_to_work';

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'work_order_id',
        'requested_by',
        'approved_by',
        'code',
        'title',
        'type',
        'status',
        'risk_level',
        'description',
        'lockout_points',
        'required_ppe',
        'precautions',
        'approved_at',
        'activated_at',
        'expires_at',
        'closed_at',
        'rejection_reason',
        'closure_notes',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'type'         => PermitType::class,
            'status'       => PermitStatus::class,
            'risk_level'   => PermitRiskLevel::class,
            'approved_at'  => 'datetime',
            'activated_at' => 'datetime',
            'expires_at'   => 'datetime',
            'closed_at'    => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->status === PermitStatus::Active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast()
            && ! in_array($this->status, [PermitStatus::Closed, PermitStatus::Cancelled]);
    }
}
