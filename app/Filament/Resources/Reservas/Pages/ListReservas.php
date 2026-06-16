<?php

namespace App\Filament\Resources\Reservas\Pages;

use App\Enums\ReservaStatus;
use App\Filament\Resources\Reservas\ReservaResource;
use App\Filament\Widgets\ReservasOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservas extends ListRecords
{
    protected static string $resource = ReservaResource::class;

    protected static ?string $title = 'Fila de reservas';

    protected static ?string $breadcrumb = 'Reservas';

    public function getTabs(): array
    {
        $query = ReservaResource::getEloquentQuery();

        return [
            'pendentes' => Tab::make('Pendentes')
                ->badge((string) (clone $query)->where('status', ReservaStatus::PENDENTE_APROVACAO->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ReservaStatus::PENDENTE_APROVACAO->value)),
            'confirmadas' => Tab::make('Confirmadas')
                ->badge((string) (clone $query)->where('status', ReservaStatus::CONFIRMADO->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ReservaStatus::CONFIRMADO->value)),
            'encerradas' => Tab::make('Reprovadas e canceladas')
                ->badge((string) (clone $query)->whereIn('status', [ReservaStatus::REJEITADO->value, ReservaStatus::CANCELADO->value])->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [ReservaStatus::REJEITADO->value, ReservaStatus::CANCELADO->value])),
            'todas' => Tab::make('Todas'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return (auth()->user()?->role?->canApproveReservations() ?? false) ? 'pendentes' : 'todas';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReservasOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nova reserva interna'),
        ];
    }
}
