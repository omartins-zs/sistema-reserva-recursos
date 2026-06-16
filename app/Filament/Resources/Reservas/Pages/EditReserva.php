<?php

namespace App\Filament\Resources\Reservas\Pages;

use App\Filament\Resources\Reservas\ReservaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReserva extends EditRecord
{
    protected static string $resource = ReservaResource::class;

    protected static ?string $title = 'Detalhes da reserva';

    protected static ?string $breadcrumb = 'Detalhes';

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Reserva atualizada com sucesso.';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Excluir reserva'),
        ];
    }
}
