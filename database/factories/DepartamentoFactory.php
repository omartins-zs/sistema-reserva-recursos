<?php

namespace Database\Factories;

use App\Models\Departamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departamento>
 */
class DepartamentoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->company(),
            'sigla' => strtoupper(fake()->unique()->lexify('???')),
            'descricao' => fake()->sentence(),
            'ativo' => true,
            'gestor_user_id' => null,
        ];
    }
}
