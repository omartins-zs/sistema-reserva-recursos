<?php

namespace Tests\Unit;

use App\Models\Departamento;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Models\User;
use App\Services\ReservaDisponibilidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_overlap_rule_detects_conflicts_for_pending_and_confirmed_requests(): void
    {
        $departamento = Departamento::factory()->create(['nome' => 'RH']);
        $tipo = TipoRecurso::factory()->create(['nome' => 'Sala']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'departamento_id' => $departamento->id,
            'departamento' => $departamento->nome,
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'status' => 'pendente_aprovacao',
        ]);

        $service = app(ReservaDisponibilidadeService::class);

        $this->assertFalse($service->estaDisponivel($recurso->id, '2026-06-20', '09:30:00', '09:45:00'));
        $this->assertTrue($service->estaDisponivel($recurso->id, '2026-06-20', '10:00:00', '11:00:00'));
    }

    public function test_tipo_recurso_cache_rebuilds_when_payload_is_invalid(): void
    {
        TipoRecurso::factory()->create(['nome' => 'Notebook', 'ativo' => true]);
        Cache::forever('tipos-recursos.ativos.v2', 'corrompido');

        $tipos = TipoRecurso::ativosEmCache();
        $tipo = $tipos->first();

        $this->assertCount(1, $tipos);
        $this->assertInstanceOf(TipoRecurso::class, $tipo);
        $this->assertSame('Notebook', $tipo->nome);
    }

    public function test_departamento_cache_rebuilds_and_restores_manager_relation(): void
    {
        $gestor = User::factory()->create(['name' => 'Gestora Financeira']);
        Departamento::factory()->create([
            'nome' => 'Financeiro',
            'gestor_user_id' => $gestor->id,
            'ativo' => true,
        ]);

        Cache::forever('departamentos.ativos.v2', 'corrompido');

        $departamentos = Departamento::ativosEmCache();
        $departamento = $departamentos->first();

        $this->assertCount(1, $departamentos);
        $this->assertInstanceOf(Departamento::class, $departamento);
        $this->assertSame('Gestora Financeira', $departamento->gestor?->name);
    }
}
