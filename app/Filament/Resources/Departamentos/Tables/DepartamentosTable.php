<?php

namespace App\Filament\Resources\Departamentos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartamentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sigla')
                    ->label('Sigla')
                    ->sortable(),
                TextColumn::make('gestor.name')
                    ->label('Gestor')
                    ->placeholder('Nao definido')
                    ->searchable(),
                TextColumn::make('usuarios_count')
                    ->label('Usuarios')
                    ->counts('usuarios'),
                TextColumn::make('reservas_count')
                    ->label('Reservas')
                    ->counts('reservas'),
                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
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
