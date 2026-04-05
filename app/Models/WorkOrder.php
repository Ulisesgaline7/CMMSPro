<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'asset_id',
        'maintenance_plan_id',
        'requested_by',
        'assigned_to',
        'approved_by',
        'code',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_date',
        'started_at',
        'completed_at',
        'estimated_duration',
        'actual_duration',
        'failure_cause',
        'resolution_notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => WorkOrderType::class,
            'status' => WorkOrderStatus::class,
            'priority' => WorkOrderPriority::class,
            'due_date' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function canTransitionTo(WorkOrderStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenancePlan(): BelongsTo
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(WorkOrderActivity::class)->orderBy('created_at');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(WorkOrderChecklist::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function permits(): HasMany
    {
        return $this->hasMany(PermitToWork::class);
    }

    public function hasActivePermit(): bool
    {
        return $this->permits()->where('status', 'active')->exists();
    }
}
