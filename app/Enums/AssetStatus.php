<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case UnderMaintenance = 'under_maintenance';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            self::UnderMaintenance => 'En Mantenimiento',
            self::Retired => 'Dado de Baja',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Inactive => 'gray',
            self::UnderMaintenance => 'yellow',
            self::Retired => 'red',
        };
    }

    public function isOperational(): bool
    {
        return $this === self::Active;
    }
}
