<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\ReservaRecurso;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Models\User;
use App\Notifications\ReservaCriadaNotification;
use App\Notifications\ReservaPendenteAprovacaoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ReservaRecursoTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_pending_request_and_notifies_the_approval_queue(): void
    {
        Notification::fake();

        $aprovadorTi = User::factory()->create([
            'role' => UserRole::TI,
            'email' => 'ti@empresa.com',
        ]);

        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        Livewire::test(ReservaRecurso::class)
            ->set('tipoRecursoId', $tipo->id)
            ->set('recursoId', $recurso->id)
            ->set('dataReserva', '2026-06-20')
            ->set('horaInicio', '09:00')
            ->set('horaFim', '10:00')
            ->set('solicitanteNome', 'Gabriel Teste')
            ->set('solicitanteEmail', 'gabriel@empresa.com')
            ->set('departamento', 'Comercial')
            ->set('motivo', 'Treinamento comercial')
            ->set('participantes', 'ana@empresa.com; bruno@empresa.com')
            ->call('reservar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reservas', [
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'gabriel@empresa.com',
            'departamento' => 'Comercial',
            'status' => 'pendente_aprovacao',
        ]);

        Notification::assertSentOnDemand(ReservaCriadaNotification::class);
        Notification::assertSentTo($aprovadorTi, ReservaPendenteAprovacaoNotification::class);
    }

    public function test_it_blocks_a_conflicting_request(): void
    {
        $tipo = TipoRecurso::factory()->create(['nome' => 'Carro']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'data_reserva' => '2026-06-21',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'status' => 'pendente_aprovacao',
        ]);

        Livewire::test(ReservaRecurso::class)
            ->set('tipoRecursoId', $tipo->id)
            ->set('recursoId', $recurso->id)
            ->set('dataReserva', '2026-06-21')
            ->set('horaInicio', '09:30')
            ->set('horaFim', '10:30')
            ->set('solicitanteNome', 'Outro Usuario')
            ->set('solicitanteEmail', 'outro@empresa.com')
            ->set('departamento', 'TI')
            ->set('motivo', 'Visita externa')
            ->call('reservar')
            ->assertHasErrors(['horaInicio']);

        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_it_blocks_resources_in_maintenance(): void
    {
        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'manutencao',
            'ativo' => true,
        ]);

        Livewire::test(ReservaRecurso::class)
            ->set('tipoRecursoId', $tipo->id)
            ->set('recursoId', $recurso->id)
            ->set('dataReserva', '2026-06-21')
            ->set('horaInicio', '14:00')
            ->set('horaFim', '15:00')
            ->call('verificarDisponibilidade')
            ->assertHasErrors(['recursoId']);
    }
}
