<?php

namespace App\Observers;

use App\Enums\ReservaStatus;
use App\Models\HistoricoReserva;
use App\Models\Reserva;

class ReservaObserver
{
    public function created(Reserva $reserva): void
    {
        HistoricoReserva::query()->create([
            'reserva_id' => $reserva->id,
            'acao' => 'solicitada',
            'descricao' => "Solicitacao criada para {$reserva->data_formatada}, {$reserva->periodo_formatado}.",
            'usuario_id' => auth()->id(),
        ]);
    }

    public function updated(Reserva $reserva): void
    {
        if ($reserva->wasChanged('status')) {
            $descricao = match ($reserva->status) {
                ReservaStatus::PENDENTE_APROVACAO => 'Solicitacao retornou para pendencia.',
                ReservaStatus::CONFIRMADO => 'Solicitacao aprovada e reserva confirmada.',
                ReservaStatus::REJEITADO => 'Solicitacao reprovada.',
                ReservaStatus::CANCELADO => 'Reserva cancelada.',
                ReservaStatus::FINALIZADO => 'Reserva finalizada.',
            };

            HistoricoReserva::query()->create([
                'reserva_id' => $reserva->id,
                'acao' => $reserva->status->value,
                'descricao' => $descricao,
                'usuario_id' => auth()->id() ?? $reserva->avaliado_por_id,
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
}
