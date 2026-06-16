<?php

namespace App\Filament\Resources\TipoRecursos\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TipoRecursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tipo de recurso')
                    ->schema([
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('icone')
                            ->label('Classe do icone')
                            ->helperText('Ex.: fa-solid fa-laptop')
                            ->maxLength(255),
                        Toggle::make('ativo')
                            ->default(true),
                        Textarea::make('descricao')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
