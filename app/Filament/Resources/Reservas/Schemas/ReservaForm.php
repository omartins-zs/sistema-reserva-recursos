<?php

namespace App\Filament\Resources\Reservas\Schemas;

use App\Enums\ReservaStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reserva')
                    ->schema([
                        Select::make('recurso_id')
                            ->relationship('recurso', 'nome')
                            ->label('Recurso')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->options(collect(ReservaStatus::cases())->mapWithKeys(fn (ReservaStatus $status) => [$status->value => $status->label()])->all())
                            ->default(ReservaStatus::CONFIRMADO->value)
                            ->required(),
                        DatePicker::make('data_reserva')
                            ->required(),
                        TimePicker::make('hora_inicio')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('hora_fim')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('solicitante_nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('solicitante_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('departamento')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('motivo')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('participantes')
                            ->helperText('Use ; para separar os e-mails.')
                            ->columnSpanFull(),
                        Textarea::make('observacoes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
