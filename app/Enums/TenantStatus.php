<?php

namespace App\Enums;

enum TenantStatus: string
{
    case Active = 'active';
    case Trial = 'trial';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Trial => 'Prueba',
            self::Inactive => 'Inactivo',
            self::Suspended => 'Suspendido',
        };
    }

    public function isOperational(): bool
    {
        return in_array($this, [self::Active, self::Trial]);
    }
}
