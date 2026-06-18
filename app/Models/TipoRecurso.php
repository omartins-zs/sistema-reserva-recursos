<?php

namespace App\Models;

use Database\Factories\TipoRecursoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $icone
 * @property string|null $descricao
 * @property bool $ativo
 */
class TipoRecurso extends Model
{
    /** @use HasFactory<TipoRecursoFactory> */
    use HasFactory;

    private const ACTIVE_CACHE_KEY = 'tipos-recursos.ativos.v2';

    protected $table = 'tipos_recursos';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'icone',
        'descricao',
        'ativo',
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
     * @return HasMany<Recurso, $this>
     */
    public function recursos(): HasMany
    {
        return $this->hasMany(Recurso::class, 'tipo_recurso_id');
    }

    /**
     * @return Collection<int, self>
     */
    public static function ativosEmCache(): Collection
    {
        $tipos = Cache::get(self::ACTIVE_CACHE_KEY);

        if (! self::payloadValido($tipos)) {
            $tipos = self::query()
                ->where('ativo', true)
                ->orderBy('nome')
                ->get()
                ->map(fn (self $tipo): array => $tipo->attributesToArray())
                ->all();

            Cache::forever(self::ACTIVE_CACHE_KEY, $tipos);
        }

        /** @var Collection<int, self> $tiposCollection */
        $tiposCollection = self::hydrate($tipos);

        return $tiposCollection;
    }

    private static function forgetActiveCache(): void
    {
        Cache::forget('tipos-recursos.ativos');
        Cache::forget(self::ACTIVE_CACHE_KEY);
    }

    private static function payloadValido(mixed $payload): bool
    {
        return is_array($payload)
            && collect($payload)->every(fn (mixed $item): bool => is_array($item));
    }
}
