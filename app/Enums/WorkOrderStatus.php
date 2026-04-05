<?php

namespace App\Enums;

enum WorkOrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Pending => 'Pendiente',
            self::InProgress => 'En Progreso',
            self::OnHold => 'En Pausa',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Pending => 'yellow',
            self::InProgress => 'blue',
            self::OnHold => 'orange',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled]);
    }

    public function isOpen(): bool
    {
        return ! $this->isClosed();
    }

    /** @return array<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Pending, self::Cancelled],
            self::Pending => [self::InProgress, self::OnHold, self::Cancelled],
            self::InProgress => [self::OnHold, self::Completed, self::Cancelled],
            self::OnHold => [self::InProgress, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }
}
