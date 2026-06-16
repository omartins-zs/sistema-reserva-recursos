<?php

namespace App\Exports;

use App\Models\Reserva;
use App\Models\User;
use App\Services\MetricasReservaService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservasExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @param  array<string, mixed>  $filtros
     */
    public function __construct(
        private readonly array $filtros = [],
        private readonly ?User $usuario = null,
    ) {}

    public function query(): Builder
    {
        /** @var Builder<Reserva> $query */
        $query = app(MetricasReservaService::class)
            ->queryBase($this->filtros, $this->usuario)
            ->orderBy('data_reserva')
            ->orderBy('hora_inicio');

        return $query;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Recurso',
            'Tipo',
            'Data',
            'Horario',
            'Solicitante',
            'E-mail',
            'Departamento',
            'Motivo',
            'Status',
        ];
    }

    /**
     * @param  Reserva  $reserva
     * @return array<int, string>
     */
    public function map($reserva): array
    {
        return [
            $reserva->recurso->nome,
            $reserva->recurso->tipoRecurso->nome,
            $reserva->data_formatada,
            $reserva->periodo_formatado,
            $reserva->solicitante_nome,
            $reserva->solicitante_email,
            $reserva->departamento,
            $reserva->motivo,
            $reserva->status->label(),
        ];
    }
}
