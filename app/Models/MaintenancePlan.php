<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\MaintenancePlanFrequency;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenancePlan extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenancePlanFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'asset_id',
        'assigned_to',
        'name',
        'description',
        'type',
        'frequency',
        'frequency_value',
        'priority',
        'estimated_duration',
        'start_date',
        'end_date',
        'next_execution_date',
        'last_execution_date',
        'is_active',
        'checklist_template',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => WorkOrderType::class,
            'frequency' => MaintenancePlanFrequency::class,
            'priority' => WorkOrderPriority::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'next_execution_date' => 'date',
            'last_execution_date' => 'date',
            'is_active' => 'boolean',
            'checklist_template' => 'array',
        ];
    }

    public function isCalendarBased(): bool
    {
        return $this->frequency->isCalendarBased();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}
