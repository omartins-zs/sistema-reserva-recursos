@php
    $recursoSelecionadoArray = collect($recursos)->firstWhere('id', $recursoId);
    $horarios = [];
    for ($h = 0; $h < 24; $h++) {
        foreach (['00', '30'] as $m) {
            $horarios[] = sprintf('%02d:%s', $h, $m);
        }
    }
@endphp

<div class="relative min-h-screen overflow-hidden page-wrapper">
    <div class="hero-orb -left-14 top-20 h-48 w-48 bg-sky-300/60"></div>
    <div class="hero-orb right-10 top-36 h-56 w-56 bg-emerald-200/60 [animation-delay:2s]"></div>
    <div class="hero-orb bottom-24 left-1/3 h-44 w-44 bg-amber-200/60 [animation-delay:4s]"></div>
    <div class="soft-grid absolute inset-0 opacity-40"></div>
 
    <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 rounded-[2rem] bg-brand-700 px-6 py-6 text-white shadow-2xl shadow-brand-700/20 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-sky-400/20 bg-sky-500/10 px-3.5 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-sky-200 backdrop-blur-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-300 animate-pulse"></span>
                    Reserva corporativa com aprovacao
                </div>
                <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">Solicite salas, notebooks, carros e equipamentos com uma experiencia clara e pronta para aprovacao.</h1>
                <!-- 
                <p class="mt-3 max-w-2xl text-sm text-sky-100 sm:text-base">Informe o departamento, o periodo e o contexto de uso para direcionar a solicitacao ao gestor correto, com notificacoes automaticas e agenda protegida contra conflitos.</p>
                -->
            </div>
            <div class="flex flex-wrap gap-4 items-center lg:ml-auto">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-sky-200 uppercase tracking-widest">Tema</span>
                    <div class="inline-flex items-center gap-1 rounded-full border border-white/10 bg-white/5 p-1 backdrop-blur-sm">
                        <button onclick="window.setAppTheme('light')" id="theme-btn-light" class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition-all hover:bg-white/10 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-sun text-amber-300"></i>
                            Claro
                        </button>
                        <button onclick="window.setAppTheme('dark')" id="theme-btn-dark" class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/80 transition-all hover:bg-white/10 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-moon text-sky-300"></i>
                            Escuro
                        </button>
                    </div>
                </div>
                <a href="/admin" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-brand-700 shadow-lg shadow-black/10 transition-all duration-300 hover:bg-sky-50 hover:shadow-xl hover:shadow-black/15 hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fa-solid fa-user-shield"></i>
                    Acessar painel
                </a>
            </div>
        </div>

        <div class="mb-6 grid gap-4 lg:grid-cols-3">
            <article class="glass-panel rounded-[2rem] p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500 dark:text-sky-400">Fluxo de solicitacao</p>
                <h2 class="mt-2 text-xl font-semibold text-ink-900">Pedido simples para o colaborador</h2>
                <!-- 
                <p class="mt-3 text-sm text-slate-600">A reserva nasce como solicitacao pendente e ja entra na fila certa para analise.</p>
                -->
            </article>
            <article class="glass-panel rounded-[2rem] p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500 dark:text-sky-400">Responsavel atual</p>
                <h2 class="mt-2 text-xl font-semibold text-ink-900">{{ $responsavelAprovacao }}</h2>
                <!-- 
                <p class="mt-3 text-sm text-slate-600">Aprovador definido pelo departamento informado na solicitacao, com apoio da administracao.</p>
                -->
            </article>
            <article class="glass-panel rounded-[2rem] p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500 dark:text-sky-400">Protecao de agenda</p>
                <h2 class="mt-2 text-xl font-semibold text-ink-900">Sem sobreposicao</h2>
                <!-- 
                <p class="mt-3 text-sm text-slate-600">Solicitacoes pendentes e reservas aprovadas ja bloqueiam o mesmo recurso no mesmo periodo.</p>
                -->
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Nova solicitacao</p>
                        <h2 class="mt-2 text-2xl font-semibold text-ink-900">Preencha os dados do pedido</h2>
                    </div>
                    <span class="status-badge {{ $disponivel === true ? 'bg-emerald-100 text-emerald-700' : ($disponivel === false ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                        <span class="h-2 w-2 rounded-full {{ $disponivel === true ? 'bg-emerald-500' : ($disponivel === false ? 'bg-rose-500' : 'bg-slate-400') }}"></span>
                        {{ $disponivel === true ? 'Disponivel' : ($disponivel === false ? 'Indisponivel' : 'Aguardando verificacao') }}
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
                        <label class="booking-label">Tipo de recurso</label>
                        <div class="relative">
                            <select wire:model.live="tipoRecursoId" class="booking-select">
                                <option value="">Selecione</option>
                                @foreach ($tiposRecursos as $tipo)
                                    <option value="{{ $tipo['id'] }}">{{ $tipo['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                        @error('tipoRecursoId') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="booking-label">Recurso especifico</label>
                        <div class="relative">
                            <select wire:model.live="recursoId" class="booking-select">
                                <option value="">Selecione</option>
                                @foreach ($recursos as $recurso)
                                    <option value="{{ $recurso['id'] }}">{{ $recurso['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                        @error('recursoId') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="booking-label">Departamento ou setor</label>
                        <div class="relative">
                            <select wire:model.live="departamentoId" class="booking-select">
                                <option value="">Selecione</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento['id'] }}">{{ $departamento['nome'] }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down picker-icon"></i>
                        </div>
                        @error('departamentoId') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="booking-label">Data</label>
                        <div class="picker-shell">
                            <input
                                wire:model.live="dataReserva"
                                data-picker="date"
                                data-placeholder="Selecione a data"
                                type="text"
                                placeholder="Selecione a data"
                                class="booking-input pr-11"
                            >
                            <i class="fa-regular fa-calendar picker-icon"></i>
                        </div>
                        @error('dataReserva') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="booking-label">Hora inicial</label>
                            <div class="relative">
                                <select wire:model="horaInicio" class="booking-select">
                                    <option value="">Selecione</option>
                                    @foreach ($horarios as $hora)
                                        <option value="{{ $hora }}">{{ $hora }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down picker-icon"></i>
                            </div>
                            @error('horaInicio') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="booking-label">Hora final</label>
                            <div class="relative">
                                <select wire:model="horaFim" class="booking-select">
                                    <option value="">Selecione</option>
                                    @foreach ($horarios as $hora)
                                        <option value="{{ $hora }}">{{ $hora }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down picker-icon"></i>
                            </div>
                            @error('horaFim') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="booking-label">Solicitante</label>
                        <input wire:model="solicitanteNome" type="text" placeholder="Nome completo" class="booking-input">
                        @error('solicitanteNome') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="booking-label">E-mail</label>
                        <input wire:model="solicitanteEmail" type="email" placeholder="nome@empresa.com" class="booking-input">
                        @error('solicitanteEmail') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="booking-label">Motivo da reserva</label>
                        <textarea wire:model="motivo" rows="3" placeholder="Ex: Reunião trimestral de alinhamento com a diretoria..." class="booking-input min-h-[7rem] resize-y"></textarea>
                        @error('motivo') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="booking-label">Convidados ou participantes</label>
                        <input wire:model="participantes" type="text" placeholder="Ex: joao.silva@empresa.com; maria.oliveira@empresa.com (separe com ponto e vírgula)" class="booking-input">
                        @error('participantes') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="booking-label">Observacoes</label>
                        <textarea wire:model="observacoes" rows="2" placeholder="Ex: Necessário projetor HDMI e suporte para videoconferência..." class="booking-input min-h-[6rem] resize-y"></textarea>
                    </div>
                </div>

                @if ($recursoSelecionadoArray)
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-ink-900">{{ $recursoSelecionadoArray['nome'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $recursoSelecionadoArray['localizacao'] ?: 'Sem localizacao informada' }}
                                    @if ($recursoSelecionadoArray['capacidade'])
                                        | Capacidade {{ $recursoSelecionadoArray['capacidade'] }} pessoas
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
                        x-on:click.prevent="Swal.fire({title: 'Enviar solicitacao?', text: 'O pedido ira para aprovacao e a agenda sera atualizada imediatamente.', icon: 'question', showCancelButton: true, confirmButtonText: 'Enviar solicitacao', cancelButtonText: 'Voltar', confirmButtonColor: '#0f4c81'}).then((result) => { if (result.isConfirmed) { $wire.reservar() } })"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:opacity-60"
                    >
                        <i class="fa-solid fa-paper-plane"></i>
                        Solicitar reserva
                    </button>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500 dark:text-sky-400">Agenda selecionada</p>
                            <h2 class="mt-2 text-2xl font-semibold text-ink-900 dark:text-white">{{ $recursoSelecionado?->nome ?? 'Selecione um recurso' }}</h2>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $hojeFormatado }}</p>
                        </div>
                        <div class="rounded-3xl bg-sky-50 dark:bg-slate-800/80 border border-sky-100 dark:border-slate-700/50 px-4 py-3 text-center min-w-[5.5rem] transition-colors duration-300">
                            <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-sky-600 dark:text-sky-400">Bloqueios</p>
                            <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-sky-300">{{ count($agenda) }}</p>
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
                                    <span class="status-badge {{ $item['status'] === 'confirmado' ? 'bg-emerald-100 text-emerald-700' : ($item['status'] === 'pendente_aprovacao' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $item['status_label'] }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                    <p><i class="fa-solid fa-user mr-2 text-slate-400"></i>{{ $item['solicitante'] }}</p>
                                    <p><i class="fa-solid fa-building mr-2 text-slate-400"></i>{{ $item['departamento'] }}</p>
                                    @if ($item['avaliado_por'])
                                        <p class="sm:col-span-2"><i class="fa-solid fa-user-shield mr-2 text-slate-400"></i>Avaliado por {{ $item['avaliado_por'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-sky-200 dark:border-slate-700/80 bg-sky-50/20 dark:bg-slate-900/40 p-8 text-center text-sm text-slate-500 dark:text-slate-400 transition-all duration-300">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-sky-100/50 dark:bg-slate-800/60 text-sky-500 dark:text-sky-400">
                                    <i class="fa-regular fa-calendar-xmark text-lg"></i>
                                </div>
                                <p class="font-medium">Nenhuma reserva ativa para este recurso nesta data.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-5 sm:p-7">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-500">Fluxo de aprovacao</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">1. Solicite</p>
                            <p class="mt-2 text-sm text-slate-700">O colaborador informa o departamento, o recurso, o horario e o contexto de uso.</p>
                        </div>
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">2. Aprove ou reprove</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $responsavelAprovacao }} recebe a fila e decide no painel administrativo.</p>
                        </div>
                        <div class="rounded-3xl bg-white p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">3. Libere o uso</p>
                            <p class="mt-2 text-sm text-slate-700">Ao aprovar, a reserva fica confirmada e o solicitante recebe notificacao por e-mail e no sistema.</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
