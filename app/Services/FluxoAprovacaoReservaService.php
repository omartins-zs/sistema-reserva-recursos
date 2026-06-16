<?php

namespace App\Services;

use App\Models\Departamento;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FluxoAprovacaoReservaService
{
    public function responsavelPorDepartamento(?Departamento $departamento): string
    {
        if (! $departamento instanceof Departamento) {
            return 'Gestor do departamento ou Administracao';
        }

        if ($departamento->gestor instanceof User) {
            return "{$departamento->gestor->name} ({$departamento->nome})";
        }

        return "Gestor de {$departamento->nome} ou Administracao";
    }

    public function responsavelPorReserva(Reserva $reserva): string
    {
        $reserva->loadMissing('departamentoRelacionamento.gestor');

        return $this->responsavelPorDepartamento($reserva->departamentoRelacionamento);
    }

    /**
     * @return Collection<int, User>
     */
    public function usuariosAprovadores(Reserva $reserva): Collection
    {
        $reserva->loadMissing('departamentoRelacionamento.gestor');

        $query = User::query()
            ->where(function ($query) use ($reserva): void {
                $query->where('role', 'administrador');

                if ($reserva->departamentoRelacionamento?->gestor_user_id) {
                    $query->orWhere('id', $reserva->departamentoRelacionamento->gestor_user_id);
                }
            })
            ->orderBy('name');

        /** @var Collection<int, User> $usuarios */
        $usuarios = $query->get();

        return $usuarios;
    }

    public function usuarioPodeAprovar(?User $usuario, Reserva $reserva): bool
    {
        if (! $usuario instanceof User) {
            return false;
        }

        if ($usuario->isAdmin()) {
            return true;
        }

        return $usuario->gerenciaDepartamento($reserva->departamento_id);
    }
}
