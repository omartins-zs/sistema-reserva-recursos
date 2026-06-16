<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaCriadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Reserva $reserva,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reserva criada com sucesso')
            ->greeting('Reserva confirmada')
            ->line("O recurso {$this->reserva->recurso->nome} foi reservado com sucesso.")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horário: {$this->reserva->periodo_formatado}")
            ->line("Motivo: {$this->reserva->motivo}")
            ->action('Acessar painel', url('/admin'))
            ->line('Você receberá novos avisos se houver qualquer alteração.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Reserva criada',
            'mensagem' => "Reserva de {$this->reserva->recurso->nome} confirmada para {$this->reserva->data_formatada}.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
        ];
    }
}
