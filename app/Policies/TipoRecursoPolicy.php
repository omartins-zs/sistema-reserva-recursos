<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\TipoRecurso;
use App\Models\User;

class TipoRecursoPolicy
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
    public function view(User $user, TipoRecurso $tipoRecurso): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoRecurso $tipoRecurso): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoRecurso $tipoRecurso): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoRecurso $tipoRecurso): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoRecurso $tipoRecurso): bool
    {
        return $user->isAdmin();
    }
}
