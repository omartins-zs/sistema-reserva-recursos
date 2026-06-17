<?php

namespace App\Filament\Resources\TipoRecursos\Pages;

use App\Filament\Resources\TipoRecursos\TipoRecursoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoRecurso extends CreateRecord
{
    protected static string $resource = TipoRecursoResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
