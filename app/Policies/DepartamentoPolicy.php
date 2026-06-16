<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Departamento;
use App\Models\User;

class DepartamentoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole(UserRole::RH);
    }

    public function view(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, Departamento $departamento): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Departamento $departamento): bool
    {
        return $user->isAdmin();
    }
}
