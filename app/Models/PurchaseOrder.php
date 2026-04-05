<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\PurchaseOrderPriority;
use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseOrderFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'work_order_id',
        'requested_by',
        'approved_by',
        'code',
        'supplier_name',
        'supplier_contact',
        'status',
        'priority',
        'expected_delivery',
        'received_at',
        'total_amount',
        'currency',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'priority' => PurchaseOrderPriority::class,
            'expected_delivery' => 'date',
            'received_at' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generateCode(): string
    {
        $last = static::withoutGlobalScopes()
            ->where('code', 'like', 'PO-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'PO-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
