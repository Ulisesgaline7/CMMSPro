<?php

namespace App\Enums;

enum PermitRiskLevel: string
{
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low      => 'Bajo',
            self::Medium   => 'Medio',
            self::High     => 'Alto',
            self::Critical => 'Crítico',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low      => 'bg-green-50 text-green-700 border-green-200',
            self::Medium   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::High     => 'bg-orange-50 text-orange-700 border-orange-200',
            self::Critical => 'bg-red-50 text-red-700 border-red-200',
        };
    }
}
