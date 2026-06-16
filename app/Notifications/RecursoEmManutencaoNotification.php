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
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recurso em manutencao')
            ->greeting('Aviso de manutencao')
            ->line("O recurso {$this->reserva->recurso->nome} entrou em manutencao.")
            ->line("Reserva afetada: {$this->reserva->data_formatada}, {$this->reserva->periodo_formatado}")
            ->action('Consultar reservas', url('/admin/relatorios-reservas'))
            ->line('Entre em contato com a administracao para remarcar a utilizacao.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Recurso em manutencao',
            'mensagem' => "{$this->reserva->recurso->nome} entrou em manutencao e impacta sua reserva.",
            'reserva_id' => $this->reserva->id,
        ];
    }
}
