<?php

namespace App\Enums;

enum ShiftStatus: string
{
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Completed = 'completed';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Programado',
            self::Active => 'Activo',
            self::Completed => 'Completado',
            self::Absent => 'Ausente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::Active => 'bg-green-50 text-green-700 border-green-200',
            self::Completed => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Absent => 'bg-red-50 text-red-700 border-red-200',
        };
    }
}
