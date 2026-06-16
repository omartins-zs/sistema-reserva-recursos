<?php

namespace App\Filament\Resources\Reservas;

use App\Enums\UserRole;
use App\Filament\Resources\Reservas\Pages\CreateReserva;
use App\Filament\Resources\Reservas\Pages\EditReserva;
use App\Filament\Resources\Reservas\Pages\ListReservas;
use App\Filament\Resources\Reservas\Schemas\ReservaForm;
use App\Filament\Resources\Reservas\Tables\ReservasTable;
use App\Models\Reserva;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Reservas';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacao';

    public static function form(Schema $schema): Schema
    {
        return ReservaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReservasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('recurso.tipoRecurso');
        $user = auth()->user();

        if (! $user || $user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return $query;
        }

        if ($user->hasRole(UserRole::COLABORADOR)) {
            return $query->where('solicitante_email', $user->email);
        }

        return $query->whereHas('recurso.tipoRecurso', fn (Builder $tipoQuery) => $tipoQuery->whereIn('nome', $user->role->allowedResourceTypes()));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReservas::route('/'),
            'create' => CreateReserva::route('/create'),
            'edit' => EditReserva::route('/{record}/edit'),
        ];
    }
}
