<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaProximaNotification extends Notification implements ShouldQueue
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
            ->subject('Sua reserva começa em breve')
            ->greeting('Lembrete de reserva')
            ->line("Sua reserva de {$this->reserva->recurso->nome} começa em breve.")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horário: {$this->reserva->periodo_formatado}")
            ->action('Abrir painel', url('/admin'))
            ->line('Chegue alguns minutos antes para garantir o uso do recurso.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Reserva próxima do horário',
            'mensagem' => "Sua reserva de {$this->reserva->recurso->nome} começa em breve.",
            'reserva_id' => $this->reserva->id,
        ];
    }
}
