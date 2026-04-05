<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'stripe_invoice_id',
        'amount_due',
        'amount_paid',
        'currency',
        'status',
        'invoice_pdf_url',
        'paid_at',
        'due_date',
        'period_start',
        'period_end',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'due_date' => 'datetime',
            'period_start' => 'datetime',
            'period_end' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
