<?php

namespace App\Models;

use Database\Factories\DepartamentoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $sigla
 * @property string|null $descricao
 * @property bool $ativo
 * @property int|null $gestor_user_id
 * @property User|null $gestor
 */
class Departamento extends Model
{
    /** @use HasFactory<DepartamentoFactory> */
    use HasFactory;

    protected $table = 'departamentos';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
        'ativo',
        'gestor_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('departamentos.ativos'));
        static::deleted(fn () => Cache::forget('departamentos.ativos'));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function gestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestor_user_id');
    }

    /**
     * @return HasMany<User, $this>
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Reserva, $this>
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * @return Collection<int, self>
     */
    public static function ativosEmCache(): Collection
    {
        /** @var Collection<int, self> $departamentos */
        $departamentos = Cache::rememberForever(
            'departamentos.ativos',
            fn () => self::query()->with('gestor')->where('ativo', true)->orderBy('nome')->get()
        );

        return $departamentos;
    }
}
