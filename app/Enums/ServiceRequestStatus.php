<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case Open         = 'open';
    case InProgress   = 'in_progress';
    case PendingParts = 'pending_parts';
    case Resolved     = 'resolved';
    case Closed       = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open         => 'Abierta',
            self::InProgress   => 'En Progreso',
            self::PendingParts => 'Esperando Partes',
            self::Resolved     => 'Resuelta',
            self::Closed       => 'Cerrada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open         => 'bg-blue-50 text-blue-700 border-blue-200',
            self::InProgress   => 'bg-amber-50 text-amber-700 border-amber-200',
            self::PendingParts => 'bg-orange-50 text-orange-700 border-orange-200',
            self::Resolved     => 'bg-green-50 text-green-700 border-green-200',
            self::Closed       => 'bg-gray-50 text-gray-600 border-gray-200',
        };
    }
}
