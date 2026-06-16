<?php

namespace App\Observers;

use App\Enums\RecursoStatus;
use App\Jobs\NotificarReservasRecursoEmManutencao;
use App\Models\Recurso;

class RecursoObserver
{
    /**
     * Handle the Recurso "created" event.
     */
    public function created(Recurso $recurso): void
    {
        //
    }

    /**
     * Handle the Recurso "updated" event.
     */
    public function updated(Recurso $recurso): void
    {
        if ($recurso->wasChanged('status') && $recurso->status === RecursoStatus::MANUTENCAO) {
            NotificarReservasRecursoEmManutencao::dispatch($recurso->id);
        }
    }

    /**
     * Handle the Recurso "deleted" event.
     */
    public function deleted(Recurso $recurso): void
    {
        //
    }

    /**
     * Handle the Recurso "restored" event.
     */
    public function restored(Recurso $recurso): void
    {
        //
    }

    /**
     * Handle the Recurso "force deleted" event.
     */
    public function forceDeleted(Recurso $recurso): void
    {
        //
    }
}
