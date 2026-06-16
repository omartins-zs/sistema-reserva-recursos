<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Recurso;
use App\Models\User;

class RecursoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMINISTRADOR, UserRole::RH, UserRole::TI, UserRole::FACILITIES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recurso $recurso): bool
    {
        return $user->isAdmin() || $user->hasRole(UserRole::RH) || $user->canManageResourceType($recurso->tipoRecurso->nome);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMINISTRADOR, UserRole::TI, UserRole::FACILITIES);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recurso $recurso): bool
    {
        return $user->isAdmin() || $user->canManageResourceType($recurso->tipoRecurso->nome);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recurso $recurso): bool
    {
        return $this->update($user, $recurso);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recurso $recurso): bool
    {
        return $this->update($user, $recurso);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recurso $recurso): bool
    {
        return $user->isAdmin();
    }
}
