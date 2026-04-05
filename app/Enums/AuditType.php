<?php

namespace App\Enums;

enum AuditType: string
{
    case Internal = 'internal';
    case External = 'external';
    case Regulatory = 'regulatory';
    case Supplier = 'supplier';

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Interna',
            self::External => 'Externa',
            self::Regulatory => 'Regulatoria',
            self::Supplier => 'Proveedores',
        };
    }
}
