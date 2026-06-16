<?php

namespace Database\Factories;

use App\Models\TipoRecurso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoRecurso>
 */
class TipoRecursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->randomElement(['Sala', 'Projetor', 'Carro', 'Notebook']),
            'icone' => fake()->randomElement(['fa-solid fa-door-open', 'fa-solid fa-video', 'fa-solid fa-car-side', 'fa-solid fa-laptop']),
            'descricao' => fake()->sentence(),
            'ativo' => true,
        ];
    }
}
