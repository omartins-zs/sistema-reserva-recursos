<?php

namespace App\Enums;

enum ReservaStatus: string
{
    case CONFIRMADO = 'confirmado';
    case CANCELADO = 'cancelado';
    case FINALIZADO = 'finalizado';

    public function label(): string
    {
        return match ($this) {
            self::CONFIRMADO => 'Confirmado',
            self::CANCELADO => 'Cancelado',
            self::FINALIZADO => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CONFIRMADO => 'success',
            self::CANCELADO => 'danger',
            self::FINALIZADO => 'gray',
        };
    }
}
