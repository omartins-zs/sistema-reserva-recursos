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

    private const ACTIVE_CACHE_KEY = 'departamentos.ativos.v2';

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
        static::saved(fn () => self::forgetActiveCache());
        static::deleted(fn () => self::forgetActiveCache());
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
        $departamentos = Cache::get(self::ACTIVE_CACHE_KEY);

        if (! self::payloadValido($departamentos)) {
            $departamentos = self::query()
                ->with('gestor')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get()
                ->map(fn (self $departamento): array => [
                    'attributes' => $departamento->attributesToArray(),
                    'gestor' => $departamento->gestor?->attributesToArray(),
                ])
                ->all();

            Cache::forever(self::ACTIVE_CACHE_KEY, $departamentos);
        }

        /** @var array<int, array{attributes: array<string, mixed>, gestor: array<string, mixed>|null}> $departamentos */
        $collection = self::hydrate(array_map(
            fn (array $item): array => $item['attributes'],
            $departamentos,
        ));

        $collection->each(function (self $departamento, int $index) use ($departamentos): void {
            $gestor = $departamentos[$index]['gestor'];

            if (is_array($gestor)) {
                $departamento->setRelation('gestor', (new User)->newFromBuilder($gestor));
            }
        });

        return $collection;
    }

    private static function forgetActiveCache(): void
    {
        Cache::forget('departamentos.ativos');
        Cache::forget(self::ACTIVE_CACHE_KEY);
    }

    private static function payloadValido(mixed $payload): bool
    {
        return is_array($payload)
            && collect($payload)->every(function (mixed $item): bool {
                return is_array($item)
                    && isset($item['attributes'])
                    && is_array($item['attributes'])
                    && array_key_exists('gestor', $item)
                    && (is_array($item['gestor']) || $item['gestor'] === null);
            });
    }
}
