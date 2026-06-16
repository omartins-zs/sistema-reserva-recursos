<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaRejeitadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Reserva $reserva,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $aprovador = $this->reserva->avaliadoPor->name ?? 'Equipe responsavel';

        return (new MailMessage)
            ->subject('Solicitacao reprovada')
            ->greeting('Solicitacao reprovada')
            ->line("Sua solicitacao para {$this->reserva->recurso->nome} nao foi aprovada.")
            ->line("Avaliado por: {$aprovador}")
            ->line("Motivo: {$this->reserva->motivo_reprovacao}")
            ->action('Enviar nova solicitacao', url('/'))
            ->line('Voce pode ajustar as informacoes e enviar um novo pedido.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Solicitacao reprovada',
            'mensagem' => "Sua solicitacao de {$this->reserva->recurso->nome} foi reprovada.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
            'motivo_reprovacao' => $this->reserva->motivo_reprovacao,
        ];
    }
}
