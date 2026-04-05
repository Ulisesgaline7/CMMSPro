<?php

namespace App\Enums;

enum LocationType: string
{
    case Plant = 'plant';
    case Building = 'building';
    case Floor = 'floor';
    case Area = 'area';
    case Room = 'room';

    public function label(): string
    {
        return match ($this) {
            self::Plant => 'Planta',
            self::Building => 'Edificio',
            self::Floor => 'Piso',
            self::Area => 'Área',
            self::Room => 'Sala / Cuarto',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Plant => 'building-2',
            self::Building => 'building',
            self::Floor => 'layers',
            self::Area => 'layout-grid',
            self::Room => 'door-open',
        };
    }
}
