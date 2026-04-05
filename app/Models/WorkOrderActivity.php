<?php

namespace App\Models;

use App\Enums\WorkOrderActivityAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderActivity extends Model
{
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'work_order_id',
        'user_id',
        'action',
        'metadata',
        'notes',
        'created_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'action' => WorkOrderActivityAction::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
