<?php

namespace App\Enums;

enum PermitType: string
{
    case Loto          = 'loto';
    case HotWork       = 'hot_work';
    case ConfinedSpace = 'confined_space';
    case WorkAtHeight  = 'work_at_height';
    case Electrical    = 'electrical';
    case General       = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Loto          => 'LOTO — Bloqueo / Etiquetado',
            self::HotWork       => 'Trabajo en Caliente',
            self::ConfinedSpace => 'Espacio Confinado',
            self::WorkAtHeight  => 'Trabajo en Altura',
            self::Electrical    => 'Eléctrico',
            self::General       => 'Permiso General',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Loto          => 'bg-red-50 text-red-700 border-red-200',
            self::HotWork       => 'bg-orange-50 text-orange-700 border-orange-200',
            self::ConfinedSpace => 'bg-purple-50 text-purple-700 border-purple-200',
            self::WorkAtHeight  => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Electrical    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            self::General       => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Loto          => 'lock',
            self::HotWork       => 'flame',
            self::ConfinedSpace => 'circle-dashed',
            self::WorkAtHeight  => 'arrow-up',
            self::Electrical    => 'zap',
            self::General       => 'shield',
        };
    }
}
