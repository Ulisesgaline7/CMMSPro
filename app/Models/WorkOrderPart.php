<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderPart extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'work_order_id',
        'part_id',
        'part_name',
        'quantity',
        'unit',
        'unit_cost',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function totalCost(): float
    {
        if ($this->unit_cost === null) {
            return 0.0;
        }

        return (float) $this->quantity * (float) $this->unit_cost;
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
