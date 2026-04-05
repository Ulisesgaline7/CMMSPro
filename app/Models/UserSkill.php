<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\SkillLevel;
use Database\Factories\UserSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    /** @use HasFactory<UserSkillFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'category',
        'level',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'level' => SkillLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
