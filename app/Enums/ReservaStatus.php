<?php

namespace App\Enums;

enum ReservaStatus: string
{
    case PENDENTE_APROVACAO = 'pendente_aprovacao';
    case CONFIRMADO = 'confirmado';
    case REJEITADO = 'rejeitado';
    case CANCELADO = 'cancelado';
    case FINALIZADO = 'finalizado';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE_APROVACAO => 'Pendente de aprovacao',
            self::CONFIRMADO => 'Confirmado',
            self::REJEITADO => 'Rejeitado',
            self::CANCELADO => 'Cancelado',
            self::FINALIZADO => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDENTE_APROVACAO => 'warning',
            self::CONFIRMADO => 'success',
            self::REJEITADO => 'danger',
            self::CANCELADO => 'danger',
            self::FINALIZADO => 'gray',
        };
    }

    /**
     * @return list<string>
     */
    public static function bloqueiaAgenda(): array
    {
        return [
            self::PENDENTE_APROVACAO->value,
            self::CONFIRMADO->value,
        ];
    }
}
