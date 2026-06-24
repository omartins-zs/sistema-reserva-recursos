<?php

namespace App\Filament\Resources\Reservas\Pages;

use App\Filament\Resources\Reservas\ReservaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use Filament\Support\Enums\Width;

class EditReserva extends EditRecord
{
    protected static string $resource = ReservaResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected static ?string $title = 'Detalhes da reserva';

    protected static ?string $breadcrumb = 'Detalhes';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Excluir reserva'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()
                ->label('Voltar')
                ->color('gray')
                ->button()
                ->icon('heroicon-m-arrow-left'),
        ];
    }
}
