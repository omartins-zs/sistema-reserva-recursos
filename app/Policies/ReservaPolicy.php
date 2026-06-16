<?php

namespace App\Policies;

use App\Enums\ReservaStatus;
use App\Enums\UserRole;
use App\Models\Reserva;
use App\Models\User;

class ReservaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->canViewReports();
    }

    public function view(User $user, Reserva $reserva): bool
    {
        if ($user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return true;
        }

        if ($user->hasRole(UserRole::COLABORADOR)) {
            return $user->email === $reserva->solicitante_email;
        }

        return $user->canManageResourceType($reserva->recurso->tipoRecurso->nome);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMINISTRADOR, UserRole::TI, UserRole::FACILITIES);
    }

    public function update(User $user, Reserva $reserva): bool
    {
        if ($user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return true;
        }

        return $this->view($user, $reserva);
    }

    public function delete(User $user, Reserva $reserva): bool
    {
        if ($user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return true;
        }

        if ($user->hasRole(UserRole::COLABORADOR)) {
            return $user->email === $reserva->solicitante_email
                && in_array($reserva->status, [ReservaStatus::PENDENTE_APROVACAO, ReservaStatus::CONFIRMADO], true);
        }

        return $user->canManageResourceType($reserva->recurso->tipoRecurso->nome);
    }

    public function approve(User $user, Reserva $reserva): bool
    {
        return $reserva->status === ReservaStatus::PENDENTE_APROVACAO
            && $user->canApproveResourceType($reserva->recurso->tipoRecurso->nome);
    }

    public function reject(User $user, Reserva $reserva): bool
    {
        return $this->approve($user, $reserva);
    }

    public function restore(User $user, Reserva $reserva): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Reserva $reserva): bool
    {
        return $user->isAdmin();
    }
}
