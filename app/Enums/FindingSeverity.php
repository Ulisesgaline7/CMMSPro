<?php

namespace App\Enums;

enum FindingSeverity: string
{
    case Minor = 'minor';
    case Major = 'major';
    case Critical = 'critical';
    case Observation = 'observation';

    public function label(): string
    {
        return match ($this) {
            self::Minor => 'Menor',
            self::Major => 'Mayor',
            self::Critical => 'Crítico',
            self::Observation => 'Observación',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Minor => 'yellow',
            self::Major => 'orange',
            self::Critical => 'red',
            self::Observation => 'blue',
        };
    }
}
