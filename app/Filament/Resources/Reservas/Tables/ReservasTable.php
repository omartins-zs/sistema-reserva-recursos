<?php

namespace App\Filament\Resources\Reservas\Tables;

use App\Actions\CancelReservaAction;
use App\Enums\ReservaStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recurso.nome')
                    ->label('Recurso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recurso.tipoRecurso.nome')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('data_formatada')
                    ->label('Data'),
                TextColumn::make('periodo_formatado')
                    ->label('Horario'),
                TextColumn::make('solicitante_nome')
                    ->label('Solicitante')
                    ->searchable(),
                TextColumn::make('departamento')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'finalizado' => 'Finalizado',
                    ]),
                SelectFilter::make('recurso_id')
                    ->relationship('recurso', 'nome')
                    ->label('Recurso'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('cancelar')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->visible(fn ($record) => $record->status === ReservaStatus::CONFIRMADO)
                    ->requiresConfirmation()
                    ->action(fn ($record) => app(CancelReservaAction::class)->execute($record, auth()->user())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
