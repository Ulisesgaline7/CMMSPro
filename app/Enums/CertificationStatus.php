<?php

namespace App\Enums;

enum CertificationStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Expired => 'Vencida',
            self::Pending => 'Pendiente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'bg-green-50 text-green-700 border-green-200',
            self::Expired => 'bg-red-50 text-red-700 border-red-200',
            self::Pending => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        };
    }
}
