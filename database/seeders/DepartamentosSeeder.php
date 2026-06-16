<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['nome' => 'Administrativo', 'sigla' => 'ADM', 'descricao' => 'Operacoes administrativas e apoio interno.'],
            ['nome' => 'Comercial', 'sigla' => 'COM', 'descricao' => 'Relacionamento comercial e vendas.'],
            ['nome' => 'Compras', 'sigla' => 'CMP', 'descricao' => 'Aquisicoes e suprimentos.'],
            ['nome' => 'Contabilidade', 'sigla' => 'CTB', 'descricao' => 'Rotinas contabeis e fiscais.'],
            ['nome' => 'Diretoria', 'sigla' => 'DIR', 'descricao' => 'Gestao executiva e lideranca.'],
            ['nome' => 'Facilities', 'sigla' => 'FAC', 'descricao' => 'Infraestrutura, espacos e frota.'],
            ['nome' => 'Financeiro', 'sigla' => 'FIN', 'descricao' => 'Fluxo de caixa, contas e planejamento financeiro.'],
            ['nome' => 'Marketing', 'sigla' => 'MKT', 'descricao' => 'Campanhas, comunicacao e marca.'],
            ['nome' => 'RH', 'sigla' => 'RH', 'descricao' => 'Pessoas, cultura e desenvolvimento.'],
            ['nome' => 'TI', 'sigla' => 'TI', 'descricao' => 'Tecnologia, suporte e equipamentos.'],
        ])->each(fn (array $departamento) => Departamento::query()->updateOrCreate(
            ['nome' => $departamento['nome']],
            [
                'sigla' => $departamento['sigla'],
                'descricao' => $departamento['descricao'],
                'ativo' => true,
            ],
        ));
    }
}
