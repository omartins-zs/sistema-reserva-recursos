<?php

namespace Database\Factories;

use App\Enums\RecursoStatus;
use App\Models\Recurso;
use App\Models\TipoRecurso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recurso>
 */
class RecursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_recurso_id' => TipoRecurso::factory(),
            'nome' => 'Recurso '.fake()->unique()->numberBetween(1, 999),
            'descricao' => fake()->sentence(),
            'codigo_patrimonio' => 'PAT-'.fake()->unique()->numberBetween(1000, 9999),
            'localizacao' => fake()->randomElement(['Matriz', 'Filial', '2º andar', 'Sala TI']),
            'capacidade' => fake()->optional()->numberBetween(2, 20),
            'placa' => fake()->optional()->regexify('[A-Z]{3}[0-9][A-Z][0-9]{2}'),
            'modelo' => fake()->randomElement(['Dell Latitude', 'Lenovo ThinkPad', 'Gol', 'Onix', 'Epson PowerLite']),
            'marca' => fake()->randomElement(['Dell', 'Lenovo', 'Volkswagen', 'Chevrolet', 'Epson']),
            'status' => RecursoStatus::DISPONIVEL,
            'ativo' => true,
        ];
    }
}
