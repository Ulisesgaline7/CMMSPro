<?php

namespace App\Enums;

enum CorrectiveActionStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Verified = 'verified';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Abierta',
            self::InProgress => 'En Progreso',
            self::Completed => 'Completada',
            self::Verified => 'Verificada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'yellow',
            self::InProgress => 'blue',
            self::Completed => 'teal',
            self::Verified => 'green',
            self::Cancelled => 'red',
        };
    }
}
