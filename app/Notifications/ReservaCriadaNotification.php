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
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitacao de reserva recebida')
            ->greeting('Solicitacao enviada')
            ->line("Recebemos sua solicitacao para o recurso {$this->reserva->recurso->nome}.")
            ->line("Data: {$this->reserva->data_formatada}")
            ->line("Horario: {$this->reserva->periodo_formatado}")
            ->line("Motivo: {$this->reserva->motivo}")
            ->line("Responsavel pela aprovacao: {$this->reserva->responsavel_aprovacao}")
            ->action('Acompanhar solicitacoes', url('/admin/relatorios-reservas'))
            ->line('Voce sera avisado assim que a solicitacao for aprovada ou reprovada.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => 'Solicitacao enviada',
            'mensagem' => "Sua solicitacao de {$this->reserva->recurso->nome} esta pendente de aprovacao.",
            'reserva_id' => $this->reserva->id,
            'status' => $this->reserva->status->value,
        ];
    }
}
