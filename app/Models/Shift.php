<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\ShiftStatus;
use App\Enums\ShiftType;
use Database\Factories\ShiftFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    /** @use HasFactory<ShiftFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'type',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => ShiftType::class,
            'status' => ShiftStatus::class,
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
