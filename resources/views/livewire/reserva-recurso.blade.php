@php
    $recursoSelecionadoArray = collect($recursos)->firstWhere('id', $recursoId);
@endphp

<div class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(15,76,129,0.18),_transparent_35%),linear-gradient(180deg,#f6f9fc_0%,#eef4f8_48%,#e2ebf2_100%)]">
    <div class="hero-orb -left-14 top-20 h-48 w-48 bg-sky-300/60"></div>
    <div class="hero-orb right-10 top-36 h-56 w-56 bg-emerald-200/60 [animation-delay:2s]"></div>
    <div class="hero-orb bottom-24 left-1/3 h-44 w-44 bg-amber-200/60 [animation-delay:4s]"></div>
    <div class="soft-grid absolute inset-0 opacity-40"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 rounded-[2rem] bg-brand-700 px-6 py-6 text-white shadow-2xl shadow-brand-700/20 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.3em] text-sky-200">Reserva de recursos corporativos</p>
                <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">Organize salas, veículos, equipamentos e notebooks sem conflito de horário.</h1>
                <p class="mt-3 max-w-2xl text-sm text-sky-100 sm:text-base">Escolha o recurso, confira a agenda do dia e reserve em poucos segundos com validação automática de disponibilidade.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('relatorios') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                    <i class="fa-solid fa-chart-column"></i>
                    Relatórios
                </a>
                <a href="/admin" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-brand-700 transition hover:bg-sky-50">
                    <i class="fa-solid fa-lock"></i>
                    Painel administrativo
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Nova reserva</p>
                        <h2 class="mt-2 text-2xl font-semibold text-ink-900">Preencha os dados da solicitação</h2>
                    </div>
                    <span class="status-badge {{ $disponivel === true ? 'bg-emerald-100 text-emerald-700' : ($disponivel === false ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                        <span class="h-2 w-2 rounded-full {{ $disponivel === true ? 'bg-emerald-500' : ($disponivel === false ? 'bg-orange-500' : 'bg-slate-400') }}"></span>
                        {{ $disponivel === true ? 'Disponível' : ($disponivel === false ? 'Ocupado' : 'Aguardando verificação') }}
                    </span>
                </div>

                <div class="mb-6 rounded-3xl bg-slate-50 p-4 text-sm text-slate-600">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-info mt-1 text-brand-500"></i>
                        <p>{{ $mensagemDisponibilidade }}</p>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Tipo de recurso</label>
                        <select wire:model.live="tipoRecursoId" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                            <option value="">Selecione</option>
                            @foreach ($tiposRecursos as $tipo)
                                <option value="{{ $tipo['id'] }}">{{ $tipo['nome'] }}</option>
                            @endforeach
                        </select>
                        @error('tipoRecursoId') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Recurso específico</label>
                        <select wire:model.live="recursoId" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                            <option value="">Selecione</option>
                            @foreach ($recursos as $recurso)
                                <option value="{{ $recurso['id'] }}">{{ $recurso['nome'] }}</option>
                            @endforeach
                        </select>
                        @error('recursoId') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Data</label>
                        <input wire:model.live="dataReserva" type="date" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        @error('dataReserva') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Hora inicial</label>
                            <input wire:model="horaInicio" type="time" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                            @error('horaInicio') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Hora final</label>
                            <input wire:model="horaFim" type="time" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                            @error('horaFim') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Solicitante</label>
                        <input wire:model="solicitanteNome" type="text" placeholder="Nome completo" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        @error('solicitanteNome') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">E-mail</label>
                        <input wire:model="solicitanteEmail" type="email" placeholder="nome@empresa.com" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        @error('solicitanteEmail') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Departamento</label>
                        <input wire:model="departamento" type="text" placeholder="Comercial, RH, TI..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        @error('departamento') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Motivo da reserva</label>
                        <textarea wire:model="motivo" rows="3" placeholder="Descreva o objetivo da utilização" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100"></textarea>
                        @error('motivo') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Convidados ou participantes</label>
                        <input wire:model="participantes" type="text" placeholder="email1@empresa.com; email2@empresa.com" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        @error('participantes') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Observações</label>
                        <textarea wire:model="observacoes" rows="2" placeholder="Informações adicionais, se necessário" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100"></textarea>
                    </div>
                </div>

                @if ($recursoSelecionadoArray)
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-ink-900">{{ $recursoSelecionadoArray['nome'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $recursoSelecionadoArray['localizacao'] ?: 'Sem localização informada' }}
                                    @if ($recursoSelecionadoArray['capacidade'])
                                        • Capacidade {{ $recursoSelecionadoArray['capacidade'] }} pessoas
                                    @endif
                                </p>
                            </div>
                            <span class="status-badge {{ $recursoSelecionadoArray['status'] === 'disponivel' ? 'bg-emerald-100 text-emerald-700' : ($recursoSelecionadoArray['status'] === 'manutencao' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                <i class="fa-solid {{ $recursoSelecionadoArray['status'] === 'disponivel' ? 'fa-circle-check' : ($recursoSelecionadoArray['status'] === 'manutencao' ? 'fa-screwdriver-wrench' : 'fa-ban') }}"></i>
                                {{ $recursoSelecionadoArray['status_label'] }}
                            </span>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <button wire:click="verificarDisponibilidade" wire:loading.attr="disabled" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-brand-200 bg-brand-50 px-5 py-3 text-sm font-semibold text-brand-700 transition hover:bg-brand-100 disabled:opacity-60">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Verificar disponibilidade
                    </button>
                    <button
                        type="button"
                        wire:loading.attr="disabled"
                        x-data
                        x-on:click.prevent="Swal.fire({title: 'Confirmar reserva?', text: 'Os dados serão salvos e a agenda será atualizada.', icon: 'question', showCancelButton: true, confirmButtonText: 'Reservar', cancelButtonText: 'Voltar', confirmButtonColor: '#0f4c81'}).then((result) => { if (result.isConfirmed) { $wire.reservar() } })"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:opacity-60"
                    >
                        <i class="fa-solid fa-calendar-check"></i>
                        Reservar
                    </button>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Agenda do dia</p>
                            <h2 class="mt-2 text-2xl font-semibold text-ink-900">{{ $recursoSelecionado?->nome ?? 'Selecione um recurso' }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $hojeFormatado }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-100 px-4 py-3 text-center">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Reservas</p>
                            <p class="mt-1 text-2xl font-semibold text-ink-900">{{ count($agenda) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($agenda as $item)
                            <article class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-brand-600">{{ $item['periodo'] }}</p>
                                        <h3 class="mt-1 text-lg font-semibold text-ink-900">{{ $item['motivo'] }}</h3>
                                    </div>
                                    <span class="status-badge {{ $item['status'] === 'confirmado' ? 'bg-emerald-100 text-emerald-700' : ($item['status'] === 'cancelado' ? 'bg-red-100 text-red-700' : 'bg-slate-200 text-slate-700') }}">
                                        {{ $item['status_label'] }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                    <p><i class="fa-solid fa-user mr-2 text-slate-400"></i>{{ $item['solicitante'] }}</p>
                                    <p><i class="fa-solid fa-building mr-2 text-slate-400"></i>{{ $item['departamento'] }}</p>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500">
                                <i class="fa-regular fa-calendar-xmark mb-3 text-2xl text-slate-400"></i>
                                <p>Nenhuma reserva para este recurso nesta data.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Fluxo rápido</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">1. Escolha</p>
                            <p class="mt-2 text-sm text-slate-700">Selecione o tipo do recurso e o item específico desejado.</p>
                        </div>
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">2. Valide</p>
                            <p class="mt-2 text-sm text-slate-700">Confira conflitos automaticamente antes de confirmar a reserva.</p>
                        </div>
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">3. Reserve</p>
                            <p class="mt-2 text-sm text-slate-700">Salve e acompanhe a reserva com notificações por e-mail e no painel.</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
