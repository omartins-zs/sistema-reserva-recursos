<?php

namespace Database\Seeders;

use App\Models\TipoRecurso;
use Illuminate\Database\Seeder;

class TiposRecursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            [
                'nome' => 'Sala',
                'icone' => 'fa-solid fa-door-open',
                'descricao' => 'Salas de reunião, diretoria e treinamento.',
            ],
            [
                'nome' => 'Projetor',
                'icone' => 'fa-solid fa-video',
                'descricao' => 'Projetores para reuniões e treinamentos.',
            ],
            [
                'nome' => 'Carro',
                'icone' => 'fa-solid fa-car-side',
                'descricao' => 'Veículos corporativos para deslocamentos.',
            ],
            [
                'nome' => 'Notebook',
                'icone' => 'fa-solid fa-laptop',
                'descricao' => 'Notebooks para uso interno e onboarding.',
            ],
        ])->each(fn (array $tipo) => TipoRecurso::query()->updateOrCreate(
            ['nome' => $tipo['nome']],
            [...$tipo, 'ativo' => true],
        ));
    }
}
