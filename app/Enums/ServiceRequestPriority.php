<?php

namespace App\Enums;

enum ServiceRequestPriority: string
{
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low      => 'Baja',
            self::Medium   => 'Media',
            self::High     => 'Alta',
            self::Critical => 'Crítica',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low      => 'bg-gray-50 text-gray-600 border-gray-200',
            self::Medium   => 'bg-blue-50 text-blue-700 border-blue-200',
            self::High     => 'bg-orange-50 text-orange-700 border-orange-200',
            self::Critical => 'bg-red-50 text-red-700 border-red-200',
        };
    }

    public function slaHours(): int
    {
        return match ($this) {
            self::Low      => 72,
            self::Medium   => 24,
            self::High     => 8,
            self::Critical => 2,
        };
    }
}
