<?php

namespace App\Enums;

enum SkillLevel: string
{
    case Basic = 'basic';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public function label(): string
    {
        return match ($this) {
            self::Basic => 'Básico',
            self::Intermediate => 'Intermedio',
            self::Advanced => 'Avanzado',
            self::Expert => 'Experto',
        };
    }
}
