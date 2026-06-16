<?php

namespace App\Jobs;

use App\Enums\ReservaStatus;
use App\Models\Recurso;
use App\Models\User;
use App\Notifications\RecursoEmManutencaoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class NotificarReservasRecursoEmManutencao implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $recursoId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recurso = Recurso::query()->with([
            'reservas' => fn ($query) => $query
                ->whereDate('data_reserva', '>=', now()->toDateString())
                ->where('status', ReservaStatus::CONFIRMADO->value),
        ])->find($this->recursoId);

        if (! $recurso) {
            return;
        }

        $recurso->reservas->each(function ($reserva): void {
            $notification = new RecursoEmManutencaoNotification($reserva);

            Notification::route('mail', $reserva->solicitante_email)->notify($notification);

            User::query()
                ->where('email', $reserva->solicitante_email)
                ->each(fn (User $user) => $user->notify($notification));
        });
    }
}
