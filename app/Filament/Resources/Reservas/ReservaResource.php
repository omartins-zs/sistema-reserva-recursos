<?php

namespace App\Filament\Resources\Reservas;

use App\Enums\ReservaStatus;
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

    protected static ?string $modelLabel = 'reserva';

    protected static ?string $pluralModelLabel = 'reservas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Reservas';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacao e aprovacao';

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
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (! ($user?->role?->canApproveReservations() ?? false)) {
            return null;
        }

        $total = (clone static::getEloquentQuery())
            ->where('status', ReservaStatus::PENDENTE_APROVACAO->value)
            ->count();

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['recurso.tipoRecurso', 'avaliadoPor']);
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
