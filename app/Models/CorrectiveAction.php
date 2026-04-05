<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\CorrectiveActionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrectiveAction extends Model
{
    /** @use HasFactory<\Database\Factories\CorrectiveActionFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'finding_id',
        'work_order_id',
        'assigned_to',
        'created_by',
        'code',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'root_cause',
        'action_taken',
        'due_date',
        'completed_at',
        'verified_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => CorrectiveActionStatus::class,
            'priority' => 'string',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(AuditFinding::class, 'finding_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
