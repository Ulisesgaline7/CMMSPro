<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'description',
        'base_price_monthly',
        'is_core',
        'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'base_price_monthly' => 'decimal:2',
            'is_core' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
