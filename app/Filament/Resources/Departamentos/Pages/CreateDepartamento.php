<?php

namespace App\Filament\Resources\Departamentos\Pages;

use App\Filament\Resources\Departamentos\DepartamentoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartamento extends CreateRecord
{
    protected static string $resource = DepartamentoResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
