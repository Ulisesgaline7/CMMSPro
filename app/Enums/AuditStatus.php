<?php

namespace App\Enums;

enum AuditStatus: string
{
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planificada',
            self::InProgress => 'En Progreso',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'yellow',
            self::InProgress => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }
}
