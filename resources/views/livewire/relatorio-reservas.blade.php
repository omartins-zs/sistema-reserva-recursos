@php
    $maxRecursoUso = max(1, (int) collect($metricas['recursos_mais_utilizados'])->max('total'));
    $maxDepartamentoUso = max(1, (int) collect($metricas['por_departamento'])->max('total'));
    $totalReservas = max(1, (int) $metricas['total']);
    $filtrosAtivos = collect([
        $tipoRecursoId,
        $recursoId,
        $departamentoId,
        filled($solicitante) ? trim($solicitante) : null,
        $dataInicial,
        $dataFinal,
        $status,
    ])->filter()->count();
@endphp

<div class="space-y-6">
    <section class="report-panel rounded-[2rem] px-6 py-6 text-white sm:px-7">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-4xl">
                <p class="text-sm font-semibold uppercase tracking-[0.32em] text-sky-100">Central operacional</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight sm:text-[2rem]">Relatorios, aprovacoes e fila de uso em um unico painel.</h2>
                <p class="mt-3 max-w-3xl text-sm text-sky-100/90 sm:text-base">Consulte pendencias, aprovacoes, cancelamentos e ocupacao dos recursos sem perder o contexto do departamento, do solicitante e do responsavel pela liberacao.</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="filter-pill">
                        <i class="fa-solid fa-hourglass-half text-amber-200"></i>
                        {{ $metricas['pendentes'] }} pendentes
                    </span>
                    <span class="filter-pill">
                        <i class="fa-solid fa-circle-check text-emerald-200"></i>
                        {{ $metricas['confirmadas'] }} confirmadas
                    </span>
                    <span class="filter-pill">
                        <i class="fa-solid fa-filter text-sky-200"></i>
                        {{ $filtrosAtivos ? $filtrosAtivos.' filtros ativos' : 'Sem filtros ativos' }}
                    </span>
                </div>
            </div>

            @if ($podeExportar)
                <div class="flex flex-wrap gap-3">
                    <button wire:click="exportarCsv" type="button" class="inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                        <i class="fa-solid fa-file-csv text-emerald-200"></i>
                        Exportar CSV
                    </button>
                    <button wire:click="exportarExcel" type="button" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-sky-50">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        Exportar Excel
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="grid gap-6 2xl:grid-cols-[minmax(0,1.6fr)_24rem]">
        <div class="space-y-6">
            <section class="report-card p-6 sm:p-7">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="report-kicker">Filtros</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-900">Refine a fila de reservas</h3>
                        <p class="mt-2 text-sm text-slate-500">Os filtros abaixo respeitam o perfil logado e atualizam a leitura operacional sem recarregar a pagina.</p>
                    </div>

                    <button wire:click="limparFiltros" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <i class="fa-solid fa-rotate-left text-brand-500"></i>
                        Limpar filtros
                    </button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="booking-label">Tipo de recurso</label>
                        <div class="relative">
                            <select wire:model.live="tipoRecursoId" class="booking-select">
                                <option value="">Todos</option>
                                @foreach ($tiposRecursos as $tipo)
                                    <option value="{{ $tipo['id'] }}">{{ $tipo['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Recurso</label>
                        <div class="relative">
                            <select wire:model.live="recursoId" class="booking-select">
                                <option value="">Todos</option>
                                @foreach ($recursos as $recurso)
                                    <option value="{{ $recurso['id'] }}">{{ $recurso['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Solicitante</label>
                        <input wire:model.live.debounce.400ms="solicitante" type="text" class="booking-input" placeholder="Buscar por nome">
                    </div>

                    <div>
                        <label class="booking-label">Departamento</label>
                        <div class="relative">
                            <select wire:model.live="departamentoId" class="booking-select">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento['id'] }}">{{ $departamento['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Data inicial</label>
                        <div class="picker-shell">
                            <input
                                wire:model.live="dataInicial"
                                data-picker="date"
                                data-placeholder="Selecione"
                                type="text"
                                placeholder="Selecione"
                                class="booking-input pr-11"
                            >
                            <i class="fa-regular fa-calendar picker-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Data final</label>
                        <div class="picker-shell">
                            <input
                                wire:model.live="dataFinal"
                                data-picker="date"
                                data-placeholder="Selecione"
                                type="text"
                                placeholder="Selecione"
                                class="booking-input pr-11"
                            >
                            <i class="fa-regular fa-calendar picker-icon"></i>
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Status</label>
                        <div class="relative">
                            <select wire:model.live="status" class="booking-select">
                                <option value="">Todos</option>
                                <option value="pendente_aprovacao">Pendente de aprovacao</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="rejeitado">Rejeitado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <article class="metric-card">
                    <p class="report-kicker">Volume total</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['total'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Reservas dentro do recorte atual.</p>
                </article>

                <article class="metric-card">
                    <p class="report-kicker">Pendencias</p>
                    <p class="mt-3 text-3xl font-semibold text-amber-600">{{ $metricas['pendentes'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Aguardando decisao do aprovador.</p>
                </article>

                <article class="metric-card">
                    <p class="report-kicker">Confirmadas</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ $metricas['confirmadas'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Liberadas para uso no periodo.</p>
                </article>

                <article class="metric-card">
                    <p class="report-kicker">Rejeitadas e canceladas</p>
                    <p class="mt-3 text-3xl font-semibold text-rose-600">{{ $metricas['rejeitadas'] + $metricas['canceladas'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Pedidos interrompidos no fluxo.</p>
                </article>

                <article class="metric-card">
                    <p class="report-kicker">Taxa de ocupacao</p>
                    <p class="mt-3 text-3xl font-semibold text-brand-600">{{ number_format($metricas['taxa_ocupacao'], 1, ',', '.') }}%</p>
                    <p class="mt-2 text-sm text-slate-500">Baseada no periodo selecionado.</p>
                </article>
            </section>

            <section class="report-card overflow-hidden p-0">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="report-kicker">Fila detalhada</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900">Reservas e solicitacoes</h3>
                        </div>
                        <div class="rounded-full bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600">
                            {{ $reservas->total() }} registros encontrados
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($reservas as $reserva)
                        @php
                            $statusClasses = match ($reserva->status->value) {
                                'confirmado' => 'bg-emerald-100 text-emerald-700',
                                'pendente_aprovacao' => 'bg-amber-100 text-amber-700',
                                'rejeitado', 'cancelado' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <article wire:key="reserva-{{ $reserva->id }}" class="p-6">
                            <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-start 2xl:justify-between">
                                <div class="grid flex-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Recurso</p>
                                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $reserva->recurso->nome }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $reserva->recurso->tipoRecurso->nome }}</p>
                                        <p class="mt-3 text-sm text-slate-500">Aprovador: {{ $reserva->responsavel_aprovacao }}</p>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Periodo</p>
                                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $reserva->data_formatada }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $reserva->periodo_formatado }}</p>
                                        @if ($reserva->avaliadoPor)
                                            <p class="mt-3 text-sm text-slate-500">Avaliado por {{ $reserva->avaliadoPor->name }}</p>
                                        @endif
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2 xl:col-span-1">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Solicitante</p>
                                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $reserva->solicitante_nome }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $reserva->solicitante_email }}</p>
                                        <p class="mt-3 text-sm text-slate-500">{{ $reserva->departamento }}</p>
                                    </div>
                                </div>

                                <div class="2xl:w-[23rem]">
                                    <div class="rounded-[1.5rem] border border-slate-200 p-4">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <span class="status-badge {{ $statusClasses }}">
                                                    {{ $reserva->status->label() }}
                                                </span>
                                                <p class="mt-3 text-sm text-slate-500">{{ $reserva->motivo }}</p>
                                            </div>
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                                                    x-data
                                                    x-on:click="Swal.fire({icon: 'info', title: 'Detalhes da solicitacao', html: '<div class=&quot;text-left text-sm&quot;><p><strong>Motivo:</strong> {{ addslashes($reserva->motivo) }}</p><p class=&quot;mt-2&quot;><strong>Participantes:</strong> {{ addslashes($reserva->participantes ?: 'Nao informados') }}</p><p class=&quot;mt-2&quot;><strong>Observacoes:</strong> {{ addslashes($reserva->observacoes ?: 'Sem observacoes') }}</p><p class=&quot;mt-2&quot;><strong>Motivo da reprovacao:</strong> {{ addslashes($reserva->motivo_reprovacao ?: 'Nao se aplica') }}</p></div>', confirmButtonColor: '#0f4c81'})"
                                                >
                                                    <i class="fa-solid fa-eye"></i>
                                                    Detalhes
                                                </button>

                                                @can('approve', $reserva)
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
                                                        x-data
                                                        x-on:click.prevent="Swal.fire({title: 'Aprovar solicitacao?', text: 'Ao aprovar, a reserva sera confirmada e o solicitante sera avisado.', icon: 'question', showCancelButton: true, confirmButtonText: 'Aprovar', cancelButtonText: 'Voltar', confirmButtonColor: '#047857'}).then((result) => { if (result.isConfirmed) { $wire.aprovarReserva({{ $reserva->id }}) } })"
                                                    >
                                                        <i class="fa-solid fa-check"></i>
                                                        Aprovar
                                                    </button>
                                                @endcan

                                                @can('reject', $reserva)
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-2 rounded-xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100"
                                                        x-data
                                                        x-on:click.prevent="Swal.fire({title: 'Reprovar solicitacao?', input: 'textarea', inputLabel: 'Motivo da reprovacao', inputPlaceholder: 'Explique porque o pedido nao sera aprovado', inputAttributes: { maxlength: 1000 }, icon: 'warning', showCancelButton: true, confirmButtonText: 'Reprovar', cancelButtonText: 'Voltar', confirmButtonColor: '#b45309', preConfirm: (value) => { if (!value || !value.trim()) { Swal.showValidationMessage('Informe o motivo da reprovacao.') } return value } }).then((result) => { if (result.isConfirmed) { $wire.reprovarReserva({{ $reserva->id }}, result.value) } })"
                                                    >
                                                        <i class="fa-solid fa-xmark"></i>
                                                        Reprovar
                                                    </button>
                                                @endcan

                                                @can('delete', $reserva)
                                                    @if (in_array($reserva->status->value, ['pendente_aprovacao', 'confirmado'], true))
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                                            x-data
                                                            x-on:click.prevent="Swal.fire({title: 'Cancelar reserva?', text: 'Esta acao atualiza a agenda imediatamente.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Cancelar reserva', cancelButtonText: 'Voltar', confirmButtonColor: '#b91c1c'}).then((result) => { if (result.isConfirmed) { $wire.cancelarReserva({{ $reserva->id }}) } })"
                                                        >
                                                            <i class="fa-solid fa-ban"></i>
                                                            Cancelar
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                            </div>
                            <h4 class="mt-5 text-xl font-semibold text-slate-900">Nenhuma reserva encontrada</h4>
                            <p class="mt-2 text-sm text-slate-500">Altere ou limpe os filtros para visualizar a fila completa de solicitacoes e reservas.</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 px-6 py-5">
                    {{ $reservas->links() }}
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="report-card">
                <p class="report-kicker">Pulso da operacao</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">Distribuicao do periodo</h3>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">Pendentes</span>
                            <span class="font-semibold text-slate-900">{{ $metricas['pendentes'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-amber-500" style="width: {{ round(($metricas['pendentes'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">Confirmadas</span>
                            <span class="font-semibold text-slate-900">{{ $metricas['confirmadas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-emerald-500" style="width: {{ round(($metricas['confirmadas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">Rejeitadas</span>
                            <span class="font-semibold text-slate-900">{{ $metricas['rejeitadas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-rose-500" style="width: {{ round(($metricas['rejeitadas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">Canceladas</span>
                            <span class="font-semibold text-slate-900">{{ $metricas['canceladas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-slate-400" style="width: {{ round(($metricas['canceladas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="report-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="report-kicker">Ranking</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">Recursos mais utilizados</h3>
                    </div>
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Top 5</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($metricas['recursos_mais_utilizados'] as $item)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-700">{{ $item->nome }}</span>
                                <span class="font-semibold text-brand-600">{{ $item->total }}</span>
                            </div>
                            <div class="soft-progress">
                                <div class="soft-progress-bar" style="width: {{ round(($item->total / $maxRecursoUso) * 100, 1) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem dados no periodo selecionado.</p>
                    @endforelse
                </div>
            </section>

            <section class="report-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="report-kicker">Departamentos</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">Uso por area</h3>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Top 5</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($metricas['por_departamento'] as $item)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-700">{{ $item->departamento }}</span>
                                <span class="font-semibold text-brand-600">{{ $item->total }}</span>
                            </div>
                            <div class="soft-progress">
                                <div class="soft-progress-bar" style="width: {{ round(($item->total / $maxDepartamentoUso) * 100, 1) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem dados no periodo selecionado.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
</div>
