<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Approved = 'approved';
    case Obsolete = 'obsolete';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Review => 'En Revisión',
            self::Approved => 'Aprobado',
            self::Obsolete => 'Obsoleto',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-600 border-gray-200',
            self::Review => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::Approved => 'bg-green-50 text-green-700 border-green-200',
            self::Obsolete => 'bg-red-50 text-red-600 border-red-200',
        };
    }
}
