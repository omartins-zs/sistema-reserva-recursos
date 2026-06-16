<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecursoEmManutencaoNotification extends Notification implements ShouldQueue
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
            ->subject('Recurso em manutenção')
            ->greeting('Aviso de manutenção')
            ->line("O recurso {$this->reserva->recurso->nome} entrou em manutenção.")
            ->line("Reserva afetada: {$this->reserva->data_formatada}, {$this->reserva->periodo_formatado}")
            ->action('Consultar reservas', url('/admin/relatorios-reservas'))
            ->line('Entre em contato com a administração para remarcar a utilização.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Recurso em manutenção',
            'mensagem' => "{$this->reserva->recurso->nome} entrou em manutenção e impacta sua reserva.",
            'reserva_id' => $this->reserva->id,
        ];
    }
}
