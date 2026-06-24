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
    <!-- Header/Banner centralizado -->
    <section class="report-panel rounded-[2rem] px-6 py-6 text-white sm:px-7">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-4xl">
                <p class="text-sm font-semibold uppercase tracking-[0.32em] text-sky-100">Central operacional</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight sm:text-[2rem]">Relatórios, aprovações e fila de uso em um único painel.</h2>
                <p class="mt-3 max-w-3xl text-sm text-sky-100/90 sm:text-base">Consulte pendências, aprovações, cancelamentos e ocupação dos recursos sem perder o contexto do departamento, do solicitante e do aprovador responsável.</p>
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
                    <button wire:click="exportarCsv" type="button" class="inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20 cursor-pointer">
                        <i class="fa-solid fa-file-csv text-emerald-200"></i>
                        Exportar CSV
                    </button>
                    <button wire:click="exportarExcel" type="button" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-sky-50 cursor-pointer">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        Exportar Excel
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="grid gap-6 2xl:grid-cols-[minmax(0,1.6fr)_24rem]">
        <div class="space-y-6">
            <!-- Filtros operacionais -->
            <section class="report-card p-6 sm:p-7 dark:bg-slate-900 dark:border-slate-800">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="report-kicker dark:text-slate-400">Filtros</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Refine a fila de reservas</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Os filtros abaixo respeitam o perfil logado e atualizam a leitura operacional sem recarregar a página.</p>
                    </div>

                    <button wire:click="limparFiltros" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-brand-500 dark:text-sky-400"></i>
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
                                <option value="pendente_aprovacao">Pendente de aprovação</option>
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

            <!-- Cards de Métricas Estilizados -->
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <article class="metric-card dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Volume total</p>
                            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $metricas['total'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-500 dark:text-blue-400">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Reservas no recorte.</p>
                </article>

                <article class="metric-card dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Pendências</p>
                            <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $metricas['pendentes'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 dark:text-amber-400">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Aguardando decisão.</p>
                </article>

                <article class="metric-card dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Confirmadas</p>
                            <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $metricas['confirmadas'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-500 dark:text-emerald-400">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Liberadas para uso.</p>
                </article>

                <article class="metric-card dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Canceladas/Rejeitadas</p>
                            <p class="mt-3 text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $metricas['rejeitadas'] + $metricas['canceladas'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-500 dark:text-rose-400">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pedidos interrompidos.</p>
                </article>

                <article class="metric-card dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Ocupação</p>
                            <p class="mt-3 text-3xl font-bold text-brand-600 dark:text-sky-400">{{ number_format($metricas['taxa_ocupacao'], 1, ',', '.') }}%</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 dark:bg-sky-950/50 text-brand-500 dark:text-sky-400">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Proporção de uso.</p>
                </article>
            </section>

            <!-- Fila Detalhada: Lista unificada e limpa -->
            <section class="report-card overflow-hidden p-0 dark:bg-slate-900 dark:border-slate-800">
                <div class="border-b border-slate-200 dark:border-slate-800 px-6 py-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="report-kicker dark:text-slate-400">Fila detalhada</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Reservas e solicitações</h3>
                        </div>
                        <div class="rounded-full bg-slate-50 dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-350">
                            {{ $reservas->total() }} registros encontrados
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($reservas as $reserva)
                        @php
                            $statusClasses = match ($reserva->status->value) {
                                'confirmado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400',
                                'pendente_aprovacao' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400',
                                'rejeitado', 'cancelado' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400',
                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                            };
                        @endphp

                        <article wire:key="reserva-{{ $reserva->id }}" class="p-6 transition-all duration-200 hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                                
                                <!-- Info do Recurso com ícone dinâmico -->
                                <div class="flex items-center gap-4 min-w-[18rem]">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-950/40 text-brand-500 dark:text-sky-400 border border-brand-100 dark:border-brand-900/30">
                                        @if(str_contains(strtolower($reserva->recurso->tipoRecurso->slug), 'sala'))
                                            <i class="fa-solid fa-door-open text-lg"></i>
                                        @elseif(str_contains(strtolower($reserva->recurso->tipoRecurso->slug), 'notebook') || str_contains(strtolower($reserva->recurso->tipoRecurso->slug), 'computador'))
                                            <i class="fa-solid fa-laptop text-lg"></i>
                                        @elseif(str_contains(strtolower($reserva->recurso->tipoRecurso->slug), 'carro') || str_contains(strtolower($reserva->recurso->tipoRecurso->slug), 'veiculo'))
                                            <i class="fa-solid fa-car text-lg"></i>
                                        @else
                                            <i class="fa-solid fa-cube text-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                                            {{ $reserva->recurso->tipoRecurso->nome }}
                                        </span>
                                        <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $reserva->recurso->nome }}</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Aprovador: <span class="font-medium text-slate-600 dark:text-slate-350">{{ $reserva->responsavel_aprovacao }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Info Período & Solicitante -->
                                <div class="grid flex-1 gap-4 sm:grid-cols-2 lg:px-4">
                                    <!-- Período -->
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                            <i class="fa-regular fa-calendar text-slate-400"></i>
                                            <span class="font-medium text-slate-900 dark:text-white">{{ $reserva->data_formatada }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                            <i class="fa-regular fa-clock text-slate-400"></i>
                                            <span>{{ $reserva->periodo_formatado }}</span>
                                        </div>
                                        @if ($reserva->avaliadoPor)
                                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">
                                                Avaliado por {{ $reserva->avaliadoPor->name }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Solicitante -->
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-200">
                                            <i class="fa-regular fa-user text-slate-400"></i>
                                            <span>{{ $reserva->solicitante_nome }}</span>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $reserva->solicitante_email }} • <span class="font-medium text-brand-600 dark:text-sky-400">{{ $reserva->departamento }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Ações -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-end gap-3 min-w-[22rem] lg:ml-auto">
                                    <span class="status-badge shrink-0 {{ $statusClasses }}">
                                        {{ $reserva->status->label() }}
                                    </span>

                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer"
                                            x-data
                                            x-on:click="Swal.fire({icon: 'info', title: 'Detalhes da solicitação', html: '<div class=&quot;text-left text-sm&quot;><p><strong>Recurso:</strong> {{ $reserva->recurso->nome }} ({{ $reserva->recurso->tipoRecurso->nome }})</p><p class=&quot;mt-2&quot;><strong>Período:</strong> {{ $reserva->data_formatada }} ({{ $reserva->periodo_formatado }})</p><p class=&quot;mt-2&quot;><strong>Solicitante:</strong> {{ $reserva->solicitante_nome }} ({{ $reserva->solicitante_email }}) - {{ $reserva->departamento }}</p><p class=&quot;mt-2&quot;><strong>Motivo:</strong> {{ addslashes($reserva->motivo) }}</p><p class=&quot;mt-2&quot;><strong>Participantes:</strong> {{ addslashes($reserva->participantes ?: 'Não informados') }}</p><p class=&quot;mt-2&quot;><strong>Observações:</strong> {{ addslashes($reserva->observacoes ?: 'Sem observações') }}</p><p class=&quot;mt-2&quot;><strong>Motivo da reprovação:</strong> {{ addslashes($reserva->motivo_reprovacao ?: 'Não se aplica') }}</p></div>', confirmButtonColor: '#0f4c81'})"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                            Detalhes
                                        </button>

                                        @can('approve', $reserva)
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/30 px-3 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-400 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/40 cursor-pointer"
                                                x-data
                                                x-on:click.prevent="Swal.fire({title: 'Aprovar solicitação?', text: 'Ao aprovar, a reserva será confirmada e o solicitante será avisado.', icon: 'question', showCancelButton: true, confirmButtonText: 'Aprovar', cancelButtonText: 'Voltar', confirmButtonColor: '#047857'}).then((result) => { if (result.isConfirmed) { $wire.aprovarReserva({{ $reserva->id }}) } })"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                                Aprovar
                                            </button>
                                        @endcan

                                        @can('reject', $reserva)
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/30 px-3 py-2 text-xs font-bold text-amber-700 dark:text-amber-400 transition hover:bg-amber-100 dark:hover:bg-amber-900/40 cursor-pointer"
                                                x-data
                                                x-on:click.prevent="Swal.fire({title: 'Reprovar solicitação?', input: 'textarea', inputLabel: 'Motivo da reprovação', inputPlaceholder: 'Explique porque o pedido não será aprovado', inputAttributes: { maxlength: 1000 }, icon: 'warning', showCancelButton: true, confirmButtonText: 'Reprovar', cancelButtonText: 'Voltar', confirmButtonColor: '#b45309', preConfirm: (value) => { if (!value || !value.trim()) { Swal.showValidationMessage('Informe o motivo da reprovação.') } return value } }).then((result) => { if (result.isConfirmed) { $wire.reprovarReserva({{ $reserva->id }}, result.value) } })"
                                            >
                                                <i class="fa-solid fa-xmark"></i>
                                                Reprovar
                                            </button>
                                        @endcan

                                        @can('delete', $reserva)
                                            @if (in_array($reserva->status->value, ['pendente_aprovacao', 'confirmado'], true))
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-1.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900/30 px-3 py-2 text-xs font-bold text-red-700 dark:text-red-450 transition hover:bg-red-100 dark:hover:bg-red-900/40 cursor-pointer"
                                                    x-data
                                                    x-on:click.prevent="Swal.fire({title: 'Cancelar reserva?', text: 'Esta ação atualiza a agenda imediatamente.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Cancelar reserva', cancelButtonText: 'Voltar', confirmButtonColor: '#b91c1c'}).then((result) => { if (result.isConfirmed) { $wire.cancelarReserva({{ $reserva->id }}) } })"
                                                >
                                                    <i class="fa-solid fa-ban"></i>
                                                    Cancelar
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                            </div>
                            <h4 class="mt-5 text-xl font-semibold text-slate-900 dark:text-white">Nenhuma reserva encontrada</h4>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Altere ou limpe os filtros para visualizar a fila completa de solicitações e reservas.</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 dark:border-slate-800 px-6 py-5">
                    {{ $reservas->links() }}
                </div>
            </section>
        </div>

        <!-- Sidebar Rankings e Gráficos -->
        <aside class="space-y-6">
            <section class="report-card dark:bg-slate-900 dark:border-slate-800">
                <p class="report-kicker dark:text-slate-400">Pulso da operação</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Distribuição do período</h3>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600 dark:text-slate-400">Pendentes</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $metricas['pendentes'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-amber-500" style="width: {{ round(($metricas['pendentes'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600 dark:text-slate-400">Confirmadas</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $metricas['confirmadas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-emerald-500" style="width: {{ round(($metricas['confirmadas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600 dark:text-slate-400">Rejeitadas</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $metricas['rejeitadas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-rose-500" style="width: {{ round(($metricas['rejeitadas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600 dark:text-slate-400">Canceladas</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $metricas['canceladas'] }}</span>
                        </div>
                        <div class="soft-progress">
                            <div class="soft-progress-bar bg-slate-450 dark:bg-slate-500" style="width: {{ round(($metricas['canceladas'] / $totalReservas) * 100, 1) }}%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="report-card dark:bg-slate-900 dark:border-slate-800">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="report-kicker dark:text-slate-400">Ranking</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Recursos mais utilizados</h3>
                    </div>
                    <span class="rounded-full bg-brand-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-brand-700 dark:text-sky-300">Top 5</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($metricas['recursos_mais_utilizados'] as $item)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-700 dark:text-slate-350">{{ $item->nome }}</span>
                                <span class="font-semibold text-brand-600 dark:text-sky-400">{{ $item->total }}</span>
                            </div>
                            <div class="soft-progress">
                                <div class="soft-progress-bar" style="width: {{ round(($item->total / $maxRecursoUso) * 100, 1) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sem dados no período selecionado.</p>
                    @endforelse
                </div>
            </section>

            <section class="report-card dark:bg-slate-900 dark:border-slate-800">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="report-kicker dark:text-slate-400">Departamentos</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Uso por área</h3>
                    </div>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400">Top 5</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($metricas['por_departamento'] as $item)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-slate-700 dark:text-slate-350">{{ $item->departamento }}</span>
                                <span class="font-semibold text-brand-600 dark:text-sky-400">{{ $item->total }}</span>
                            </div>
                            <div class="soft-progress">
                                <div class="soft-progress-bar" style="width: {{ round(($item->total / $maxDepartamentoUso) * 100, 1) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sem dados no período selecionado.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
</div>
