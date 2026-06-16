<?php

namespace Tests\Unit;

use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Services\ReservaDisponibilidadeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_overlap_rule_detects_conflicts_for_pending_and_confirmed_requests(): void
    {
        $tipo = TipoRecurso::factory()->create(['nome' => 'Sala']);
        $recurso = Recurso::factory()->create([
            'tipo_recurso_id' => $tipo->id,
            'status' => 'disponivel',
            'ativo' => true,
        ]);

        Reserva::factory()->create([
            'recurso_id' => $recurso->id,
            'data_reserva' => '2026-06-20',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'status' => 'pendente_aprovacao',
        ]);

        $service = app(ReservaDisponibilidadeService::class);

        $this->assertFalse($service->estaDisponivel($recurso->id, '2026-06-20', '09:30:00', '09:45:00'));
        $this->assertTrue($service->estaDisponivel($recurso->id, '2026-06-20', '10:00:00', '11:00:00'));
    }
}
