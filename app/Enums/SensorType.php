<?php

namespace App\Enums;

enum SensorType: string
{
    case Temperature = 'temperature';
    case Vibration = 'vibration';
    case Pressure = 'pressure';
    case Humidity = 'humidity';
    case Current = 'current';
    case Voltage = 'voltage';
    case Flow = 'flow';
    case Level = 'level';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Temperature => 'Temperatura',
            self::Vibration => 'Vibración',
            self::Pressure => 'Presión',
            self::Humidity => 'Humedad',
            self::Current => 'Corriente',
            self::Voltage => 'Voltaje',
            self::Flow => 'Flujo',
            self::Level => 'Nivel',
            self::Custom => 'Personalizado',
        };
    }

    public function unit(): string
    {
        return match ($this) {
            self::Temperature => '°C',
            self::Vibration => 'mm/s',
            self::Pressure => 'bar',
            self::Humidity => '%',
            self::Current => 'A',
            self::Voltage => 'V',
            self::Flow => 'L/min',
            self::Level => 'cm',
            self::Custom => 'u',
        };
    }
}
