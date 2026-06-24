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
                    ->disabled(fn (string $operation): bool => $operation !== 'create')
                    ->schema([
                        Select::make('recurso_id')
                            ->relationship('recurso', 'nome')
                            ->label('Recurso')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(ReservaStatus::cases())->mapWithKeys(fn (ReservaStatus $status) => [$status->value => $status->label()])->all())
                            ->default(ReservaStatus::PENDENTE_APROVACAO->value)
                            ->required()
                            ->columnSpan(1),
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->required()
                            ->relationship('departamentoRelacionamento', 'nome')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        DatePicker::make('data_reserva')
                            ->label('Data')
                            ->required()
                            ->columnSpan(1),
                        TimePicker::make('hora_inicio')
                            ->label('Hora inicial')
                            ->seconds(false)
                            ->required()
                            ->columnSpan(1),
                        TimePicker::make('hora_fim')
                            ->label('Hora final')
                            ->seconds(false)
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('solicitante_nome')
                            ->label('Solicitante')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('solicitante_email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->required()
                            ->rows(3)
                            ->columnSpan(2),
                        Textarea::make('participantes')
                            ->label('Participantes')
                            ->helperText('Use ; para separar os e-mails.')
                            ->rows(3)
                            ->columnSpan(2),
                        Textarea::make('motivo_reprovacao')
                            ->label('Motivo da reprovacao')
                            ->rows(3)
                            ->columnSpan(2),
                        Textarea::make('observacoes')
                            ->label('Observacoes internas')
                            ->rows(3)
                            ->columnSpan(2),
                    ])
                    ->columns(4),
            ]);
    }
}
