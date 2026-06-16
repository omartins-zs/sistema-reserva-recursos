<?php

namespace App\Filament\Resources\Recursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RecursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipoRecurso.nome')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('localizacao')
                    ->toggleable(),
                TextColumn::make('modelo')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
                IconColumn::make('ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('tipo_recurso_id')
                    ->relationship('tipoRecurso', 'nome')
                    ->label('Tipo'),
                SelectFilter::make('status')
                    ->options([
                        'disponivel' => 'Disponivel',
                        'manutencao' => 'Manutencao',
                        'inativo' => 'Inativo',
                    ]),
                TernaryFilter::make('ativo'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
