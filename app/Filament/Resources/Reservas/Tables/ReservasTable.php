<?php

namespace App\Filament\Resources\Reservas\Tables;

use App\Actions\ApproveReservaAction;
use App\Actions\CancelReservaAction;
use App\Actions\RejectReservaAction;
use App\Enums\ReservaStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('data_reserva', 'desc')
            ->columns([
                TextColumn::make('recurso.nome')
                    ->label('Recurso')
                    ->description(fn ($record): string => $record->recurso->tipoRecurso->nome)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('data_formatada')
                    ->label('Data')
                    ->sortable(),
                TextColumn::make('periodo_formatado')
                    ->label('Horario'),
                TextColumn::make('solicitante_nome')
                    ->label('Solicitante')
                    ->description(fn ($record): string => "{$record->solicitante_email} | {$record->departamento}")
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
                TextColumn::make('avaliadoPor.name')
                    ->label('Ultima avaliacao')
                    ->placeholder('Aguardando')
                    ->description(fn ($record): ?string => $record->avaliado_em?->format('d/m/Y H:i'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ReservaStatus::cases())->mapWithKeys(fn (ReservaStatus $status) => [$status->value => $status->label()])->all()),
                SelectFilter::make('recurso_id')
                    ->relationship('recurso', 'nome')
                    ->label('Recurso'),
            ])
            ->recordActions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->visible(fn ($record) => auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->modalHeading('Aprovar solicitacao')
                    ->modalDescription('A reserva sera confirmada e o solicitante sera avisado.')
                    ->action(fn ($record) => app(ApproveReservaAction::class)->execute($record, auth()->user())),
                Action::make('reprovar')
                    ->label('Reprovar')
                    ->color('warning')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->visible(fn ($record) => auth()->user()?->can('reject', $record))
                    ->schema([
                        Textarea::make('motivo')
                            ->label('Motivo da reprovacao')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->action(fn ($record, array $data) => app(RejectReservaAction::class)->execute($record, auth()->user(), (string) $data['motivo'])),
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->visible(fn ($record) => auth()->user()?->can('delete', $record) && in_array($record->status, [ReservaStatus::PENDENTE_APROVACAO, ReservaStatus::CONFIRMADO], true))
                    ->requiresConfirmation()
                    ->action(fn ($record) => app(CancelReservaAction::class)->execute($record, auth()->user())),
                EditAction::make()
                    ->label('Detalhes'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Excluir selecionadas'),
                ])
                    ->label('Acoes em lote'),
            ]);
    }
}
