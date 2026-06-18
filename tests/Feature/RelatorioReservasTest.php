<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\RelatorioReservas;
use App\Models\Departamento;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Models\User;
use App\Notifications\ReservaAprovadaNotification;
use App\Services\MetricasReservaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

        $departamento = Departamento::factory()->create(['nome' => 'Comercial']);
        $tipo = TipoRecurso::factory()->create(['nome' => 'Sala']);
        $recurso = Recurso::factory()->create(['tipo_recurso_id' => $tipo->id]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'colaborador@empresa.com',
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'motivo' => 'Reserva visivel',
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'outro@empresa.com',
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
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

        $departamento = Departamento::factory()->create(['nome' => 'Operacoes']);
        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create(['tipo_recurso_id' => $tipo->id]);
        $reserva = Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'colaborador@empresa.com',
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
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

    public function test_a_manager_can_approve_a_pending_request(): void
    {
        Notification::fake();

        $departamento = Departamento::factory()->create(['nome' => 'Financeiro']);
        $gestorFinanceiro = User::factory()->create([
            'email' => 'gestor.financeiro@empresa.com',
            'role' => UserRole::COLABORADOR,
        ]);
        $departamento->update(['gestor_user_id' => $gestorFinanceiro->id]);

        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        $reserva = Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'solicitante_email' => 'solicitante@empresa.com',
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
            'status' => 'pendente_aprovacao',
            'data_reserva' => '2026-06-25',
            'hora_inicio' => '13:00:00',
            'hora_fim' => '15:00:00',
        ]);

        Livewire::actingAs($gestorFinanceiro)
            ->test(RelatorioReservas::class)
            ->call('aprovarReserva', $reserva->id);

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'status' => 'confirmado',
            'avaliado_por_id' => $gestorFinanceiro->id,
        ]);

        Notification::assertSentOnDemand(ReservaAprovadaNotification::class);
    }

    public function test_report_metrics_calculate_a_positive_occupancy_rate(): void
    {
        $departamento = Departamento::factory()->create(['nome' => 'Operacoes']);
        $tipo = TipoRecurso::factory()->create(['nome' => 'Notebook']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
            'status' => 'confirmado',
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '11:00:00',
        ]);

        $metricas = app(MetricasReservaService::class)->resumo([]);

        $this->assertSame(16.7, $metricas['taxa_ocupacao']);
    }
}
