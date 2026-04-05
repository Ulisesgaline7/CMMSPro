<?php

namespace App\Enums;

enum WorkOrderType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case Predictive = 'predictive';

    public function label(): string
    {
        return match ($this) {
            self::Preventive => 'Preventivo',
            self::Corrective => 'Correctivo',
            self::Predictive => 'Predictivo',
        };
    }

    public function abbreviation(): string
    {
        return match ($this) {
            self::Preventive => 'PM',
            self::Corrective => 'CM',
            self::Predictive => 'PdM',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Preventive => 'blue',
            self::Corrective => 'red',
            self::Predictive => 'purple',
        };
    }
}
