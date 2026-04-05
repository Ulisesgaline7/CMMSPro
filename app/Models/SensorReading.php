<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    /** @use HasFactory<\Database\Factories\SensorReadingFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'sensor_id',
        'tenant_id',
        'value',
        'quality',
        'read_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'read_at' => 'datetime',
        ];
    }

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
