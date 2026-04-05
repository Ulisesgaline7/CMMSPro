<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Incomplete = 'incomplete';
    case Active = 'active';
    case Trialing = 'trialing';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Incomplete => 'Incompleto',
            self::Active => 'Activo',
            self::Trialing => 'Prueba',
            self::PastDue => 'Vencido',
            self::Canceled => 'Cancelado',
            self::Suspended => 'Suspendido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Incomplete => 'bg-gray-100 text-gray-600',
            self::Active => 'bg-green-100 text-green-700',
            self::Trialing => 'bg-blue-100 text-blue-700',
            self::PastDue => 'bg-orange-100 text-orange-700',
            self::Canceled => 'bg-red-100 text-red-700',
            self::Suspended => 'bg-red-100 text-red-700',
        };
    }
}
