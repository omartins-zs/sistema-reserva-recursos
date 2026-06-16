<?php

namespace App\Filament\Resources\TipoRecursos;

use App\Enums\UserRole;
use App\Filament\Resources\TipoRecursos\Pages\CreateTipoRecurso;
use App\Filament\Resources\TipoRecursos\Pages\EditTipoRecurso;
use App\Filament\Resources\TipoRecursos\Pages\ListTipoRecursos;
use App\Filament\Resources\TipoRecursos\Schemas\TipoRecursoForm;
use App\Filament\Resources\TipoRecursos\Tables\TipoRecursosTable;
use App\Models\TipoRecurso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoRecursoResource extends Resource
{
    protected static ?string $model = TipoRecurso::class;

    protected static ?string $modelLabel = 'tipo de recurso';

    protected static ?string $pluralModelLabel = 'tipos de recurso';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Tipos de Recursos';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    public static function form(Schema $schema): Schema
    {
        return TipoRecursoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoRecursosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(UserRole::ADMINISTRADOR, UserRole::RH, UserRole::TI, UserRole::FACILITIES) ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTipoRecursos::route('/'),
            'create' => CreateTipoRecurso::route('/create'),
            'edit' => EditTipoRecurso::route('/{record}/edit'),
        ];
    }
}
