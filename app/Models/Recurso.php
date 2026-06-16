<?php

namespace App\Models;

use App\Enums\RecursoStatus;
use Database\Factories\RecursoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $tipo_recurso_id
 * @property string $nome
 * @property string|null $descricao
 * @property string|null $codigo_patrimonio
 * @property string|null $localizacao
 * @property int|null $capacidade
 * @property string|null $placa
 * @property string|null $modelo
 * @property string|null $marca
 * @property RecursoStatus $status
 * @property bool $ativo
 * @property TipoRecurso $tipoRecurso
 */
class Recurso extends Model
{
    /** @use HasFactory<RecursoFactory> */
    use HasFactory;

    protected $table = 'recursos';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tipo_recurso_id',
        'nome',
        'descricao',
        'codigo_patrimonio',
        'localizacao',
        'capacidade',
        'placa',
        'modelo',
        'marca',
        'status',
        'ativo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacidade' => 'integer',
            'ativo' => 'boolean',
            'status' => RecursoStatus::class,
        ];
    }

    /**
     * @return BelongsTo<TipoRecurso, $this>
     */
    public function tipoRecurso(): BelongsTo
    {
        return $this->belongsTo(TipoRecurso::class, 'tipo_recurso_id');
    }

    /**
     * @return HasMany<Reserva, $this>
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeOperacionais(Builder $query): Builder
    {
        return $query
            ->where('ativo', true)
            ->where('status', RecursoStatus::DISPONIVEL->value);
    }

    public function isReservavel(): bool
    {
        return $this->ativo && $this->status === RecursoStatus::DISPONIVEL;
    }
}
