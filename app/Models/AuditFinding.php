<?php

namespace App\Models;

use App\Enums\FindingSeverity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditFinding extends Model
{
    /** @use HasFactory<\Database\Factories\AuditFindingFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'audit_id',
        'assigned_to',
        'code',
        'description',
        'severity',
        'status',
        'due_date',
        'closed_at',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'severity' => FindingSeverity::class,
            'status' => 'string',
            'due_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function correctiveActions(): HasMany
    {
        return $this->hasMany(CorrectiveAction::class, 'finding_id');
    }
}
