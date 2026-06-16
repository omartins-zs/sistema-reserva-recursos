<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMINISTRADOR = 'administrador';
    case RH = 'rh';
    case TI = 'ti';
    case FACILITIES = 'facilities';
    case COLABORADOR = 'colaborador';

    public function label(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::RH => 'RH',
            self::TI => 'TI',
            self::FACILITIES => 'Facilities',
            self::COLABORADOR => 'Colaborador',
        };
    }

    /**
     * @return array<int, string>
     */
    public function allowedResourceTypes(): array
    {
        return match ($this) {
            self::ADMINISTRADOR, self::RH => ['Sala', 'Projetor', 'Carro', 'Notebook'],
            self::TI => ['Projetor', 'Notebook'],
            self::FACILITIES => ['Sala', 'Carro'],
            self::COLABORADOR => [],
        };
    }

    public function canManageResources(): bool
    {
        return in_array($this, [self::ADMINISTRADOR, self::TI, self::FACILITIES], true);
    }

    public function canApproveReservations(): bool
    {
        return in_array($this, [self::ADMINISTRADOR, self::TI, self::FACILITIES], true);
    }

    public function canViewReports(): bool
    {
        return true;
    }
}
