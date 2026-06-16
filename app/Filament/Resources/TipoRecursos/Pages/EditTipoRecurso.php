<?php

namespace App\Filament\Resources\TipoRecursos\Pages;

use App\Filament\Resources\TipoRecursos\TipoRecursoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoRecurso extends EditRecord
{
    protected static string $resource = TipoRecursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
