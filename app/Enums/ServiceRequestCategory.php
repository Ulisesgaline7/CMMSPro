<?php

namespace App\Enums;

enum ServiceRequestCategory: string
{
    case HVAC       = 'hvac';
    case Electrical = 'electrical';
    case Plumbing   = 'plumbing';
    case IT         = 'it';
    case General    = 'general';
    case Cleaning   = 'cleaning';

    public function label(): string
    {
        return match ($this) {
            self::HVAC       => 'HVAC / Climatización',
            self::Electrical => 'Eléctrico',
            self::Plumbing   => 'Plomería',
            self::IT         => 'Sistemas TI',
            self::General    => 'General',
            self::Cleaning   => 'Limpieza',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HVAC       => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            self::Electrical => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::Plumbing   => 'bg-blue-50 text-blue-700 border-blue-200',
            self::IT         => 'bg-purple-50 text-purple-700 border-purple-200',
            self::General    => 'bg-gray-50 text-gray-700 border-gray-200',
            self::Cleaning   => 'bg-green-50 text-green-700 border-green-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::HVAC       => 'thermometer',
            self::Electrical => 'zap',
            self::Plumbing   => 'droplets',
            self::IT         => 'monitor',
            self::General    => 'wrench',
            self::Cleaning   => 'sparkles',
        };
    }
}
