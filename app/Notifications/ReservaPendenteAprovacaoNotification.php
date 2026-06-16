<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaPendenteAprovacaoNotification extends Notification implements ShouldQueue
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
            ->subject('Nova solicitacao aguardando aprovacao')
            ->greeting('Nova solicitacao pendente')
            ->line("O recurso {$this->reserva->recurso->nome} recebeu uma nova solicitacao.")
            ->line("Solicitante: {$this->reserva->solicitante_nome}")
            ->line("Departamento: {$this->reserva->departamento}")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horario: {$this->reserva->periodo_formatado}")
            ->action('Abrir fila de aprovacoes', url('/admin/reservas'))
            ->line('Aprove ou reprovar no painel para liberar o recurso.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Aprovacao pendente',
            'mensagem' => "Nova solicitacao para {$this->reserva->recurso->nome} aguardando sua analise.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
        ];
    }
}
