<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Ordered = 'ordered';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::PendingApproval => 'Pend. Aprobación',
            self::Approved => 'Aprobada',
            self::Ordered => 'Pedida',
            self::Received => 'Recibida',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-600 border-gray-200',
            self::PendingApproval => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::Approved => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Ordered => 'bg-purple-50 text-purple-700 border-purple-200',
            self::Received => 'bg-green-50 text-green-700 border-green-200',
            self::Cancelled => 'bg-red-50 text-red-600 border-red-200',
        };
    }
}
