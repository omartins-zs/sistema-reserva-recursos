<?php

namespace Database\Seeders;

use App\Enums\RecursoStatus;
use App\Models\Recurso;
use App\Models\TipoRecurso;
use Illuminate\Database\Seeder;

class RecursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = TipoRecurso::query()->get()->keyBy('nome');

        $recursos = [
            ['tipo' => 'Sala', 'nome' => 'Sala Reunião 01', 'localizacao' => '1º andar', 'capacidade' => 8],
            ['tipo' => 'Sala', 'nome' => 'Sala Reunião 02', 'localizacao' => '1º andar', 'capacidade' => 10],
            ['tipo' => 'Sala', 'nome' => 'Sala Diretoria', 'localizacao' => '2º andar', 'capacidade' => 6],
            ['tipo' => 'Sala', 'nome' => 'Sala Treinamento', 'localizacao' => 'Térreo', 'capacidade' => 24],
            ['tipo' => 'Projetor', 'nome' => 'Projetor Epson 01', 'codigo_patrimonio' => 'PRJ-1001', 'modelo' => 'PowerLite X49', 'marca' => 'Epson', 'localizacao' => 'Almoxarifado'],
            ['tipo' => 'Projetor', 'nome' => 'Projetor Epson 02', 'codigo_patrimonio' => 'PRJ-1002', 'modelo' => 'PowerLite X49', 'marca' => 'Epson', 'localizacao' => '2º andar'],
            ['tipo' => 'Projetor', 'nome' => 'Projetor BenQ 01', 'codigo_patrimonio' => 'PRJ-1003', 'modelo' => 'MS560', 'marca' => 'BenQ', 'localizacao' => 'Sala Treinamento'],
            ['tipo' => 'Carro', 'nome' => 'Gol Branco', 'placa' => 'ABC1D23', 'modelo' => 'Gol 1.0', 'marca' => 'Volkswagen'],
            ['tipo' => 'Carro', 'nome' => 'Onix Prata', 'placa' => 'EFG4H56', 'modelo' => 'Onix LT', 'marca' => 'Chevrolet'],
            ['tipo' => 'Carro', 'nome' => 'Fiorino Utilitária', 'placa' => 'IJK7L89', 'modelo' => 'Fiorino', 'marca' => 'Fiat'],
            ['tipo' => 'Notebook', 'nome' => 'Notebook Dell 01', 'codigo_patrimonio' => 'NTB-2001', 'modelo' => 'Latitude 5440', 'marca' => 'Dell', 'localizacao' => 'TI'],
            ['tipo' => 'Notebook', 'nome' => 'Notebook Dell 02', 'codigo_patrimonio' => 'NTB-2002', 'modelo' => 'Latitude 5440', 'marca' => 'Dell', 'localizacao' => 'TI'],
            ['tipo' => 'Notebook', 'nome' => 'Notebook Lenovo 01', 'codigo_patrimonio' => 'NTB-2003', 'modelo' => 'ThinkPad E14', 'marca' => 'Lenovo', 'localizacao' => 'TI'],
            ['tipo' => 'Notebook', 'nome' => 'Notebook HP 01', 'codigo_patrimonio' => 'NTB-2004', 'modelo' => 'ProBook 440', 'marca' => 'HP', 'localizacao' => 'TI'],
        ];

        collect($recursos)->each(function (array $dados) use ($tipos): void {
            $tipo = $tipos->get($dados['tipo']);

            if (! $tipo) {
                return;
            }

            Recurso::query()->updateOrCreate(
                ['nome' => $dados['nome']],
                [
                    'tipo_recurso_id' => $tipo->id,
                    'descricao' => $dados['descricao'] ?? null,
                    'codigo_patrimonio' => $dados['codigo_patrimonio'] ?? null,
                    'localizacao' => $dados['localizacao'] ?? null,
                    'capacidade' => $dados['capacidade'] ?? null,
                    'placa' => $dados['placa'] ?? null,
                    'modelo' => $dados['modelo'] ?? null,
                    'marca' => $dados['marca'] ?? null,
                    'status' => RecursoStatus::DISPONIVEL,
                    'ativo' => true,
                ],
            );
        });
    }
}
