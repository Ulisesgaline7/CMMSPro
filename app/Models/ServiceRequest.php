<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\ServiceRequestCategory;
use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceRequestFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'requested_by',
        'assigned_to',
        'asset_id',
        'code',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'location_description',
        'sla_deadline',
        'resolved_at',
        'closed_at',
        'resolution_time',
        'sla_met',
        'resolution_notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'category'     => ServiceRequestCategory::class,
            'priority'     => ServiceRequestPriority::class,
            'status'       => ServiceRequestStatus::class,
            'sla_deadline' => 'datetime',
            'resolved_at'  => 'datetime',
            'closed_at'    => 'datetime',
            'sla_met'      => 'boolean',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSlaBreached(): bool
    {
        if (in_array($this->status, [ServiceRequestStatus::Resolved, ServiceRequestStatus::Closed])) {
            return false;
        }

        return $this->sla_deadline !== null && $this->sla_deadline->isPast();
    }
}
