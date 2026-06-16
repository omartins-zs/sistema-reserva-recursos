<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property UserRole $role
 * @property int|null $departamento_id
 * @property string $password
 * @property Departamento|null $departamento
 */
#[Fillable(['name', 'email', 'role', 'departamento_id', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role instanceof UserRole;
    }

    /**
     * @return BelongsTo<Departamento, $this>
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * @return HasMany<Departamento, $this>
     */
    public function departamentosGerenciados(): HasMany
    {
        return $this->hasMany(Departamento::class, 'gestor_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMINISTRADOR;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function canManageResourceType(?string $tipoNome): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($tipoNome === null || $tipoNome === '') {
            return false;
        }

        return in_array($tipoNome, $this->role->allowedResourceTypes(), true);
    }

    /**
     * @return list<int>
     */
    public function departamentosGerenciadosIds(): array
    {
        return $this->departamentosGerenciados()
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function gerenciaDepartamento(?int $departamentoId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $departamentoId) {
            return false;
        }

        return in_array($departamentoId, $this->departamentosGerenciadosIds(), true);
    }

    public function canApproveReservations(): bool
    {
        return $this->isAdmin() || $this->departamentosGerenciados()->exists();
    }
}
