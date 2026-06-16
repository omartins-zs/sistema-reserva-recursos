<?php

namespace App\Actions;

use App\Enums\ReservaStatus;
use App\Models\Reserva;
use App\Models\User;
use App\Notifications\ReservaAprovadaNotification;
use App\Services\FluxoAprovacaoReservaService;
use App\Services\ReservaDisponibilidadeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class ApproveReservaAction
{
    public function __construct(
        private readonly FluxoAprovacaoReservaService $fluxoAprovacaoService,
        private readonly ReservaDisponibilidadeService $disponibilidadeService,
    ) {}

    public function execute(Reserva $reserva, User $usuario, ?string $observacao = null): Reserva
    {
        $reserva->loadMissing('recurso.tipoRecurso');

        if (! $this->fluxoAprovacaoService->usuarioPodeAprovar($usuario, $reserva)) {
            throw ValidationException::withMessages([
                'status' => 'Voce nao pode aprovar esta solicitacao.',
            ]);
        }

        if ($reserva->status !== ReservaStatus::PENDENTE_APROVACAO) {
            throw ValidationException::withMessages([
                'status' => 'Apenas solicitacoes pendentes podem ser aprovadas.',
            ]);
        }

        $this->disponibilidadeService->validarRecursoReservavel($reserva->recurso);
        $this->disponibilidadeService->validarDisponibilidade(
            $reserva->recurso_id,
            $reserva->data_reserva->toDateString(),
            $reserva->hora_inicio,
            $reserva->hora_fim,
            $reserva->id,
        );

        DB::transaction(function () use ($reserva, $usuario, $observacao): void {
            $observacoes = trim(collect([
                $reserva->observacoes,
                $observacao ? 'Aprovacao: '.$observacao : null,
            ])->filter()->implode(PHP_EOL));

            $reserva->forceFill([
                'status' => ReservaStatus::CONFIRMADO,
                'avaliado_por_id' => $usuario->id,
                'avaliado_em' => now(),
                'motivo_reprovacao' => null,
                'observacoes' => $observacoes !== '' ? $observacoes : null,
            ])->save();
        });

        $reserva->refresh()->load(['recurso.tipoRecurso', 'avaliadoPor']);

        $notification = new ReservaAprovadaNotification($reserva);

        Notification::route('mail', $reserva->solicitante_email)->notify($notification);

        User::query()
            ->where('email', $reserva->solicitante_email)
            ->each(fn (User $destinatario) => $destinatario->notify($notification));

        return $reserva;
    }
}
