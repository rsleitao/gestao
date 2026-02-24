<x-app-layout>
    @push('styles')
    <style>
        @keyframes dashboard-today-pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 6px 2px rgba(34, 197, 94, 0.6); }
            50% { opacity: 0.5; box-shadow: 0 0 4px 1px rgba(34, 197, 94, 0.35); }
        }
        .dashboard-today-dot {
            animation: dashboard-today-pulse 2.2s ease-in-out infinite;
        }
    </style>
    @endpush
    <div class="py-6 space-y-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Resumo rápido --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
                <a href="{{ route('requerentes.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow transition">
                    <h3 class="text-sm font-medium text-slate-500">Requerentes</h3>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $totalRequerentes ?? 0 }}</p>
                    <span class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista →</span>
                </a>
                <a href="{{ route('servicos.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow transition">
                    <h3 class="text-sm font-medium text-slate-500">Serviços</h3>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $totalServicos ?? 0 }}</p>
                    <span class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista →</span>
                </a>
                <a href="{{ route('processos.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow transition">
                    <h3 class="text-sm font-medium text-slate-500">Processos</h3>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $totalProcessos ?? 0 }}</p>
                    <span class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista →</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {{-- Coluna esquerda: Meus trabalhos pendentes + Negócios concluídos --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Os meus trabalhos (por prazo) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <h2 class="text-base font-semibold text-slate-800">Os meus trabalhos pendentes</h2>
                            <a href="{{ route('trabalhos.kanban') }}" class="text-sm text-sky-600 hover:text-sky-800">Ver Kanban →</a>
                        </div>
                        <div class="overflow-x-auto">
                            @if(($meusTrabalhosPendentes ?? collect())->isEmpty())
                                <p class="p-4 text-sm text-slate-500">Nenhum trabalho pendente associado a si.</p>
                            @else
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-slate-600">Serviço / Negócio</th>
                                            <th class="px-4 py-2 text-left font-medium text-slate-600">Prazo</th>
                                            <th class="px-4 py-2 text-left font-medium text-slate-600">Estado</th>
                                            <th class="px-4 py-2 text-right font-medium text-slate-600"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach($meusTrabalhosPendentes as $t)
                                            @php
                                                $processo = $t->negocio->processo ?? null;
                                                $titulo = $processo ? ($processo->referencia . ($processo->designacao ? ' - ' . $processo->designacao : '')) : $t->designacao_negocio;
                                                $prazoD = $t->prazo_para_exibicao;
                                                $prazoStr = $prazoD ? $prazoD->format('d/m/Y') : '—';
                                                $alerta = null;
                                                if ($prazoD) {
                                                    $hoje = \Carbon\Carbon::today();
                                                    if ($prazoD->isPast()) {
                                                        $alerta = 'atrasado';
                                                    } elseif ($hoje->diffInDays($prazoD, false) <= 2) {
                                                        $alerta = 'proximo';
                                                    }
                                                }
                                            @endphp
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-2">
                                                    <span class="font-medium text-slate-800">{{ Str::limit($titulo ?? $t->descricao_servico ?? '—', 40) }}</span>
                                                    @if($t->descricao_servico && $titulo != $t->descricao_servico)
                                                        <br><span class="text-slate-500 text-xs">{{ Str::limit($t->descricao_servico, 35) }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    @if($alerta === 'atrasado')
                                                        <span class="rounded px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">Atrasado</span>
                                                    @elseif($alerta === 'proximo')
                                                        <span class="rounded px-1.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">Próximo</span>
                                                    @endif
                                                    <span class="text-slate-600">{{ $prazoStr }}</span>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ \App\Models\Trabalho::ESTADOS[$t->estado] ?? $t->estado }}</span>
                                                </td>
                                                <td class="px-4 py-2 text-right">
                                                    <a href="{{ route('negocios.edit', $t->negocio) }}" class="text-sky-600 hover:text-sky-800 text-xs">Abrir negócio</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                    {{-- Negócios concluídos (recentes) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <h2 class="text-base font-semibold text-slate-800">Negócios concluídos (recentes)</h2>
                            <a href="{{ route('negocios.index') }}" class="text-sm text-sky-600 hover:text-sky-800">Ver todos →</a>
                        </div>
                        <div class="divide-y divide-slate-200">
                            @if(($negociosConcluidosRecentes ?? collect())->isEmpty())
                                <p class="p-4 text-sm text-slate-500">Nenhum negócio concluído recentemente.</p>
                            @else
                                @foreach($negociosConcluidosRecentes as $n)
                                    @php
                                        $proc = $n->processo ?? null;
                                        $designacao = $proc ? ($proc->referencia . ($proc->designacao ? ' - ' . $proc->designacao : '')) : $n->designacao;
                                    @endphp
                                    <a href="{{ route('negocios.edit', $n) }}" class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 text-left">
                                        <span class="font-medium text-slate-800 truncate flex-1">{{ Str::limit($designacao ?? 'Negócio #'.$n->id, 50) }}</span>
                                        <span class="text-xs text-slate-500 flex-shrink-0 ml-2">{{ $n->updated_at->format('d/m/Y') }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Coluna direita: Calendário de prazos --}}
                <div class="space-y-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <h2 class="text-base font-semibold text-slate-800">Prazos (próximos 60 dias)</h2>
                        </div>
                        <div class="p-4">
                            @php
                                $diasComPrazos = array_keys($prazosPorDia ?? []);
                                $proximos7 = array_slice($diasComPrazos, 0, 14);
                            @endphp
                            @if(empty($proximos7))
                                <p class="text-sm text-slate-500">Sem prazos nos próximos dias.</p>
                            @else
                                <ul class="space-y-2 text-sm">
                                    @foreach($proximos7 as $dia)
                                        @php
                                            $data = \Carbon\Carbon::parse($dia);
                                            $itens = $prazosPorDia[$dia] ?? [];
                                        @endphp
                                        <li class="flex items-start gap-2">
                                            <span class="font-medium text-slate-700 whitespace-nowrap">{{ $data->format('d/m') }}</span>
                                            <span class="text-slate-500">—</span>
                                            <span class="text-slate-700">{{ count($itens) }} {{ count($itens) === 1 ? 'trabalho' : 'trabalhos' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('trabalhos.kanban') }}" class="mt-3 inline-block text-sm text-sky-600 hover:text-sky-800">Ver no Kanban →</a>
                            @endif
                        </div>
                    </div>

                    {{-- Mini calendário do mês atual --}}
                    @php
                        $calendarPrazosData = [];
                        foreach ($prazosPorDia ?? [] as $diaKey => $trabalhos) {
                            $calendarPrazosData[$diaKey] = [];
                            foreach ($trabalhos as $t) {
                                $processo = $t->negocio->processo ?? null;
                                $titulo = $processo ? ($processo->referencia . ($processo->designacao ? ' - ' . $processo->designacao : '')) : $t->designacao_negocio;
                                $calendarPrazosData[$diaKey][] = [
                                    'titulo' => Str::limit($titulo ?? $t->descricao_servico ?? '—', 55),
                                    'url' => route('negocios.edit', $t->negocio),
                                ];
                            }
                        }
                    @endphp
                    <script>window.dashboardCalendarPrazos = @json($calendarPrazosData);</script>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="dashboardCalendar(window.dashboardCalendarPrazos || {})">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between gap-2">
                            <button type="button" class="rounded p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700" @click="prevMonth()" aria-label="Mês anterior">←</button>
                            <h2 class="text-base font-semibold text-slate-800" x-text="monthLabel"></h2>
                            <button type="button" class="rounded p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700" @click="nextMonth()" aria-label="Mês seguinte">→</button>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                <template x-for="d in ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom']" :key="d">
                                    <div class="font-medium text-slate-500" x-text="d"></div>
                                </template>
                                <template x-for="(day, i) in getCalendarDays()" :key="i">
                                    <div
                                        class="relative p-1 rounded min-h-[1.5rem] flex items-center justify-center"
                                        :class="{
                                            'text-slate-300': day.isOtherMonth,
                                            'bg-slate-100': day.isWeekend && !day.isOtherMonth && !day.isToday && !day.hasPrazo,
                                            'bg-slate-200': day.isWeekend && day.isOtherMonth,
                                            'bg-sky-100 font-semibold text-sky-800': day.isToday && !day.isOtherMonth,
                                            'bg-amber-100 text-amber-900 cursor-pointer hover:bg-amber-200': day.hasPrazo && !day.isOtherMonth,
                                            'text-slate-600': !day.isOtherMonth && !day.isToday && !day.hasPrazo
                                        }"
                                        :title="day.hasPrazo ? (day.count + (day.count === 1 ? ' trabalho para entregar' : ' trabalhos para entregar')) : ''"
                                        @click="day.hasPrazo && openModal(day.dateKey, day.dateLabel)"
                                    >
                                        <span x-text="day.day"></span>
                                        <span x-show="day.isToday" x-cloak class="dashboard-today-dot absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_6px_2px_rgba(34,197,94,0.6)]" aria-hidden="true"></span>
                                    </div>
                                </template>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Dias a amarelo têm prazos (passe o rato; clique para ver). Azul é hoje.</p>
                        </div>
                        {{-- Modal: trabalhos do dia --}}
                        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="modalOpen = false">
                            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[80vh] flex flex-col" @click.stop>
                                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                                    <h3 class="text-sm font-semibold text-slate-800" x-text="modalDate ? 'Prazos a ' + modalDate : ''"></h3>
                                    <button type="button" class="text-slate-400 hover:text-slate-600" @click="modalOpen = false" aria-label="Fechar">&times;</button>
                                </div>
                                <ul class="overflow-auto p-4 space-y-2">
                                    <template x-for="(item, i) in modalWorks" :key="i">
                                        <li>
                                            <a :href="item.url" class="block rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 hover:bg-slate-50 hover:border-sky-300" x-text="item.titulo"></a>
                                        </li>
                                    </template>
                                </ul>
                                <div class="border-t border-slate-200 px-4 py-2 text-xs text-slate-500" x-show="modalWorks.length">
                                    <span x-text="modalWorks.length + (modalWorks.length === 1 ? ' trabalho' : ' trabalhos')"></span> com prazo neste dia.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var MESES_PT = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        function dashboardCalendar(prazosData) {
            var hoje = new Date();
            return {
                prazosData: prazosData || {},
                currentMonth: { year: hoje.getFullYear(), month: hoje.getMonth() },
                modalOpen: false,
                modalDate: null,
                modalWorks: [],
                get monthLabel() {
                    return MESES_PT[this.currentMonth.month] + ' ' + this.currentMonth.year;
                },
                prevMonth() {
                    if (this.currentMonth.month === 0) {
                        this.currentMonth = { year: this.currentMonth.year - 1, month: 11 };
                    } else {
                        this.currentMonth = { year: this.currentMonth.year, month: this.currentMonth.month - 1 };
                    }
                },
                nextMonth() {
                    if (this.currentMonth.month === 11) {
                        this.currentMonth = { year: this.currentMonth.year + 1, month: 0 };
                    } else {
                        this.currentMonth = { year: this.currentMonth.year, month: this.currentMonth.month + 1 };
                    }
                },
                getCalendarDays() {
                    var y = this.currentMonth.year, m = this.currentMonth.month;
                    var first = new Date(y, m, 1);
                    var last = new Date(y, m + 1, 0);
                    var startDay = first.getDay();
                    var diffToMonday = startDay === 0 ? -6 : 1 - startDay;
                    var start = new Date(y, m, first.getDate() + diffToMonday);
                    var endDay = last.getDay();
                    var diffToSunday = endDay === 0 ? 0 : 7 - endDay;
                    var end = new Date(y, m + 1, 0, 23, 59, 59);
                    end.setDate(last.getDate() + diffToSunday);
                    var days = [];
                    var d = new Date(start);
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    while (d <= end) {
                        var dateKey = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                        var prazos = this.prazosData[dateKey] || [];
                        var dCopy = new Date(d);
                        dCopy.setHours(0, 0, 0, 0);
                        var dayOfWeek = d.getDay();
                        days.push({
                            day: d.getDate(),
                            dateKey: dateKey,
                            dateLabel: String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear(),
                            isToday: dCopy.getTime() === today.getTime(),
                            isOtherMonth: d.getMonth() !== m,
                            isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
                            hasPrazo: prazos.length > 0,
                            count: prazos.length
                        });
                        d.setDate(d.getDate() + 1);
                    }
                    return days;
                },
                openModal(dateKey, dateLabel) {
                    this.modalDate = dateLabel;
                    this.modalWorks = this.prazosData[dateKey] || [];
                    this.modalOpen = true;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
