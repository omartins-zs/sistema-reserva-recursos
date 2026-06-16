<?php

namespace Database\Factories;

use App\Models\HistoricoReserva;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HistoricoReserva>
 */
class HistoricoReservaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reserva_id' => Reserva::factory(),
            'acao' => fake()->randomElement(['criada', 'cancelada', 'atualizada']),
            'descricao' => fake()->sentence(),
            'usuario_id' => User::factory(),
        ];
    }
}
