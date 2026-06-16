<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FluxoAprovacaoReservaService
{
    /**
     * @return list<UserRole>
     */
    public function perfisAprovadoresPorTipo(?string $tipoNome): array
    {
        return match ($tipoNome) {
            'Notebook', 'Projetor' => [UserRole::ADMINISTRADOR, UserRole::TI],
            'Sala', 'Carro' => [UserRole::ADMINISTRADOR, UserRole::FACILITIES],
            default => [UserRole::ADMINISTRADOR],
        };
    }

    public function responsavelPorTipo(?string $tipoNome): string
    {
        return match ($tipoNome) {
            'Notebook', 'Projetor' => 'Coordenacao de TI',
            'Sala', 'Carro' => 'Coordenacao de Facilities',
            default => 'Administracao',
        };
    }

    /**
     * @return Collection<int, User>
     */
    public function usuariosAprovadores(Recurso $recurso): Collection
    {
        $roles = collect($this->perfisAprovadoresPorTipo($recurso->tipoRecurso->nome))
            ->map(fn (UserRole $role): string => $role->value)
            ->all();

        return User::query()
            ->whereIn('role', $roles)
            ->orderBy('name')
            ->get();
    }

    public function usuarioPodeAprovar(?User $usuario, Reserva $reserva): bool
    {
        if (! $usuario instanceof User) {
            return false;
        }

        if ($usuario->isAdmin()) {
            return true;
        }

        return $usuario->canApproveResourceType($reserva->recurso->tipoRecurso->nome);
    }
}
