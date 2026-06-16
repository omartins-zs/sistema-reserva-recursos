<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\ReservaRecurso;
use App\Models\Departamento;
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

        $departamento = Departamento::factory()->create(['nome' => 'Comercial']);
        $aprovadorComercial = User::factory()->create([
            'role' => UserRole::COLABORADOR,
            'email' => 'gestor.comercial@empresa.com',
        ]);
        $departamento->update(['gestor_user_id' => $aprovadorComercial->id]);

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
            ->set('departamentoId', $departamento->id)
            ->set('solicitanteNome', 'Gabriel Teste')
            ->set('solicitanteEmail', 'gabriel@empresa.com')
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
        Notification::assertSentTo($aprovadorComercial, ReservaPendenteAprovacaoNotification::class);
    }

    public function test_it_blocks_a_conflicting_request(): void
    {
        $departamento = Departamento::factory()->create(['nome' => 'TI']);
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
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
            'status' => 'pendente_aprovacao',
        ]);

        Livewire::test(ReservaRecurso::class)
            ->set('tipoRecursoId', $tipo->id)
            ->set('recursoId', $recurso->id)
            ->set('dataReserva', '2026-06-21')
            ->set('horaInicio', '09:30')
            ->set('horaFim', '10:30')
            ->set('departamentoId', $departamento->id)
            ->set('solicitanteNome', 'Outro Usuario')
            ->set('solicitanteEmail', 'outro@empresa.com')
            ->set('motivo', 'Visita externa')
            ->call('reservar')
            ->assertHasErrors(['horaInicio']);

        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_it_blocks_resources_in_maintenance(): void
    {
        $departamento = Departamento::factory()->create(['nome' => 'RH']);
        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'manutencao',
            'ativo' => true,
        ]);

        Livewire::test(ReservaRecurso::class)
            ->set('tipoRecursoId', $tipo->id)
            ->set('recursoId', $recurso->id)
            ->set('departamentoId', $departamento->id)
            ->set('dataReserva', '2026-06-21')
            ->set('horaInicio', '14:00')
            ->set('horaFim', '15:00')
            ->call('verificarDisponibilidade')
            ->assertHasErrors(['recursoId']);
    }
}
