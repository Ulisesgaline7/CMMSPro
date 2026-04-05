<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\CertificationStatus;
use Database\Factories\UserCertificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCertification extends Model
{
    /** @use HasFactory<UserCertificationFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'issuing_body',
        'certificate_number',
        'issued_at',
        'expires_at',
        'status',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => CertificationStatus::class,
            'issued_at' => 'date',
            'expires_at' => 'date',
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
