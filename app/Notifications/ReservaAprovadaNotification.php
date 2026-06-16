<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaAprovadaNotification extends Notification implements ShouldQueue
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
            ->subject('Solicitacao aprovada')
            ->greeting('Reserva aprovada')
            ->line("Sua solicitacao para {$this->reserva->recurso->nome} foi aprovada.")
            ->line("Aprovado por: {$aprovador}")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horario: {$this->reserva->periodo_formatado}")
            ->action('Consultar reserva', url('/admin/relatorios-reservas'))
            ->line('A reserva ja esta liberada para uso no horario informado.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Solicitacao aprovada',
            'mensagem' => "Sua reserva de {$this->reserva->recurso->nome} foi aprovada.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
        ];
    }
}
