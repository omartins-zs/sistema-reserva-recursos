<?php

namespace App\Enums;

enum RecursoStatus: string
{
    case DISPONIVEL = 'disponivel';
    case MANUTENCAO = 'manutencao';
    case INATIVO = 'inativo';

    public function label(): string
    {
        return match ($this) {
            self::DISPONIVEL => 'Disponível',
            self::MANUTENCAO => 'Manutenção',
            self::INATIVO => 'Inativo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DISPONIVEL => 'success',
            self::MANUTENCAO => 'warning',
            self::INATIVO => 'danger',
        };
    }
}
