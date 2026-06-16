<?php

namespace App\Filament\Resources\Recursos;

use App\Enums\UserRole;
use App\Filament\Resources\Recursos\Pages\CreateRecurso;
use App\Filament\Resources\Recursos\Pages\EditRecurso;
use App\Filament\Resources\Recursos\Pages\ListRecursos;
use App\Filament\Resources\Recursos\Schemas\RecursoForm;
use App\Filament\Resources\Recursos\Tables\RecursosTable;
use App\Models\Recurso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecursoResource extends Resource
{
    protected static ?string $model = Recurso::class;

    protected static ?string $modelLabel = 'recurso';

    protected static ?string $pluralModelLabel = 'recursos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Recursos';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    public static function form(Schema $schema): Schema
    {
        return RecursoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecursosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('tipoRecurso');
        $user = auth()->user();

        if (! $user || $user->isAdmin() || $user->hasRole(UserRole::RH)) {
            return $query;
        }

        return $query->whereHas('tipoRecurso', fn (Builder $tipoQuery) => $tipoQuery->whereIn('nome', $user->role->allowedResourceTypes()));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecursos::route('/'),
            'create' => CreateRecurso::route('/create'),
            'edit' => EditRecurso::route('/{record}/edit'),
        ];
    }
}
