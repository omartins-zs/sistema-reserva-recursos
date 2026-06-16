<?php

namespace App\Models;

use App\Enums\ReservaStatus;
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
 * @property string $departamento
 * @property string $motivo
 * @property string|null $participantes
 * @property Carbon|string $data_reserva
 * @property string $hora_inicio
 * @property string $hora_fim
 * @property ReservaStatus $status
 * @property string|null $observacoes
 * @property Recurso $recurso
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
        'departamento',
        'motivo',
        'participantes',
        'data_reserva',
        'hora_inicio',
        'hora_fim',
        'status',
        'observacoes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_reserva' => 'date',
            'status' => ReservaStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $reserva): void {
            if ($reserva->status !== ReservaStatus::CONFIRMADO) {
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
     * @return HasMany<HistoricoReserva, $this>
     */
    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoReserva::class);
    }

    public function getPeriodoFormatadoAttribute(): string
    {
        return sprintf('%s às %s', substr($this->hora_inicio, 0, 5), substr($this->hora_fim, 0, 5));
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
}
