<?php

namespace App\Actions;

use App\Enums\ReservaStatus;
use App\Models\Reserva;
use App\Models\User;
use App\Notifications\ReservaRejeitadaNotification;
use App\Services\FluxoAprovacaoReservaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class RejectReservaAction
{
    public function __construct(
        private readonly FluxoAprovacaoReservaService $fluxoAprovacaoService,
    ) {}

    public function execute(Reserva $reserva, User $usuario, string $motivo): Reserva
    {
        $reserva->loadMissing(['recurso.tipoRecurso', 'departamentoRelacionamento']);
        $motivo = trim($motivo);

        if ($motivo === '') {
            throw ValidationException::withMessages([
                'motivo_reprovacao' => 'Informe o motivo da reprovacao.',
            ]);
        }

        if (! $this->fluxoAprovacaoService->usuarioPodeAprovar($usuario, $reserva)) {
            throw ValidationException::withMessages([
                'status' => 'Voce nao pode reprovar esta solicitacao.',
            ]);
        }

        if ($reserva->status !== ReservaStatus::PENDENTE_APROVACAO) {
            throw ValidationException::withMessages([
                'status' => 'Apenas solicitacoes pendentes podem ser reprovadas.',
            ]);
        }

        DB::transaction(function () use ($reserva, $usuario, $motivo): void {
            $reserva->forceFill([
                'status' => ReservaStatus::REJEITADO,
                'avaliado_por_id' => $usuario->id,
                'avaliado_em' => now(),
                'motivo_reprovacao' => $motivo,
            ])->save();
        });

        $reserva->refresh()->load(['recurso.tipoRecurso', 'departamentoRelacionamento', 'avaliadoPor']);

        $notification = new ReservaRejeitadaNotification($reserva);

        Notification::route('mail', $reserva->solicitante_email)->notify($notification);

        User::query()
            ->where('email', $reserva->solicitante_email)
            ->each(fn (User $destinatario) => $destinatario->notify($notification));

        return $reserva;
    }
}
