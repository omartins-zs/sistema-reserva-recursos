<?php

namespace Database\Factories;

use App\Enums\ReservaStatus;
use App\Models\Recurso;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserva>
 */
class ReservaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = fake()->dateTimeBetween('now', '+30 days');

        return [
            'recurso_id' => Recurso::factory(),
            'solicitante_nome' => fake()->name(),
            'solicitante_email' => fake()->safeEmail(),
            'departamento' => fake()->randomElement(['RH', 'Comercial', 'TI', 'Facilities']),
            'motivo' => fake()->sentence(),
            'participantes' => fake()->safeEmail().'; '.fake()->safeEmail(),
            'data_reserva' => $data->format('Y-m-d'),
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'status' => ReservaStatus::PENDENTE_APROVACAO,
            'motivo_reprovacao' => null,
            'observacoes' => fake()->optional()->sentence(),
        ];
    }
}
