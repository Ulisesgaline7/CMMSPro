<?php

namespace App\Enums;

enum ShiftType: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Night = 'night';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Morning => 'Mañana',
            self::Afternoon => 'Tarde',
            self::Night => 'Noche',
            self::Custom => 'Personalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Morning => 'bg-amber-50 text-amber-700 border-amber-200',
            self::Afternoon => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Night => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::Custom => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Morning => 'sunrise',
            self::Afternoon => 'sun',
            self::Night => 'moon',
            self::Custom => 'clock',
        };
    }
}
