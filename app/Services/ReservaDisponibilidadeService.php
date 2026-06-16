<?php

namespace App\Services;

use App\Enums\RecursoStatus;
use App\Enums\ReservaStatus;
use App\Models\Recurso;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ReservaDisponibilidadeService
{
    /**
     * @return Collection<int, Reserva>
     */
    public function agendaDoDia(int $recursoId, string $dataReserva): Collection
    {
        return Reserva::query()
            ->with('recurso.tipoRecurso')
            ->where('recurso_id', $recursoId)
            ->whereDate('data_reserva', $dataReserva)
            ->orderBy('hora_inicio')
            ->get();
    }

    public function estaDisponivel(
        int $recursoId,
        string $dataReserva,
        string $horaInicio,
        string $horaFim,
        ?int $ignorarReservaId = null,
    ): bool {
        return ! $this->queryConflitos($recursoId, $dataReserva, $horaInicio, $horaFim, $ignorarReservaId)->exists();
    }

    public function validarRecursoReservavel(Recurso $recurso): void
    {
        if ($recurso->status === RecursoStatus::MANUTENCAO) {
            throw ValidationException::withMessages([
                'recurso_id' => 'Este recurso está em manutenção.',
            ]);
        }

        if (! $recurso->ativo || $recurso->status === RecursoStatus::INATIVO) {
            throw ValidationException::withMessages([
                'recurso_id' => 'Este recurso está inativo e não pode ser reservado.',
            ]);
        }
    }

    public function validarDisponibilidade(
        int $recursoId,
        string $dataReserva,
        string $horaInicio,
        string $horaFim,
        ?int $ignorarReservaId = null,
    ): void {
        if (! $this->estaDisponivel($recursoId, $dataReserva, $horaInicio, $horaFim, $ignorarReservaId)) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'O recurso está indisponível nesse horário.',
            ]);
        }
    }

    private function queryConflitos(
        int $recursoId,
        string $dataReserva,
        string $horaInicio,
        string $horaFim,
        ?int $ignorarReservaId = null,
    ): Builder {
        return Reserva::query()
            ->where('recurso_id', $recursoId)
            ->whereDate('data_reserva', $dataReserva)
            ->where('status', ReservaStatus::CONFIRMADO->value)
            ->when($ignorarReservaId, fn (Builder $query) => $query->whereKeyNot($ignorarReservaId))
            ->where('hora_inicio', '<', $horaFim)
            ->where('hora_fim', '>', $horaInicio);
    }
}
