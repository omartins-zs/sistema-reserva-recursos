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
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua reserva comeca em breve')
            ->greeting('Lembrete de reserva')
            ->line("Sua reserva de {$this->reserva->recurso->nome} comeca em breve.")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horario: {$this->reserva->periodo_formatado}")
            ->action('Abrir painel', url('/admin'))
            ->line('Chegue alguns minutos antes para garantir o uso do recurso.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Reserva proxima do horario',
            'mensagem' => "Sua reserva de {$this->reserva->recurso->nome} comeca em breve.",
            'reserva_id' => $this->reserva->id,
        ];
    }
}
