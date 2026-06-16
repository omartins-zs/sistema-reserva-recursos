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
                    ->description('Use este formulario para ajustes internos, reservas assistidas e tratamento das aprovacoes.')
                    ->schema([
                        Select::make('recurso_id')
                            ->relationship('recurso', 'nome')
                            ->label('Recurso')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(ReservaStatus::cases())->mapWithKeys(fn (ReservaStatus $status) => [$status->value => $status->label()])->all())
                            ->default(ReservaStatus::PENDENTE_APROVACAO->value)
                            ->required(),
                        DatePicker::make('data_reserva')
                            ->label('Data')
                            ->required(),
                        TimePicker::make('hora_inicio')
                            ->label('Hora inicial')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('hora_fim')
                            ->label('Hora final')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('solicitante_nome')
                            ->label('Solicitante')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('solicitante_email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->required()
                            ->relationship('departamentoRelacionamento', 'nome')
                            ->searchable()
                            ->preload(),
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('participantes')
                            ->label('Participantes')
                            ->helperText('Use ; para separar os e-mails.')
                            ->columnSpanFull(),
                        Textarea::make('motivo_reprovacao')
                            ->label('Motivo da reprovacao')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('observacoes')
                            ->label('Observacoes internas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
