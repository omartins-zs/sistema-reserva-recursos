<?php

namespace App\Filament\Resources\TipoRecursos\Pages;

use App\Filament\Resources\TipoRecursos\TipoRecursoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoRecursos extends ListRecords
{
    protected static string $resource = TipoRecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
