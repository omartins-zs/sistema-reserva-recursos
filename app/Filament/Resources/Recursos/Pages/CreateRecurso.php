<?php

namespace App\Filament\Resources\Recursos\Pages;

use App\Filament\Resources\Recursos\RecursoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurso extends CreateRecord
{
    protected static string $resource = RecursoResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
