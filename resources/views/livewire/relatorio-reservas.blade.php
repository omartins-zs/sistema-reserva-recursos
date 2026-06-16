<div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Relatorios</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Consulta de reservas</h2>
                <p class="mt-2 text-sm text-slate-500">Filtre por periodo, recurso, solicitante, departamento e status. Os dados respeitam o perfil de acesso do usuario logado.</p>
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
                    <option value="confirmado">Confirmado</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Reservas no periodo</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $metricas['total'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Confirmadas</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ $metricas['confirmadas'] }}</p>
        </article>
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Canceladas</p>
            <p class="mt-3 text-3xl font-semibold text-orange-600">{{ $metricas['canceladas'] }}</p>
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
                            <th class="pb-4">Motivo</th>
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
                                </td>
                                <td class="py-4 pr-4">
                                    <p>{{ $reserva->data_formatada }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->periodo_formatado }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <p>{{ $reserva->solicitante_nome }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $reserva->departamento }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <p class="max-w-xs text-sm text-slate-600">{{ $reserva->motivo }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    <span class="status-badge {{ $reserva->status->value === 'confirmado' ? 'bg-emerald-100 text-emerald-700' : ($reserva->status->value === 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $reserva->status->label() }}
                                    </span>
                                </td>
                                <td class="py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                                            x-data
                                            x-on:click="Swal.fire({icon: 'info', title: 'Detalhes da reserva', text: '{{ addslashes($reserva->recurso->nome) }} | {{ addslashes($reserva->periodo_formatado) }} | {{ addslashes($reserva->solicitante_email) }}', confirmButtonColor: '#0f4c81'})"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                            Ver detalhes
                                        </button>
                                        @can('delete', $reserva)
                                            @if ($reserva->status->value === 'confirmado')
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
                                <td colspan="6" class="py-10 text-center text-sm text-slate-500">Nenhuma reserva encontrada para os filtros informados.</td>
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
