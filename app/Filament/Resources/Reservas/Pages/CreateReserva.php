<?php

namespace App\Filament\Resources\Reservas\Pages;

use App\Filament\Resources\Reservas\ReservaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected static ?string $title = 'Nova reserva interna';

    protected static ?string $breadcrumb = 'Nova reserva';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Reserva cadastrada com sucesso.';
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Criar reserva'),
            $this->getCreateAnotherFormAction()->label('Criar e cadastrar outra'),
        ];
    }
}
