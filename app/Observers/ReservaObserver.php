<?php

namespace App\Observers;

use App\Enums\ReservaStatus;
use App\Models\HistoricoReserva;
use App\Models\Reserva;

class ReservaObserver
{
    /**
     * Handle the Reserva "created" event.
     */
    public function created(Reserva $reserva): void
    {
        HistoricoReserva::query()->create([
            'reserva_id' => $reserva->id,
            'acao' => 'criada',
            'descricao' => "Reserva criada para {$reserva->data_formatada}, {$reserva->periodo_formatado}.",
            'usuario_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the Reserva "updated" event.
     */
    public function updated(Reserva $reserva): void
    {
        if ($reserva->wasChanged('status') && $reserva->status === ReservaStatus::CANCELADO) {
            HistoricoReserva::query()->create([
                'reserva_id' => $reserva->id,
                'acao' => 'cancelada',
                'descricao' => 'Reserva cancelada.',
                'usuario_id' => auth()->id(),
            ]);

            return;
        }

        if ($reserva->wasChanged()) {
            HistoricoReserva::query()->create([
                'reserva_id' => $reserva->id,
                'acao' => 'atualizada',
                'descricao' => 'Reserva atualizada.',
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Handle the Reserva "deleted" event.
     */
    public function deleted(Reserva $reserva): void
    {
        //
    }

    /**
     * Handle the Reserva "restored" event.
     */
    public function restored(Reserva $reserva): void
    {
        //
    }

    /**
     * Handle the Reserva "force deleted" event.
     */
    public function forceDeleted(Reserva $reserva): void
    {
        //
    }
}
