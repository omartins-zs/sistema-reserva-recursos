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
        static::saved(fn () => Cache::forget('tipos-recursos.ativos'));
        static::deleted(fn () => Cache::forget('tipos-recursos.ativos'));
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
        /** @var Collection<int, self> $tipos */
        $tipos = Cache::rememberForever(
            'tipos-recursos.ativos',
            fn () => self::query()->where('ativo', true)->orderBy('nome')->get()
        );

        return $tipos;
    }
}
