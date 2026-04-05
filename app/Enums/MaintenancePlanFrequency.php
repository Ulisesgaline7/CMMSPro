<?php

namespace App\Enums;

enum MaintenancePlanFrequency: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Semiannual = 'semiannual';
    case Annual = 'annual';
    case ByHours = 'by_hours';
    case ByKilometers = 'by_kilometers';
    case ByCycles = 'by_cycles';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Diario',
            self::Weekly => 'Semanal',
            self::Biweekly => 'Quincenal',
            self::Monthly => 'Mensual',
            self::Quarterly => 'Trimestral',
            self::Semiannual => 'Semestral',
            self::Annual => 'Anual',
            self::ByHours => 'Por Horas de Operación',
            self::ByKilometers => 'Por Kilómetros',
            self::ByCycles => 'Por Ciclos',
        };
    }

    public function isCalendarBased(): bool
    {
        return in_array($this, [
            self::Daily,
            self::Weekly,
            self::Biweekly,
            self::Monthly,
            self::Quarterly,
            self::Semiannual,
            self::Annual,
        ]);
    }

    public function isMetricBased(): bool
    {
        return ! $this->isCalendarBased();
    }

    public function approximateDays(): ?int
    {
        return match ($this) {
            self::Daily => 1,
            self::Weekly => 7,
            self::Biweekly => 15,
            self::Monthly => 30,
            self::Quarterly => 90,
            self::Semiannual => 180,
            self::Annual => 365,
            default => null,
        };
    }
}
