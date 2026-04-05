<?php

namespace App\Enums;

enum PermitStatus: string
{
    case Draft           = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved        = 'approved';
    case Active          = 'active';
    case Cancelled       = 'cancelled';
    case Closed          = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft           => 'Borrador',
            self::PendingApproval => 'Pendiente Aprobación',
            self::Approved        => 'Aprobado',
            self::Active          => 'Activo',
            self::Cancelled       => 'Cancelado',
            self::Closed          => 'Cerrado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft           => 'bg-gray-100 text-gray-600 border-gray-200',
            self::PendingApproval => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::Approved        => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Active          => 'bg-green-50 text-green-700 border-green-200',
            self::Cancelled       => 'bg-red-50 text-red-600 border-red-200',
            self::Closed          => 'bg-gray-50 text-gray-600 border-gray-200',
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return match ($this) {
            self::Draft           => in_array($new, [self::PendingApproval, self::Cancelled]),
            self::PendingApproval => in_array($new, [self::Approved, self::Cancelled]),
            self::Approved        => in_array($new, [self::Active, self::Cancelled]),
            self::Active          => in_array($new, [self::Closed, self::Cancelled]),
            self::Cancelled,
            self::Closed          => false,
        };
    }
}
