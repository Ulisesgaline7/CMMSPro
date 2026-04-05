<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderChecklistItem extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'work_order_checklist_id',
        'description',
        'is_completed',
        'completed_by',
        'completed_at',
        'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(WorkOrderChecklist::class, 'work_order_checklist_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
