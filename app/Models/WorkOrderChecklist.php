<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrderChecklist extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'work_order_id',
        'name',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WorkOrderChecklistItem::class)->orderBy('sort_order');
    }

    public function completionPercentage(): float
    {
        $total = $this->items()->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = $this->items()->where('is_completed', true)->count();

        return round(($completed / $total) * 100, 1);
    }
}
