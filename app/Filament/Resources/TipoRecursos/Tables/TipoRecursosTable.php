<?php

namespace App\Filament\Resources\TipoRecursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TipoRecursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('icone')
                    ->label('Icone')
                    ->toggleable(),
                TextColumn::make('descricao')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('recursos_count')
                    ->counts('recursos')
                    ->label('Recursos'),
                IconColumn::make('ativo')
                    ->boolean(),
            ])
            ->filters([
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
