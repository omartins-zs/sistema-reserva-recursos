<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaCanceladaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Reserva $reserva,
        private readonly ?string $motivo = null,
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
        return (new MailMessage)
            ->subject('Reserva cancelada')
            ->greeting('Reserva cancelada')
            ->line("A reserva do recurso {$this->reserva->recurso->nome} foi cancelada.")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horario: {$this->reserva->periodo_formatado}")
            ->when($this->motivo, fn (MailMessage $message) => $message->line("Motivo: {$this->motivo}"))
            ->action('Consultar reservas', url('/admin/relatorios-reservas'))
            ->line('Se precisar, envie uma nova solicitacao para outro horario.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Reserva cancelada',
            'mensagem' => "A reserva de {$this->reserva->recurso->nome} foi cancelada.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
            'motivo' => $this->motivo,
        ];
    }
}
