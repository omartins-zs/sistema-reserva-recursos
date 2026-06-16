<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Reserva;
use App\Models\User;

class ReservaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->canViewReports();
    }

    /**
     * Determine whether the user can view the model.
     */
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

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMINISTRADOR, UserRole::RH, UserRole::TI, UserRole::FACILITIES);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        if ($user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return true;
        }

        return $this->view($user, $reserva);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        return $this->update($user, $reserva);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reserva $reserva): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reserva $reserva): bool
    {
        return $user->isAdmin();
    }
}
