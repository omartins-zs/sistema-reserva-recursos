<?php

namespace App\Filament\Resources\Departamentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Departamento')
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sigla')
                            ->label('Sigla')
                            ->maxLength(20),
                        Select::make('gestor_user_id')
                            ->label('Gestor responsavel')
                            ->relationship('gestor', 'name')
                            ->searchable()
                            ->preload(),
                        Toggle::make('ativo')
                            ->label('Ativo')
                            ->default(true),
                        Textarea::make('descricao')
                            ->label('Descricao')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
