<?php

namespace App\Livewire;

use App\Actions\ApproveReservaAction;
use App\Actions\CancelReservaAction;
use App\Actions\RejectReservaAction;
use App\Enums\UserRole;
use App\Exports\ReservasExport;
use App\Models\Departamento;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\TipoRecurso;
use App\Services\MetricasReservaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioReservas extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?int $tipoRecursoId = null;

    public ?int $recursoId = null;

    public ?int $departamentoId = null;

    public string $solicitante = '';

    public string $dataInicial = '';

    public string $dataFinal = '';

    public string $status = '';

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
    public array $departamentos = [];

    public function mount(): void
    {
        $this->dataInicial = now()->startOfMonth()->toDateString();
        $this->dataFinal = now()->endOfMonth()->toDateString();
        $this->carregarTiposRecursos();
        $this->carregarRecursos();
        $this->carregarDepartamentos();
    }

    public function updatedTipoRecursoId(): void
    {
        $this->recursoId = null;
        $this->resetPage();
        $this->carregarRecursos();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['recursoId', 'departamentoId', 'solicitante', 'dataInicial', 'dataFinal', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function exportarCsv()
    {
        abort_unless($this->podeExportar(), 403);

        return Excel::download(
            new ReservasExport($this->filtros(), auth()->user()),
            'reservas.csv',
            ExcelFormat::CSV
        );
    }

    public function exportarExcel()
    {
        abort_unless($this->podeExportar(), 403);

        return Excel::download(
            new ReservasExport($this->filtros(), auth()->user()),
            'reservas.xlsx',
            ExcelFormat::XLSX
        );
    }

    public function aprovarReserva(int $reservaId): void
    {
        $reserva = Reserva::query()->with(['recurso.tipoRecurso', 'departamentoRelacionamento'])->findOrFail($reservaId);
        $this->authorize('approve', $reserva);

        app(ApproveReservaAction::class)->execute($reserva, auth()->user());

        $this->dispatch('notify', type: 'success', title: 'Solicitacao aprovada', text: 'A reserva foi confirmada e o solicitante foi avisado.');
        $this->resetPage();
    }

    public function reprovarReserva(int $reservaId, string $motivo): void
    {
        $reserva = Reserva::query()->with(['recurso.tipoRecurso', 'departamentoRelacionamento'])->findOrFail($reservaId);
        $this->authorize('reject', $reserva);

        app(RejectReservaAction::class)->execute($reserva, auth()->user(), $motivo);

        $this->dispatch('notify', type: 'success', title: 'Solicitacao reprovada', text: 'A resposta foi registrada e o solicitante foi avisado.');
        $this->resetPage();
    }

    public function cancelarReserva(int $reservaId): void
    {
        $reserva = Reserva::query()->with(['recurso.tipoRecurso', 'departamentoRelacionamento'])->findOrFail($reservaId);
        $this->authorize('delete', $reserva);

        app(CancelReservaAction::class)->execute($reserva, auth()->user());

        $this->dispatch('notify', type: 'success', title: 'Reserva cancelada', text: 'A agenda e o relatorio foram atualizados.');
        $this->resetPage();
    }

    public function render()
    {
        $metricasService = app(MetricasReservaService::class);
        $usuario = auth()->user();

        $metricas = $metricasService->resumo($this->filtros(), $usuario);
        $reservas = $metricasService
            ->queryBase($this->filtros(), $usuario)
            ->orderByDesc('data_reserva')
            ->orderBy('hora_inicio')
            ->paginate(10);

        return view('livewire.relatorio-reservas', [
            'metricas' => $metricas,
            'reservas' => $reservas,
            'podeExportar' => $this->podeExportar(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function filtros(): array
    {
        return [
            'tipo_recurso_id' => $this->tipoRecursoId,
            'recurso_id' => $this->recursoId,
            'departamento_id' => $this->departamentoId,
            'solicitante' => $this->solicitante,
            'data_inicial' => $this->dataInicial,
            'data_final' => $this->dataFinal,
            'status' => $this->status,
        ];
    }

    private function carregarTiposRecursos(): void
    {
        $this->tiposRecursos = TipoRecurso::ativosEmCache()
            ->map(fn (TipoRecurso $tipo): array => [
                'id' => $tipo->id,
                'nome' => $tipo->nome,
            ])
            ->all();
    }

    private function carregarRecursos(): void
    {
        $query = Recurso::query()
            ->with('tipoRecurso')
            ->where('ativo', true)
            ->when($this->tipoRecursoId, fn ($query) => $query->where('tipo_recurso_id', $this->tipoRecursoId));

        $this->recursos = $query
            ->orderBy('nome')
            ->get()
            ->map(fn (Recurso $recurso): array => [
                'id' => $recurso->id,
                'nome' => $recurso->nome,
            ])
            ->all();
    }

    private function carregarDepartamentos(): void
    {
        $this->departamentos = Departamento::ativosEmCache()
            ->map(fn (Departamento $departamento): array => [
                'id' => $departamento->id,
                'nome' => $departamento->nome,
            ])
            ->all();
    }

    private function podeExportar(): bool
    {
        $usuario = auth()->user();

        return auth()->check() && (
            $usuario->role !== UserRole::COLABORADOR
            || $usuario->canApproveReservations()
        );
    }
}
