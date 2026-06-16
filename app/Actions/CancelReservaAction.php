<?php

namespace App\Actions;

use App\Enums\ReservaStatus;
use App\Models\Reserva;
use App\Models\User;
use App\Notifications\ReservaCanceladaNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class CancelReservaAction
{
    public function execute(Reserva $reserva, ?User $usuario = null, ?string $motivo = null): Reserva
    {
        if ($reserva->status === ReservaStatus::CANCELADO) {
            throw ValidationException::withMessages([
                'status' => 'Esta reserva já está cancelada.',
            ]);
        }

        DB::transaction(function () use ($reserva, $motivo): void {
            $observacoes = trim(collect([
                $reserva->observacoes,
                $motivo ? 'Cancelamento: '.$motivo : null,
            ])->filter()->implode(PHP_EOL));

            $reserva->forceFill([
                'status' => ReservaStatus::CANCELADO,
                'observacoes' => $observacoes !== '' ? $observacoes : null,
            ])->save();
        });

        $reserva->refresh()->load('recurso.tipoRecurso');

        $notification = new ReservaCanceladaNotification($reserva, $motivo);

        Notification::route('mail', $reserva->solicitante_email)->notify($notification);

        User::query()
            ->where('email', $reserva->solicitante_email)
            ->each(fn (User $user) => $user->notify($notification));

        return $reserva;
    }
}
