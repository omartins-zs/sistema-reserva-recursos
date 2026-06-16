<?php

namespace App\Models;

use App\Enums\ReservaStatus;
use App\Services\FluxoAprovacaoReservaService;
use App\Services\ReservaDisponibilidadeService;
use Database\Factories\ReservaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $recurso_id
 * @property string $solicitante_nome
 * @property string $solicitante_email
 * @property int|null $departamento_id
 * @property string $departamento
 * @property string $motivo
 * @property string|null $participantes
 * @property Carbon|string $data_reserva
 * @property string $hora_inicio
 * @property string $hora_fim
 * @property ReservaStatus $status
 * @property int|null $avaliado_por_id
 * @property Carbon|string|null $avaliado_em
 * @property string|null $motivo_reprovacao
 * @property string|null $observacoes
 * @property Recurso $recurso
 * @property Departamento|null $departamentoRelacionamento
 * @property User|null $avaliadoPor
 */
class Reserva extends Model
{
    /** @use HasFactory<ReservaFactory> */
    use HasFactory;

    protected $table = 'reservas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'recurso_id',
        'solicitante_nome',
        'solicitante_email',
        'departamento_id',
        'departamento',
        'motivo',
        'participantes',
        'data_reserva',
        'hora_inicio',
        'hora_fim',
        'status',
        'avaliado_por_id',
        'avaliado_em',
        'motivo_reprovacao',
        'observacoes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_reserva' => 'date',
            'avaliado_em' => 'datetime',
            'status' => ReservaStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $reserva): void {
            if ($reserva->departamento_id) {
                $departamento = $reserva->relationLoaded('departamentoRelacionamento') && $reserva->departamentoRelacionamento instanceof Departamento
                    ? $reserva->departamentoRelacionamento
                    : Departamento::query()->find($reserva->departamento_id);

                if ($departamento instanceof Departamento) {
                    $reserva->departamento = $departamento->nome;
                }
            }

            if (! in_array($reserva->status, [ReservaStatus::PENDENTE_APROVACAO, ReservaStatus::CONFIRMADO], true)) {
                return;
            }

            $recurso = ($reserva->relationLoaded('recurso') && $reserva->recurso instanceof Recurso)
                ? $reserva->recurso
                : Recurso::query()->findOrFail($reserva->recurso_id);

            app(ReservaDisponibilidadeService::class)->validarRecursoReservavel($recurso);
            app(ReservaDisponibilidadeService::class)->validarDisponibilidade(
                $recurso->id,
                Carbon::parse($reserva->data_reserva)->toDateString(),
                (string) $reserva->hora_inicio,
                (string) $reserva->hora_fim,
                $reserva->exists ? $reserva->id : null,
            );
        });
    }

    /**
     * @return BelongsTo<Recurso, $this>
     */
    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class);
    }

    /**
     * @return BelongsTo<Departamento, $this>
     */
    public function departamentoRelacionamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function avaliadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avaliado_por_id');
    }

    /**
     * @return HasMany<HistoricoReserva, $this>
     */
    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoReserva::class);
    }

    public function getPeriodoFormatadoAttribute(): string
    {
        return sprintf('%s as %s', substr($this->hora_inicio, 0, 5), substr($this->hora_fim, 0, 5));
    }

    public function getDataFormatadaAttribute(): string
    {
        return Carbon::parse($this->data_reserva)->format('d/m/Y');
    }

    /**
     * @return list<string>
     */
    public function getParticipantesListaAttribute(): array
    {
        return collect(explode(';', (string) $this->participantes))
            ->map(fn (string $email): string => trim($email))
            ->filter()
            ->values()
            ->all();
    }

    public function getResponsavelAprovacaoAttribute(): string
    {
        return app(FluxoAprovacaoReservaService::class)->responsavelPorReserva($this);
    }
}
