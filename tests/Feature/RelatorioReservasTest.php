<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\RelatorioReservas;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RelatorioReservasTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_collaborator_only_sees_their_own_reservations(): void
    {
        $user = User::factory()->create([
            'email' => 'colaborador@empresa.com',
            'role' => UserRole::COLABORADOR,
        ]);

        $tipo = TipoRecurso::factory()->create(['nome' => 'Sala']);
        $recurso = Recurso::factory()->create(['tipo_recurso_id' => $tipo->id]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'colaborador@empresa.com',
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'motivo' => 'Reserva visivel',
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'outro@empresa.com',
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '11:00:00',
            'hora_fim' => '12:00:00',
            'motivo' => 'Reserva oculta',
        ]);

        Livewire::actingAs($user)
            ->test(RelatorioReservas::class)
            ->assertSee('Reserva visivel')
            ->assertDontSee('Reserva oculta');
    }

    public function test_a_collaborator_can_cancel_their_own_reservation(): void
    {
        $user = User::factory()->create([
            'email' => 'colaborador@empresa.com',
            'role' => UserRole::COLABORADOR,
        ]);

        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create(['tipo_recurso_id' => $tipo->id]);
        $reserva = Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'colaborador@empresa.com',
            'status' => 'confirmado',
        ]);

        Livewire::actingAs($user)
            ->test(RelatorioReservas::class)
            ->call('cancelarReserva', $reserva->id);

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'status' => 'cancelado',
        ]);
    }
}
