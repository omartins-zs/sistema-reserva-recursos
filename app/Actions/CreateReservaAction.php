<?php

namespace App\Actions;

use App\Enums\ReservaStatus;
use App\Models\Departamento;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use App\Notifications\ReservaCriadaNotification;
use App\Notifications\ReservaPendenteAprovacaoNotification;
use App\Services\FluxoAprovacaoReservaService;
use App\Services\ReservaDisponibilidadeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class CreateReservaAction
{
    public function __construct(
        private readonly ReservaDisponibilidadeService $disponibilidadeService,
        private readonly FluxoAprovacaoReservaService $fluxoAprovacaoService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?User $usuario = null): Reserva
    {
        $recurso = Recurso::query()->with('tipoRecurso')->findOrFail($data['recurso_id']);
        $departamento = Departamento::query()->with('gestor')->findOrFail($data['departamento_id']);

        $this->disponibilidadeService->validarRecursoReservavel($recurso);
        $this->disponibilidadeService->validarDisponibilidade(
            $recurso->id,
            (string) $data['data_reserva'],
            (string) $data['hora_inicio'],
            (string) $data['hora_fim'],
        );

        $participantes = $this->normalizarParticipantes((string) ($data['participantes'] ?? ''));

        /** @var Reserva $reserva */
        $reserva = DB::transaction(function () use ($data, $participantes, $departamento): Reserva {
            return Reserva::query()->create([
                'recurso_id' => $data['recurso_id'],
                'solicitante_nome' => trim((string) $data['solicitante_nome']),
                'solicitante_email' => mb_strtolower(trim((string) $data['solicitante_email'])),
                'departamento_id' => $departamento->id,
                'departamento' => $departamento->nome,
                'motivo' => trim((string) $data['motivo']),
                'participantes' => $participantes,
                'data_reserva' => $data['data_reserva'],
                'hora_inicio' => $data['hora_inicio'],
                'hora_fim' => $data['hora_fim'],
                'status' => ReservaStatus::PENDENTE_APROVACAO,
                'observacoes' => $data['observacoes'] ?? null,
            ]);
        });

        $reserva->load(['recurso.tipoRecurso', 'departamentoRelacionamento.gestor']);

        $this->notificarSolicitante($reserva);
        $this->notificarAprovadores($reserva);

        return $reserva;
    }

    private function normalizarParticipantes(string $participantes): ?string
    {
        $lista = collect(explode(';', $participantes))
            ->map(fn (string $email): string => trim($email))
            ->filter()
            ->values();

        foreach ($lista as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'participantes' => 'Os participantes devem ser e-mails validos separados por ponto e virgula.',
                ]);
            }
        }

        return $lista->isEmpty() ? null : $lista->implode('; ');
    }

    private function notificarSolicitante(Reserva $reserva): void
    {
        $notification = new ReservaCriadaNotification($reserva);

        Notification::route('mail', $reserva->solicitante_email)->notify($notification);

        User::query()
            ->where('email', $reserva->solicitante_email)
            ->each(fn (User $user) => $user->notify($notification));
    }

    private function notificarAprovadores(Reserva $reserva): void
    {
        $notification = new ReservaPendenteAprovacaoNotification($reserva);
        $aprovadores = $this->fluxoAprovacaoService->usuariosAprovadores($reserva);

        Notification::send($aprovadores, $notification);
    }
}
