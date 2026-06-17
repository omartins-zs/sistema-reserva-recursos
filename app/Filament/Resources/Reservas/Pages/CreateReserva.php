<?php

namespace App\Filament\Resources\Reservas\Pages;

use App\Filament\Resources\Reservas\ReservaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Nova reserva interna';

    protected static ?string $breadcrumb = 'Nova reserva';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Reserva cadastrada com sucesso.';
    }
}
