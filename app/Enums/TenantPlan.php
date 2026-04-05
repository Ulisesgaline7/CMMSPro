<?php

namespace App\Enums;

enum TenantPlan: string
{
    case Starter = 'starter';
    case Professional = 'professional';
    case Enterprise = 'enterprise';

    public function label(): string
    {
        return match ($this) {
            self::Starter => 'Starter',
            self::Professional => 'Professional',
            self::Enterprise => 'Enterprise',
        };
    }

    public function maxUsers(): int
    {
        return match ($this) {
            self::Starter => 10,
            self::Professional => 50,
            self::Enterprise => PHP_INT_MAX,
        };
    }

    public function maxAssets(): int
    {
        return match ($this) {
            self::Starter => 200,
            self::Professional => 5000,
            self::Enterprise => PHP_INT_MAX,
        };
    }
}
