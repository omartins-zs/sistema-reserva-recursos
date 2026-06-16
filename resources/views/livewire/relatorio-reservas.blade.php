<div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Central de reservas</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Fila operacional e relatorios</h2>
                <p class="mt-2 text-sm text-slate-500">Consulte pendencias, aprovacoes, cancelamentos e uso dos recursos. Os dados respeitam o perfil do usuario logado.</p>
            </div>
            @if ($podeExportar)
                <div class="flex flex-wrap gap-3">
                    <button wire:click="exportarCsv" type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <i class="fa-solid fa-file-csv text-emerald-600"></i>
                        Exportar CSV
                    </button>
                    <button wire:click="exportarExcel" type="button" class="inline-flex items-center gap-2 rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
                        <i class="fa-solid fa-file-excel"></i>
                        Exportar Excel
                    </button>
                </div>
            @endif
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tipo de recurso</label>
                <select wire:model.live="tipoRecursoId" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    <option value="">Todos</option>
                    @foreach ($tiposRecursos as $tipo)
                        <option value="{{ $tipo['id'] }}">{{ $tipo['nome'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Recurso</label>
                <select wire:model.live="recursoId" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    <option value="">Todos</option>
                    @foreach ($recursos as $recurso)
                        <option value="{{ $recurso['id'] }}">{{ $recurso['nome'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Solicitante</label>
                <input wire:model.live.debounce.400ms="solicitante" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100" placeholder="Buscar por nome">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Departamento</label>
                <input wire:model.live.debounce.400ms="departamento" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100" placeholder="Buscar por departamento">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Data inicial</label>
                <input wire:model.live="dataInicial" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Data final</label>
                <input wire:model.live="dataFinal" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select wire:model.live="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    <option value="">Todos</option>
                    <option value="pendente_aprovacao">Pendente de aprovacao</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="rejeitado">Rejeitado</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Reservas no periodo</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['total'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Pendentes</p>
            <p class="mt-3 text-3xl font-semibold text-amber-600">{{ $metricas['pendentes'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Confirmadas</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ $metricas['confirmadas'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Rejeitadas + canceladas</p>
            <p class="mt-3 text-3xl font-semibold text-rose-600">{{ $metricas['rejeitadas'] + $metricas['canceladas'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Taxa de ocupacao</p>
            <p class="mt-3 text-3xl font-semibold text-brand-600">{{ number_format($metricas['taxa_ocupacao'], 1, ',', '.') }}%</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="pb-4">Recurso</th>
                            <th class="pb-4">Data</th>
                            <th class="pb-4">Solicitante</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4 text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse ($reservas as $reserva)
                            <tr class="align-top">
                                <td class="py-4 pr-4">
                                    <p class="font-semibold text-slate-900">{{ $reserva->recurso->nome }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->recurso->tipoRecurso->nome }}</p>
                                    <p class="mt-2 text-xs text-slate-500">Aprovador: {{ $reserva->responsavel_aprovacao }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <p>{{ $reserva->data_formatada }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->periodo_formatado }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <p>{{ $reserva->solicitante_nome }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->solicitante_email }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->departamento }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="status-badge {{ $reserva->status->value === 'confirmado' ? 'bg-emerald-100 text-emerald-700' : ($reserva->status->value === 'pendente_aprovacao' ? 'bg-amber-100 text-amber-700' : ($reserva->status->value === 'rejeitado' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ $reserva->status->label() }}
                                    </span>
                                    @if ($reserva->avaliadoPor)
                                        <p class="mt-2 text-xs text-slate-500">Avaliado por {{ $reserva->avaliadoPor->name }}</p>
                                    @endif
                                </td>
                                <td class="py-4 text-right">
                                    <div class="flex justify-end gap-2">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-sm text-slate-500">Nenhuma reserva encontrada para os filtros informados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $reservas->links() }}
            </div>
        </div>

        <div class="space-y-6">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Recursos mais utilizados</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($metricas['recursos_mais_utilizados'] as $item)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                            <span class="font-medium text-slate-700">{{ $item->nome }}</span>
                            <span class="font-semibold text-brand-600">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem dados no periodo selecionado.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Reservas por departamento</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($metricas['por_departamento'] as $item)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm">
                            <span class="font-medium text-slate-700">{{ $item->departamento }}</span>
                            <span class="font-semibold text-brand-600">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem dados no periodo selecionado.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</div>
