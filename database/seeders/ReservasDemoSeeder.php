<?php

namespace Database\Seeders;

use App\Enums\ReservaStatus;
use App\Models\Departamento;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservasDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = Departamento::query()->get()->keyBy('nome');
        $usuarios = User::query()->get()->keyBy('email');

        $salas = Recurso::query()
            ->whereHas('tipoRecurso', fn ($query) => $query->where('nome', 'Sala'))
            ->orderBy('id')
            ->get()
            ->values();

        $projetores = Recurso::query()
            ->whereHas('tipoRecurso', fn ($query) => $query->where('nome', 'Projetor'))
            ->orderBy('id')
            ->get()
            ->values();

        $carros = Recurso::query()
            ->whereHas('tipoRecurso', fn ($query) => $query->where('nome', 'Carro'))
            ->orderBy('id')
            ->get()
            ->values();

        $notebooks = Recurso::query()
            ->whereHas('tipoRecurso', fn ($query) => $query->where('nome', 'Notebook'))
            ->orderBy('id')
            ->get()
            ->values();

        $hoje = now()->startOfDay();

        $reservas = [
            [
                'recurso_id' => $salas->get(0)?->id,
                'solicitante_nome' => 'Bruna Martins',
                'solicitante_email' => 'bruna.martins@empresa.com',
                'departamento_id' => $departamentos->get('Comercial')?->id,
                'departamento' => 'Comercial',
                'motivo' => 'Treinamento comercial trimestral',
                'participantes' => 'comercial@empresa.com; liderancas@empresa.com',
                'data_reserva' => $hoje->copy()->addDay()->toDateString(),
                'hora_inicio' => '09:00:00',
                'hora_fim' => '10:30:00',
                'status' => ReservaStatus::CONFIRMADO,
                'avaliado_por_id' => $usuarios->get('comercial@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subHours(6),
                'motivo_reprovacao' => null,
                'observacoes' => 'Sala preparada com TV e apoio do comercial.',
            ],
            [
                'recurso_id' => $projetores->get(0)?->id,
                'solicitante_nome' => 'Fernanda Alves',
                'solicitante_email' => 'fernanda.alves@empresa.com',
                'departamento_id' => $departamentos->get('RH')?->id,
                'departamento' => 'RH',
                'motivo' => 'Onboarding de novos colaboradores',
                'participantes' => 'rh@empresa.com; onboarding@empresa.com',
                'data_reserva' => $hoje->copy()->addDays(2)->toDateString(),
                'hora_inicio' => '14:00:00',
                'hora_fim' => '16:00:00',
                'status' => ReservaStatus::CONFIRMADO,
                'avaliado_por_id' => $usuarios->get('rh@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subHours(4),
                'motivo_reprovacao' => null,
                'observacoes' => 'Uso na sala de treinamento.',
            ],
            [
                'recurso_id' => $carros->get(0)?->id,
                'solicitante_nome' => 'Marcos Lima',
                'solicitante_email' => 'marcos.lima@empresa.com',
                'departamento_id' => $departamentos->get('Facilities')?->id,
                'departamento' => 'Facilities',
                'motivo' => 'Visita tecnica em unidade externa',
                'participantes' => 'facilities@empresa.com',
                'data_reserva' => $hoje->copy()->addDays(3)->toDateString(),
                'hora_inicio' => '08:00:00',
                'hora_fim' => '17:00:00',
                'status' => ReservaStatus::CONFIRMADO,
                'avaliado_por_id' => $usuarios->get('facilities@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subHours(3),
                'motivo_reprovacao' => null,
                'observacoes' => 'Retirada prevista as 07:45 na garagem.',
            ],
            [
                'recurso_id' => $notebooks->get(0)?->id,
                'solicitante_nome' => 'Rafael Costa',
                'solicitante_email' => 'rafael.costa@empresa.com',
                'departamento_id' => $departamentos->get('TI')?->id,
                'departamento' => 'TI',
                'motivo' => 'Notebook para treinamento interno de sistema',
                'participantes' => 'ti@empresa.com; suporte@empresa.com',
                'data_reserva' => $hoje->copy()->addDays(1)->toDateString(),
                'hora_inicio' => '13:30:00',
                'hora_fim' => '17:30:00',
                'status' => ReservaStatus::PENDENTE_APROVACAO,
                'avaliado_por_id' => null,
                'avaliado_em' => null,
                'motivo_reprovacao' => null,
                'observacoes' => 'Equipamento com acesso temporario ao ambiente de homologacao.',
            ],
            [
                'recurso_id' => $notebooks->get(2)?->id,
                'solicitante_nome' => 'Paula Nunes',
                'solicitante_email' => 'paula.nunes@empresa.com',
                'departamento_id' => $departamentos->get('Financeiro')?->id,
                'departamento' => 'Financeiro',
                'motivo' => 'Notebook adicional para fechamento mensal',
                'participantes' => 'financeiro@empresa.com; controladoria@empresa.com',
                'data_reserva' => $hoje->copy()->subDay()->toDateString(),
                'hora_inicio' => '09:00:00',
                'hora_fim' => '11:00:00',
                'status' => ReservaStatus::REJEITADO,
                'avaliado_por_id' => $usuarios->get('financeiro@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subDay()->setTime(8, 20),
                'motivo_reprovacao' => 'Ha outro equipamento do setor reservado para o mesmo horario.',
                'observacoes' => 'Solicitacao replanejada para o dia seguinte.',
            ],
            [
                'recurso_id' => $salas->get(3)?->id,
                'solicitante_nome' => 'Juliana Prado',
                'solicitante_email' => 'juliana.prado@empresa.com',
                'departamento_id' => $departamentos->get('Marketing')?->id,
                'departamento' => 'Marketing',
                'motivo' => 'Workshop de campanha institucional',
                'participantes' => 'marketing@empresa.com; criacao@empresa.com',
                'data_reserva' => $hoje->copy()->subDays(2)->toDateString(),
                'hora_inicio' => '09:00:00',
                'hora_fim' => '12:00:00',
                'status' => ReservaStatus::CANCELADO,
                'avaliado_por_id' => $usuarios->get('marketing@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subDays(3)->setTime(18, 5),
                'motivo_reprovacao' => null,
                'observacoes' => 'Evento cancelado apos remarcacao do cliente interno.',
            ],
            [
                'recurso_id' => $salas->get(1)?->id,
                'solicitante_nome' => 'Ana Ribeiro',
                'solicitante_email' => 'ana.ribeiro@empresa.com',
                'departamento_id' => $departamentos->get('Administrativo')?->id,
                'departamento' => 'Administrativo',
                'motivo' => 'Reuniao de alinhamento com fornecedores',
                'participantes' => 'compras@empresa.com; administrativo@empresa.com',
                'data_reserva' => $hoje->copy()->subDays(4)->toDateString(),
                'hora_inicio' => '15:00:00',
                'hora_fim' => '16:00:00',
                'status' => ReservaStatus::FINALIZADO,
                'avaliado_por_id' => $usuarios->get('admin@empresa.com')?->id,
                'avaliado_em' => $hoje->copy()->subDays(5)->setTime(11, 30),
                'motivo_reprovacao' => null,
                'observacoes' => 'Reserva concluida sem ocorrencias.',
            ],
        ];

        foreach ($reservas as $reserva) {
            if (! $reserva['recurso_id'] || ! $reserva['departamento_id']) {
                continue;
            }

            Reserva::query()->updateOrCreate(
                [
                    'solicitante_email' => $reserva['solicitante_email'],
                    'motivo' => $reserva['motivo'],
                    'data_reserva' => $reserva['data_reserva'],
                ],
                $reserva,
            );
        }
    }
}
