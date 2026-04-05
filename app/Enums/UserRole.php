<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Technician = 'technician';
    case Reader = 'reader';
    case Requester = 'requester';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Supervisor => 'Supervisor',
            self::Technician => 'Técnico',
            self::Reader => 'Lector / Auditor',
            self::Requester => 'Solicitante',
        };
    }

    public function canManageWorkOrders(): bool
    {
        return in_array($this, [self::Admin, self::Supervisor]);
    }

    public function canExecuteWorkOrders(): bool
    {
        return in_array($this, [self::Admin, self::Supervisor, self::Technician]);
    }

    public function canManageAssets(): bool
    {
        return in_array($this, [self::Admin, self::Supervisor]);
    }

    public function canViewReports(): bool
    {
        return in_array($this, [self::Admin, self::Supervisor, self::Reader]);
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
