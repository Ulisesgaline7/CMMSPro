<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\AuditStatus;
use App\Enums\AuditType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    /** @use HasFactory<\Database\Factories\AuditFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'created_by',
        'auditor_id',
        'code',
        'title',
        'description',
        'type',
        'status',
        'scope',
        'scheduled_date',
        'completed_date',
        'location',
        'findings_count',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => AuditStatus::class,
            'type' => AuditType::class,
            'scheduled_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AuditFinding::class);
    }
}
