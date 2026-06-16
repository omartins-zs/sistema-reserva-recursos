<?php

namespace App\Jobs;

use App\Enums\ReservaStatus;
use App\Models\Reserva;
use App\Models\User;
use App\Notifications\ReservaProximaNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class EnviarLembretesReservasProximas implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $inicioJanela = now();
        $fimJanela = now()->copy()->addHour();

        Reserva::query()
            ->with('recurso')
            ->whereDate('data_reserva', $inicioJanela->toDateString())
            ->where('status', ReservaStatus::CONFIRMADO->value)
            ->whereTime('hora_inicio', '>=', $inicioJanela->format('H:i:s'))
            ->whereTime('hora_inicio', '<=', $fimJanela->format('H:i:s'))
            ->each(function (Reserva $reserva): void {
                $cacheKey = "reserva-lembrete:{$reserva->id}:{$reserva->data_reserva}";

                if (Cache::has($cacheKey)) {
                    return;
                }

                $notification = new ReservaProximaNotification($reserva);

                Notification::route('mail', $reserva->solicitante_email)->notify($notification);

                User::query()
                    ->where('email', $reserva->solicitante_email)
                    ->each(fn (User $user) => $user->notify($notification));

                Cache::put($cacheKey, true, now()->addHours(6));
            });
    }
}
