<?php

namespace App\Enums;

enum DocumentType: string
{
    case Procedure = 'procedure';
    case Manual = 'manual';
    case Certificate = 'certificate';
    case Regulation = 'regulation';
    case Form = 'form';
    case Report = 'report';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Procedure => 'Procedimiento',
            self::Manual => 'Manual',
            self::Certificate => 'Certificado',
            self::Regulation => 'Reglamento',
            self::Form => 'Formato',
            self::Report => 'Reporte',
            self::Other => 'Otro',
        };
    }
}
