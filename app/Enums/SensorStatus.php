<?php

namespace App\Enums;

enum SensorStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Fault = 'fault';
    case Disconnected = 'disconnected';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
            self::Fault => 'Falla',
            self::Disconnected => 'Desconectado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'bg-green-100 text-green-700 border-green-200',
            self::Inactive => 'bg-gray-100 text-gray-600 border-gray-200',
            self::Fault => 'bg-red-100 text-red-700 border-red-200',
            self::Disconnected => 'bg-orange-100 text-orange-700 border-orange-200',
        };
    }
}
