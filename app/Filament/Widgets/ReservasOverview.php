<?php

namespace App\Filament\Widgets;

use App\Services\MetricasReservaService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservasOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Pulso operacional';

    protected ?string $description = 'Indicadores rapidos da fila de reservas e aprovacoes.';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $usuario = auth()->user();
        $metricas = app(MetricasReservaService::class)->resumo([
            'data_inicial' => now()->startOfMonth()->toDateString(),
            'data_final' => now()->endOfMonth()->toDateString(),
        ], $usuario);

        $labelPendente = $usuario?->canApproveReservations()
            ? 'Aguardando sua analise'
            : 'Pendentes no periodo';

        return [
            Stat::make($labelPendente, (string) $metricas['pendentes'])
                ->color('warning')
                ->description('Solicitacoes ainda sem decisao')
                ->chart([3, 5, 4, 7, 6, 9, max(1, (int) $metricas['pendentes'])]),
            Stat::make('Confirmadas no periodo', (string) $metricas['confirmadas'])
                ->color('success')
                ->description('Reservas liberadas para uso')
                ->chart([2, 4, 5, 7, 8, 8, max(1, (int) $metricas['confirmadas'])]),
            Stat::make('Rejeitadas + canceladas', (string) ($metricas['rejeitadas'] + $metricas['canceladas']))
                ->color('danger')
                ->description('Pedidos encerrados sem uso')
                ->chart([1, 1, 2, 2, 3, 2, max(1, (int) ($metricas['rejeitadas'] + $metricas['canceladas']))]),
            Stat::make('Taxa de ocupacao', number_format($metricas['taxa_ocupacao'], 1, ',', '.').'%')
                ->color('primary')
                ->description('Baseada apenas em reservas confirmadas')
                ->chart([12, 18, 22, 21, 25, 28, max(1, (int) round($metricas['taxa_ocupacao']))]),
        ];
    }
}
