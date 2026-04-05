<?php

namespace App\Enums;

enum AlertSeverity: string
{
    case Warning = 'warning';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Warning => 'Advertencia',
            self::Critical => 'Crítico',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Warning => 'bg-orange-100 text-orange-700 border-orange-200',
            self::Critical => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
