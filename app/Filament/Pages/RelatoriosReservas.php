<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RelatoriosReservas extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Relatorios';

    protected static ?string $title = 'Central de relatorios';

    protected static ?string $slug = 'relatorios-reservas';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacao e aprovacao';

    protected string $view = 'filament.pages.relatorios-reservas';

    public static function canAccess(): bool
    {
        return auth()->user()?->role?->canViewReports() ?? false;
    }
}
