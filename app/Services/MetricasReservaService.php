<?php

namespace App\Services;

use App\Enums\ReservaStatus;
use App\Enums\UserRole;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MetricasReservaService
{
    /**
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    public function resumo(array $filtros, ?User $usuario = null): array
    {
        $query = $this->queryBase($filtros, $usuario);

        $total = (clone $query)->count();
        $pendentes = (clone $query)->where('status', ReservaStatus::PENDENTE_APROVACAO->value)->count();
        $confirmadas = (clone $query)->where('status', ReservaStatus::CONFIRMADO->value)->count();
        $rejeitadas = (clone $query)->where('status', ReservaStatus::REJEITADO->value)->count();
        $canceladas = (clone $query)->where('status', ReservaStatus::CANCELADO->value)->count();

        $minutosReservados = (clone $query)
            ->where('status', ReservaStatus::CONFIRMADO->value)
            ->get(['hora_inicio', 'hora_fim'])
            ->sum(function ($reserva): int {
                $inicio = Carbon::createFromFormat('H:i:s', $reserva->hora_inicio);
                $fim = Carbon::createFromFormat('H:i:s', $reserva->hora_fim);

                return (int) $fim->diffInMinutes($inicio);
            });

        $recursosUnicos = max(1, (int) (clone $query)->distinct('recurso_id')->count('recurso_id'));
        $diasConsiderados = max(1, $this->diasDoPeriodo($filtros));
        $capacidadeTotalMinutos = $recursosUnicos * $diasConsiderados * 12 * 60;
        $taxaOcupacao = round(($minutosReservados / $capacidadeTotalMinutos) * 100, 1);

        /** @var Collection<int, object> $recursosMaisUtilizados */
        $recursosMaisUtilizados = (clone $query)
            ->select('recursos.nome')
            ->selectRaw('COUNT(reservas.id) as total')
            ->join('recursos', 'recursos.id', '=', 'reservas.recurso_id')
            ->groupBy('recursos.nome')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        /** @var Collection<int, object> $porDepartamento */
        $porDepartamento = (clone $query)
            ->select('departamento')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('departamento')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'total' => $total,
            'pendentes' => $pendentes,
            'confirmadas' => $confirmadas,
            'rejeitadas' => $rejeitadas,
            'canceladas' => $canceladas,
            'taxa_ocupacao' => $taxaOcupacao,
            'recursos_mais_utilizados' => $recursosMaisUtilizados,
            'por_departamento' => $porDepartamento,
        ];
    }

    /**
     * @param  array<string, mixed>  $filtros
     * @return Builder<Reserva>
     */
    public function queryBase(array $filtros, ?User $usuario = null): Builder
    {
        $query = Reserva::query()
            ->with(['recurso.tipoRecurso', 'avaliadoPor', 'departamentoRelacionamento.gestor'])
            ->when($filtros['tipo_recurso_id'] ?? null, function (Builder $query, mixed $tipoId): void {
                $query->whereHas('recurso', fn (Builder $resourceQuery) => $resourceQuery->where('tipo_recurso_id', $tipoId));
            })
            ->when($filtros['recurso_id'] ?? null, fn (Builder $query, mixed $recursoId) => $query->where('recurso_id', $recursoId))
            ->when($filtros['solicitante'] ?? null, fn (Builder $query, mixed $solicitante) => $query->where('solicitante_nome', 'like', '%'.trim((string) $solicitante).'%'))
            ->when($filtros['departamento_id'] ?? null, fn (Builder $query, mixed $departamentoId) => $query->where('departamento_id', $departamentoId))
            ->when($filtros['data_inicial'] ?? null, fn (Builder $query, mixed $data) => $query->whereDate('data_reserva', '>=', $data))
            ->when($filtros['data_final'] ?? null, fn (Builder $query, mixed $data) => $query->whereDate('data_reserva', '<=', $data))
            ->when($filtros['status'] ?? null, fn (Builder $query, mixed $status) => $query->where('status', $status));

        if (! $usuario || $usuario->isAdmin() || $usuario->hasRole(UserRole::RH)) {
            return $query;
        }

        if ($usuario->canApproveReservations()) {
            return $query->where(function (Builder $builder) use ($usuario): void {
                $builder
                    ->whereIn('departamento_id', $usuario->departamentosGerenciadosIds())
                    ->orWhere('solicitante_email', $usuario->email);
            });
        }

        return $query->where('solicitante_email', $usuario->email);
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    private function diasDoPeriodo(array $filtros): int
    {
        $dataInicial = $filtros['data_inicial'] ?? now()->toDateString();
        $dataFinal = $filtros['data_final'] ?? $dataInicial;

        return (int) Carbon::parse($dataInicial)->diffInDays(Carbon::parse($dataFinal)) + 1;
    }
}
