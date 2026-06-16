<?php

namespace App\Filament\Resources\Recursos\Schemas;

use App\Enums\RecursoStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados principais')
                    ->schema([
                        Select::make('tipo_recurso_id')
                            ->relationship('tipoRecurso', 'nome')
                            ->label('Tipo de recurso')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->options(collect(RecursoStatus::cases())->mapWithKeys(fn (RecursoStatus $status) => [$status->value => $status->label()])->all())
                            ->default(RecursoStatus::DISPONIVEL->value)
                            ->required(),
                        Toggle::make('ativo')
                            ->default(true),
                        Textarea::make('descricao')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Detalhes operacionais')
                    ->schema([
                        TextInput::make('codigo_patrimonio')
                            ->maxLength(255),
                        TextInput::make('localizacao')
                            ->maxLength(255),
                        TextInput::make('capacidade')
                            ->numeric(),
                        TextInput::make('placa')
                            ->maxLength(15),
                        TextInput::make('modelo')
                            ->maxLength(255),
                        TextInput::make('marca')
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }
}
