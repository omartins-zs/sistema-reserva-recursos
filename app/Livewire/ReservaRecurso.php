<?php

namespace App\Livewire;

use App\Actions\CreateReservaAction;
use App\Models\Recurso;
use App\Models\TipoRecurso;
use App\Services\ReservaDisponibilidadeService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ReservaRecurso extends Component
{
    public ?int $tipoRecursoId = null;

    public ?int $recursoId = null;

    public string $dataReserva = '';

    public string $horaInicio = '';

    public string $horaFim = '';

    public string $solicitanteNome = '';

    public string $solicitanteEmail = '';

    public string $departamento = '';

    public string $motivo = '';

    public string $participantes = '';

    public string $observacoes = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $tiposRecursos = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $recursos = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $agenda = [];

    public ?bool $disponivel = null;

    public string $mensagemDisponibilidade = 'Selecione um recurso e um período para verificar a disponibilidade.';

    public function mount(): void
    {
        $this->dataReserva = now()->toDateString();
        $this->carregarTiposRecursos();
    }

    public function updatedTipoRecursoId(): void
    {
        $this->recursoId = null;
        $this->recursos = [];
        $this->agenda = [];
        $this->disponivel = null;
        $this->mensagemDisponibilidade = 'Selecione um recurso e um período para verificar a disponibilidade.';
        $this->carregarRecursos();
    }

    public function updatedRecursoId(): void
    {
        $this->disponivel = null;
        $this->mensagemDisponibilidade = 'Selecione um período para verificar a disponibilidade.';
        $this->carregarAgenda();
    }

    public function updatedDataReserva(): void
    {
        $this->disponivel = null;
        $this->carregarAgenda();
    }

    public function verificarDisponibilidade(ReservaDisponibilidadeService $service): void
    {
        $dados = $this->validate($this->availabilityRules(), $this->messages());
        $recurso = Recurso::query()->findOrFail($dados['recursoId']);

        try {
            $service->validarRecursoReservavel($recurso);
            $service->validarDisponibilidade(
                $recurso->id,
                $dados['dataReserva'],
                $dados['horaInicio'],
                $dados['horaFim'],
            );

            $this->disponivel = true;
            $this->mensagemDisponibilidade = 'Recurso disponível para o horário informado.';
            $this->notify('success', 'Disponível', 'Recurso disponível para reserva.');
        } catch (ValidationException $exception) {
            $this->disponivel = false;
            $this->adicionarErrosAmigaveis($exception);
            $this->mensagemDisponibilidade = collect($exception->errors())->flatten()->first() ?? 'Recurso indisponivel nesse horario.';
            $this->notify('error', 'Indisponível', $this->mensagemDisponibilidade);
        } finally {
            $this->carregarAgenda();
        }
    }

    public function reservar(CreateReservaAction $action): void
    {
        $dados = $this->validate($this->reservationRules(), $this->messages());

        try {
            $reserva = $action->execute([
                'recurso_id' => $dados['recursoId'],
                'solicitante_nome' => $dados['solicitanteNome'],
                'solicitante_email' => $dados['solicitanteEmail'],
                'departamento' => $dados['departamento'],
                'motivo' => $dados['motivo'],
                'participantes' => $dados['participantes'],
                'data_reserva' => $dados['dataReserva'],
                'hora_inicio' => $dados['horaInicio'],
                'hora_fim' => $dados['horaFim'],
                'observacoes' => $dados['observacoes'] ?: null,
            ]);

            $this->notify('success', 'Reserva criada com sucesso', "Reserva confirmada para {$reserva->periodo_formatado}.");
            $this->limparFormulario();
            $this->dispatch('scroll-top');
        } catch (ValidationException $exception) {
            $this->adicionarErrosAmigaveis($exception);
            $this->notify('error', 'Erro de validação', collect($exception->errors())->flatten()->first() ?? 'Revise os dados informados.');
        }
    }

    public function render()
    {
        $recursoSelecionado = $this->recursoId
            ? Recurso::query()->with('tipoRecurso')->find($this->recursoId)
            : null;

        return view('livewire.reserva-recurso', [
            'recursoSelecionado' => $recursoSelecionado,
            'hojeFormatado' => Carbon::parse($this->dataReserva)->translatedFormat('d \\d\\e F \\d\\e Y'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function availabilityRules(): array
    {
        return [
            'tipoRecursoId' => ['required', 'integer', 'exists:tipos_recursos,id'],
            'recursoId' => ['required', 'integer', 'exists:recursos,id'],
            'dataReserva' => ['required', 'date'],
            'horaInicio' => ['required', 'date_format:H:i'],
            'horaFim' => ['required', 'date_format:H:i', 'after:horaInicio'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function reservationRules(): array
    {
        return [
            ...$this->availabilityRules(),
            'solicitanteNome' => ['required', 'string', 'max:255'],
            'solicitanteEmail' => ['required', 'email', 'max:255'],
            'departamento' => ['required', 'string', 'max:255'],
            'motivo' => ['required', 'string', 'max:500'],
            'participantes' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                collect(explode(';', (string) $value))
                    ->map(fn (string $email): string => trim($email))
                    ->filter()
                    ->each(function (string $email) use ($fail): void {
                        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail('Os participantes devem ser e-mails válidos separados por ponto e vírgula.');
                        }
                    });
            }],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'tipoRecursoId.required' => 'O tipo de recurso é obrigatório.',
            'recursoId.required' => 'O recurso é obrigatório.',
            'dataReserva.required' => 'A data é obrigatória.',
            'horaInicio.required' => 'A hora inicial é obrigatória.',
            'horaFim.required' => 'A hora final é obrigatória.',
            'horaFim.after' => 'A hora final deve ser maior que a hora inicial.',
            'solicitanteNome.required' => 'O nome do solicitante é obrigatório.',
            'solicitanteEmail.required' => 'O e-mail do solicitante é obrigatório.',
            'solicitanteEmail.email' => 'Informe um e-mail válido.',
            'departamento.required' => 'O departamento é obrigatório.',
            'motivo.required' => 'O motivo da reserva é obrigatório.',
        ];
    }

    private function carregarTiposRecursos(): void
    {
        $this->tiposRecursos = TipoRecurso::ativosEmCache()
            ->map(fn (TipoRecurso $tipo): array => [
                'id' => $tipo->id,
                'nome' => $tipo->nome,
                'icone' => $tipo->icone,
                'descricao' => $tipo->descricao,
            ])
            ->all();
    }

    private function carregarRecursos(): void
    {
        if (! $this->tipoRecursoId) {
            $this->recursos = [];

            return;
        }

        $this->recursos = Recurso::query()
            ->where('tipo_recurso_id', $this->tipoRecursoId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(fn (Recurso $recurso): array => [
                'id' => $recurso->id,
                'nome' => $recurso->nome,
                'status' => $recurso->status->value,
                'status_label' => $recurso->status->label(),
                'localizacao' => $recurso->localizacao,
                'capacidade' => $recurso->capacidade,
            ])
            ->all();
    }

    private function carregarAgenda(): void
    {
        if (! $this->recursoId || ! $this->dataReserva) {
            $this->agenda = [];

            return;
        }

        $this->agenda = app(ReservaDisponibilidadeService::class)
            ->agendaDoDia($this->recursoId, $this->dataReserva)
            ->map(fn ($reserva): array => [
                'id' => $reserva->id,
                'periodo' => $reserva->periodo_formatado,
                'motivo' => $reserva->motivo,
                'solicitante' => $reserva->solicitante_nome,
                'departamento' => $reserva->departamento,
                'status' => $reserva->status->value,
                'status_label' => $reserva->status->label(),
            ])
            ->all();
    }

    private function limparFormulario(): void
    {
        $this->reset([
            'tipoRecursoId',
            'recursoId',
            'horaInicio',
            'horaFim',
            'solicitanteNome',
            'solicitanteEmail',
            'departamento',
            'motivo',
            'participantes',
            'observacoes',
            'agenda',
            'recursos',
            'disponivel',
        ]);

        $this->dataReserva = now()->toDateString();
        $this->mensagemDisponibilidade = 'Selecione um recurso e um período para verificar a disponibilidade.';
        $this->carregarTiposRecursos();
    }

    private function notify(string $type, string $title, string $text): void
    {
        $this->dispatch('notify', type: $type, title: $title, text: $text);
    }

    private function adicionarErrosAmigaveis(ValidationException $exception): void
    {
        $mapa = [
            'recurso_id' => 'recursoId',
            'hora_inicio' => 'horaInicio',
            'hora_fim' => 'horaFim',
            'data_reserva' => 'dataReserva',
        ];

        foreach ($exception->errors() as $campo => $mensagens) {
            $campoLivewire = $mapa[$campo] ?? $campo;

            foreach ($mensagens as $mensagem) {
                $this->addError($campoLivewire, $mensagem);
            }
        }
    }
}
