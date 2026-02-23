@extends('layouts.app')

@section('title', ' - Kanban Trabalhos')

@section('main_class')
max-w-[95vw]
@endsection

@section('content')
<div class="mx-auto max-w-[88rem]">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kanban de Trabalhos</h1>
            <p class="mt-1 text-sm text-slate-600">Arraste os cartões entre colunas. Quando todos os trabalhos de um negócio estiverem concluídos, o negócio passa à coluna Concluído.</p>
        </div>
        <a href="{{ route('negocios.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Ver Negócios</a>
    </div>

    {{-- Filtros: técnicos (círculos com iniciais) e tipo de trabalho --}}
    <div class="mb-4 flex flex-wrap items-center gap-4 rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium uppercase text-slate-500">Técnicos</span>
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach($tecnicosParaFiltro ?? [] as $t)
                    <button type="button" class="kanban-filtro-tecnico h-8 w-8 rounded-full border-2 border-slate-200 bg-slate-100 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1" data-tecnico-id="{{ $t['id'] }}" title="{{ $t['name'] }}">{{ $t['initials'] }}</button>
                @endforeach
            </div>
            <button type="button" id="kanban-filtro-tecnico-limpar" class="ml-1 hidden text-xs text-slate-500 underline hover:text-slate-700">Limpar</button>
        </div>
        <div class="h-4 w-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium uppercase text-slate-500">Serviço</span>
            <select id="kanban-filtro-servico" class="min-w-[180px] rounded border border-slate-300 px-2 py-1 text-sm text-slate-700 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Todos</option>
                @foreach($servicosParaFiltro ?? [] as $idx => $desc)
                    <option value="{{ $idx }}">{{ Str::limit($desc, 50) }}</option>
                @endforeach
            </select>
        </div>
        <div class="h-4 w-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium uppercase text-slate-500">Loja</span>
            <select id="kanban-filtro-loja" class="min-w-[180px] rounded border border-slate-300 px-2 py-1 text-sm text-slate-700 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Todas</option>
                @if($temTrabalhosSemLoja ?? false)
                    <option value="sem-loja">Sem loja</option>
                @endif
                @foreach($lojasParaFiltro ?? [] as $loja)
                    <option value="{{ $loja->id }}">{{ $loja->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>

@php
    $columnColors = [
        'a_fazer' => 'bg-emerald-50 border-emerald-200',
        'em_execucao' => 'bg-blue-50 border-blue-200',
        'pendente' => 'bg-amber-50 border-amber-200',
        'concluido' => 'bg-slate-100 border-slate-200',
    ];
    $cardBorderColors = [
        'a_fazer' => 'border-l-4 border-l-emerald-500',
        'em_execucao' => 'border-l-4 border-l-blue-500',
        'pendente' => 'border-l-4 border-l-amber-500',
        'concluido' => 'border-l-4 border-l-slate-500',
    ];
@endphp

<div class="flex gap-3 overflow-x-auto pb-4 justify-center" id="kanban-container">
    @foreach(\App\Models\Trabalho::ESTADOS as $estadoKey => $estadoLabel)
        <div class="kanban-column flex flex-shrink-0 w-72 min-w-72 flex-col rounded-lg border p-3 {{ $columnColors[$estadoKey] ?? 'bg-slate-50 border-slate-200' }}" data-status="{{ $estadoKey }}">
            <div class="mb-3 flex items-center justify-between gap-1">
                <h2 class="min-w-0 flex-1 break-words text-sm font-semibold text-slate-800">{{ $estadoLabel }}</h2>
                <span class="flex-shrink-0 rounded-full bg-white/80 px-1.5 py-0.5 text-xs font-medium text-slate-700 shadow-sm">{{ $trabalhosPorEstado[$estadoKey]->count() }}</span>
            </div>
            <div class="kanban-dropzone flex min-h-[280px] flex-1 flex-col space-y-2 rounded" ondrop="drop(event)" ondragover="allowDrop(event)">
                @foreach($trabalhosPorEstado[$estadoKey] as $trabalho)
                    @php
                        $processo = $trabalho->negocio->processo ?? null;
                        $tituloCard = $processo
                            ? ($processo->referencia . ($processo->designacao ? ' - ' . $processo->designacao : ''))
                            : $trabalho->designacao_negocio;
                        $descricao = $trabalho->descricao_servico;
                        $tipoLabel = $trabalho->tipo_trabalho_para_exibicao;
                        $prazoStr = $trabalho->prazo_para_exibicao ? $trabalho->prazo_para_exibicao->format('d/m/Y') : null;
                        $tecnicoNome = $trabalho->tecnico->name ?? null;
                        $tecnicoIdFiltro = $trabalho->id_tecnico ? (string)$trabalho->id_tecnico : 'em-aberto';
                        $servicoFiltroId = in_array($trabalho->descricao_servico, $servicosParaFiltro ?? []) ? (string)array_search($trabalho->descricao_servico, $servicosParaFiltro) : '';
                        $prazoAlerta = null;
                        if ($trabalho->estado !== 'concluido' && $trabalho->prazo_para_exibicao) {
                            $hoje = \Carbon\Carbon::today()->startOfDay();
                            $prazo = $trabalho->prazo_para_exibicao->startOfDay();
                            if ($prazo->lt($hoje)) {
                                $prazoAlerta = 'atrasado';
                            } elseif ($hoje->diffInDays($prazo, false) <= 2) {
                                $prazoAlerta = 'proximo';
                            }
                        }
                    @endphp
                    <div class="kanban-card group cursor-pointer rounded border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow {{ $cardBorderColors[$estadoKey] ?? '' }} @if($prazoAlerta === 'atrasado') kanban-prazo-atrasado @elseif($prazoAlerta === 'proximo') kanban-prazo-proximo @endif"
                         draggable="true"
                         ondragstart="drag(event)"
                         ondragend="window.kanbanJustDragged = true"
                         data-trabalho-id="{{ $trabalho->id }}"
                         data-negocio-id="{{ $trabalho->negocio->id }}"
                         data-estado="{{ $trabalho->estado }}"
                         data-prazo="{{ $trabalho->prazo_para_exibicao?->format('Y-m-d') ?? '' }}"
                         data-loja-id="{{ $trabalho->negocio->imovel->id_loja ?? '' }}"
                         data-tecnico-id="{{ $tecnicoIdFiltro }}"
                         data-servico-id="{{ $servicoFiltroId }}">
                        @if($tituloCard)
                            <div class="mb-1.5 text-sm font-semibold text-slate-800" title="{{ $tituloCard }}">{{ Str::limit($tituloCard, 40) }}</div>
                        @endif
                        <div class="mb-1.5 space-y-1 text-xs">
                            <div class="flex items-start gap-1.5">
                                <span class="flex-shrink-0 text-slate-500">Serviço:</span>
                                <span class="min-w-0 text-slate-700" title="{{ $descricao ?? '' }}">{{ $descricao ? Str::limit($descricao, 48) : '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="flex-shrink-0 text-slate-500">Tipo:</span>
                                @if($tipoLabel)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 font-medium text-blue-800">{{ $tipoLabel }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap card-prazo-row">
                                <span class="flex-shrink-0 text-slate-500">Prazo:</span>
                                <span class="card-prazo-alert-tag">@if($prazoAlerta === 'atrasado')<span class="rounded px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">Atrasado</span>@elseif($prazoAlerta === 'proximo')<span class="rounded px-1.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">Próximo</span>@endif</span>
                                <span class="card-prazo-val text-slate-600">{{ $prazoStr ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-600">
                            <svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="truncate card-tecnico-nome">{{ $tecnicoNome ?? 'Em aberto' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
</div>

{{-- Modal: trabalhos do negócio + chat de observações --}}
<div id="modal-trabalhos-negocio" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" aria-hidden="true">
    <div class="flex max-h-[90vh] w-full max-w-4xl flex-col rounded-xl bg-white shadow-xl sm:flex-row">
        <div class="flex flex-col border-b border-slate-200 sm:border-b-0 sm:border-r sm:w-2/3 min-w-0">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 id="modal-trabalhos-titulo" class="text-sm font-semibold text-slate-800 truncate pr-2"></h3>
                <button type="button" onclick="closeModalTrabalhos()" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Fechar">&times;</button>
            </div>
            <div class="flex-1 overflow-auto p-3 min-h-0">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase text-slate-500">
                            <th class="pb-2 pr-2">Trabalho</th>
                            <th class="pb-2 pr-2 whitespace-nowrap">Tipo</th>
                            <th class="pb-2 pr-2 whitespace-nowrap">Prazo</th>
                            <th class="pb-2 pr-2">Técnico</th>
                            <th class="pb-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="modal-trabalhos-tbody" class="divide-y divide-slate-100">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex flex-col w-full sm:w-1/3 border-t sm:border-t-0 border-slate-200">
            <div class="border-b border-slate-200 px-3 py-2">
                <h4 class="text-sm font-medium text-slate-700">Observações</h4>
            </div>
            <div id="modal-observacoes-list" class="overflow-y-auto p-3 space-y-2 h-[220px] bg-slate-50/50">
            </div>
            <div class="border-t border-slate-200 p-3 flex-shrink-0 relative">
                <form id="form-nova-observacao" onsubmit="submitObservacao(event)" class="flex gap-2">
                    <input type="hidden" id="modal-negocio-id" value="">
                    <div class="flex-1 relative">
                        <textarea id="modal-observacao-input" name="observacao" rows="1" class="w-full min-h-[60px] rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="Use @ para mencionar utilizador ou trabalho (ex: @João tens que fazer @Licenciamento)" maxlength="5000" required></textarea>
                        <div id="modal-mention-dropdown" class="absolute left-0 right-0 bottom-full mb-1 hidden max-h-40 overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg z-10">
                        </div>
                    </div>
                    <button type="submit" class="flex-shrink-0 self-end rounded-lg bg-slate-700 px-3 py-2 text-sm text-white hover:bg-slate-600">Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.kanban-column { min-height: 320px; }
.kanban-card { transition: all 0.2s; }
.kanban-card.dragging { opacity: 0.5; transform: rotate(2deg); }
.kanban-dropzone.drag-over { background-color: #e2e8f0; border: 2px dashed #64748b; border-radius: 8px; }
.kanban-card { cursor: pointer; }
.kanban-card.dragging { cursor: grabbing; }
.kanban-card.filter-hidden { display: none !important; }
.kanban-filtro-tecnico.active { border-color: #475569; background-color: #cbd5e1; color: #0f172a; }
.kanban-prazo-proximo { border-left-color: #eab308 !important; background-color: #fffbeb; }
.kanban-prazo-atrasado { border-left-color: #dc2626 !important; background-color: #fef2f2; }
.chat-mention { background-color: #e0f2fe; color: #0369a1; border-radius: 4px; padding: 0 4px; font-weight: 500; }
.chat-mention.mention-trabalho { background-color: #fef3c7; color: #b45309; }
#modal-mention-dropdown .mention-item { padding: 6px 10px; cursor: pointer; font-size: 12px; }
#modal-mention-dropdown .mention-item:hover { background-color: #f1f5f9; }
#modal-mention-dropdown .mention-item.mention-trabalho { color: #b45309; }
#modal-mention-dropdown .mention-section { padding: 4px 10px 2px; font-size: 10px; font-weight: 600; text-transform: uppercase; color: #64748b; }
</style>
@endpush

@push('scripts')
<script>
const updateEstadoUrl = '{{ route("trabalhos.update-estado", ":id") }}';
const trabalhosBaseUrl = '{{ url("trabalhos") }}';
const negociosBaseUrl = '{{ url("negocios") }}';
const csrfToken = '{{ csrf_token() }}';
let draggedElement = null;
window.kanbanJustDragged = false;
window.kanbanFiltroTecnicos = [];

function applyKanbanFilters() {
    const servicoVal = document.getElementById('kanban-filtro-servico')?.value ?? '';
    const lojaVal = document.getElementById('kanban-filtro-loja')?.value ?? '';
    const tecnicosSet = window.kanbanFiltroTecnicos;
    document.querySelectorAll('.kanban-card').forEach(card => {
        const cardTecnico = card.dataset.tecnicoId || '';
        const cardServico = card.dataset.servicoId ?? '';
        const cardLoja = (card.dataset.lojaId ?? '').toString();
        const matchTecnico = tecnicosSet.length === 0 || tecnicosSet.includes(cardTecnico);
        const matchServico = servicoVal === '' || cardServico === servicoVal;
        const matchLoja = lojaVal === '' || (lojaVal === 'sem-loja' ? cardLoja === '' : cardLoja === lojaVal);
        if (matchTecnico && matchServico && matchLoja) {
            card.classList.remove('filter-hidden');
        } else {
            card.classList.add('filter-hidden');
        }
    });
    document.querySelectorAll('.kanban-column').forEach(col => {
        const status = col.dataset.status;
        const count = col.querySelectorAll('.kanban-card:not(.filter-hidden)').length;
        const span = col.querySelector('.mb-3 span');
        if (span) span.textContent = count;
    });
    const btnLimpar = document.getElementById('kanban-filtro-tecnico-limpar');
    if (btnLimpar) btnLimpar.classList.toggle('hidden', tecnicosSet.length === 0);
}

function updatePrazoAlerts() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    document.querySelectorAll('.kanban-card').forEach(card => {
        const estado = card.dataset.estado;
        const prazoStr = (card.dataset.prazo || '').trim();
        card.classList.remove('kanban-prazo-atrasado', 'kanban-prazo-proximo');
        const alertTag = card.querySelector('.card-prazo-alert-tag');
        const prazoVal = card.querySelector('.card-prazo-val');
        const formatPrazoDisplay = (ymd) => {
            if (!ymd) return '—';
            const [y, m, d] = ymd.split('-');
            return d + '/' + m + '/' + y;
        };
        if (estado === 'concluido') {
            if (alertTag) alertTag.innerHTML = '';
            if (prazoVal) prazoVal.textContent = formatPrazoDisplay(prazoStr) || '—';
            return;
        }
        if (!prazoStr) {
            if (alertTag) alertTag.innerHTML = '';
            if (prazoVal) prazoVal.textContent = '—';
            return;
        }
        const prazoDate = new Date(prazoStr + 'T00:00:00');
        const diffDays = Math.ceil((prazoDate - today) / (1000 * 60 * 60 * 24));
        let alertType = null;
        if (diffDays < 0) alertType = 'atrasado';
        else if (diffDays <= 2) alertType = 'proximo';
        if (alertType) card.classList.add('kanban-prazo-' + alertType);
        if (alertTag) {
            if (alertType === 'atrasado') alertTag.innerHTML = '<span class="rounded px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">Atrasado</span>';
            else if (alertType === 'proximo') alertTag.innerHTML = '<span class="rounded px-1.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">Próximo</span>';
            else alertTag.innerHTML = '';
        }
        if (prazoVal) prazoVal.textContent = formatPrazoDisplay(prazoStr);
    });
}

function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
}

function drag(ev) {
    window.kanbanJustDragged = false;
    draggedElement = ev.target.closest('.kanban-card');
    ev.dataTransfer.effectAllowed = 'move';
    ev.dataTransfer.setData('text/html', draggedElement.outerHTML);
    draggedElement.classList.add('dragging');
}

function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');
    if (!draggedElement) return;

    const dropzone = ev.currentTarget.closest('.kanban-dropzone');
    const column = ev.currentTarget.closest('.kanban-column');
    const newEstado = column.dataset.status;
    const trabalhoId = draggedElement.dataset.trabalhoId;
    const oldEstado = draggedElement.dataset.estado;

    if (newEstado === oldEstado) {
        window.kanbanJustDragged = true;
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        return;
    }

    draggedElement.remove();
    draggedElement.dataset.estado = newEstado;
    draggedElement.classList.remove('dragging');
    const cardColors = { a_fazer: 'border-l-emerald-500', em_execucao: 'border-l-blue-500', pendente: 'border-l-amber-500', concluido: 'border-l-slate-500' };
    draggedElement.classList.remove('border-l-emerald-500', 'border-l-blue-500', 'border-l-amber-500', 'border-l-slate-500');
    if (cardColors[newEstado]) draggedElement.classList.add('border-l-4', cardColors[newEstado]);
    dropzone.appendChild(draggedElement);
    sortColumnByPrazo(dropzone);
    updateColumnCount(oldEstado);
    updateColumnCount(newEstado);
    updatePrazoAlerts();
    updateTrabalhoEstado(trabalhoId, newEstado);
    window.kanbanJustDragged = true;
    draggedElement = null;
}

document.querySelectorAll('.kanban-dropzone').forEach(zone => {
    zone.addEventListener('dragleave', function(e) { e.currentTarget.classList.remove('drag-over'); });
});

document.getElementById('kanban-container').addEventListener('click', function(e) {
    const card = e.target.closest('.kanban-card');
    if (!card) return;
    if (window.kanbanJustDragged) {
        window.kanbanJustDragged = false;
        return;
    }
    const negocioId = card.dataset.negocioId;
    if (negocioId) openModalTrabalhos(negocioId);
});

document.querySelectorAll('.kanban-filtro-tecnico').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.tecnicoId;
        const idx = window.kanbanFiltroTecnicos.indexOf(id);
        if (idx === -1) {
            window.kanbanFiltroTecnicos.push(id);
            this.classList.add('active');
        } else {
            window.kanbanFiltroTecnicos.splice(idx, 1);
            this.classList.remove('active');
        }
        applyKanbanFilters();
    });
});
document.getElementById('kanban-filtro-tecnico-limpar')?.addEventListener('click', function() {
    window.kanbanFiltroTecnicos = [];
    document.querySelectorAll('.kanban-filtro-tecnico.active').forEach(b => b.classList.remove('active'));
    applyKanbanFilters();
});
document.getElementById('kanban-filtro-servico')?.addEventListener('change', applyKanbanFilters);
document.getElementById('kanban-filtro-loja')?.addEventListener('change', applyKanbanFilters);

function sortColumnByPrazo(dropzone) {
    const cards = Array.from(dropzone.querySelectorAll('.kanban-card'));
    if (cards.length <= 1) return;
    cards.sort((a, b) => {
        const pa = (a.dataset.prazo || '').trim();
        const pb = (b.dataset.prazo || '').trim();
        if (!pa && !pb) return 0;
        if (!pa) return 1;
        if (!pb) return -1;
        return pa.localeCompare(pb);
    });
    cards.forEach(card => dropzone.appendChild(card));
}

function updateColumnCount(estado) {
    const column = document.querySelector(`.kanban-column[data-status="${estado}"]`);
    if (column) {
        const count = column.querySelectorAll('.kanban-card').length;
        const span = column.querySelector('.mb-3 span');
        if (span) span.textContent = count;
    }
}

function updateTrabalhoEstado(trabalhoId, estado) {
    const url = updateEstadoUrl.replace(':id', trabalhoId);
    fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ estado: estado })
    })
    .then(r => r.json())
    .then(data => { if (!data.success) window.location.reload(); })
    .catch(() => window.location.reload());
}

function openModalTrabalhos(negocioId) {
    const modal = document.getElementById('modal-trabalhos-negocio');
    document.getElementById('modal-negocio-id').value = negocioId;
    document.getElementById('modal-observacao-input').value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    fetch(negociosBaseUrl + '/' + negocioId + '/modal-trabalhos', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(data => {
        document.getElementById('modal-trabalhos-titulo').textContent = data.titulo || 'Trabalhos do negócio';
        const tbody = document.getElementById('modal-trabalhos-tbody');
        const tecnicos = data.tecnicos || [];
        const tecnicosSelect = (t) => {
            const opts = '<option value="">Em aberto</option>' + tecnicos.map(u => {
                const sel = (t.id_tecnico != null && String(u.id) === String(t.id_tecnico)) ? ' selected' : '';
                return `<option value="${u.id}"${sel}>${escapeHtml(u.name)}</option>`;
            }).join('');
            return `<select class="modal-tecnico-select w-full max-w-[140px] rounded border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" data-trabalho-id="${t.id}" onclick="event.stopPropagation()">${opts}</select>`;
        };
        tbody.innerHTML = data.trabalhos.length === 0
            ? '<tr><td colspan="5" class="py-4 text-center text-slate-500">Sem trabalhos.</td></tr>'
            : data.trabalhos.map(t => `
                <tr class="text-slate-700">
                    <td class="py-2 pr-2 align-top">${escapeHtml(t.servico)}</td>
                    <td class="py-2 pr-2 whitespace-nowrap align-top">${escapeHtml(t.tipo)}</td>
                    <td class="py-2 pr-2 whitespace-nowrap align-top">${escapeHtml(t.prazo)}</td>
                    <td class="py-2 pr-2 align-top">${tecnicosSelect(t)}</td>
                    <td class="py-2 align-top">${escapeHtml(t.estado)}</td>
                </tr>
            `).join('');
        tbody.querySelectorAll('.modal-tecnico-select').forEach(sel => {
            sel.addEventListener('change', function() {
                updateTrabalhoTecnico(this.dataset.trabalhoId, this.value || null, this);
            });
        });
        window.modalMentionData = {
            tecnicos: (data.tecnicos || []).map(u => ({ type: 'user', label: u.name })),
            trabalhos: (data.trabalhos || []).map(t => ({ type: 'trabalho', label: t.servico }))
        };
        const list = document.getElementById('modal-observacoes-list');
        if (data.observacoes.length === 0) {
            list.innerHTML = '<p class="text-sm text-slate-500">Ainda não há observações. Use @ para mencionar utilizador ou trabalho.</p>';
        } else {
            list.innerHTML = data.observacoes.map(o => `
                <div class="rounded-lg bg-white border border-slate-200 p-2.5 shadow-sm">
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <span class="font-medium text-slate-700">${escapeHtml(o.user_name)}</span>
                        <span>${escapeHtml(o.created_at)}</span>
                    </div>
                    <p class="text-sm text-slate-800 whitespace-pre-wrap">${formatMentionHtml(o.observacao)}</p>
                </div>
            `).join('');
        }
    })
    .catch(() => {
        document.getElementById('modal-trabalhos-titulo').textContent = 'Erro ao carregar';
        document.getElementById('modal-trabalhos-tbody').innerHTML = '<tr><td colspan="5" class="py-4 text-center text-red-600">Não foi possível carregar os dados.</td></tr>';
    });
}

function closeModalTrabalhos() {
    const modal = document.getElementById('modal-trabalhos-negocio');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatMentionHtml(text) {
    if (!text) return '';
    const escaped = escapeHtml(text);
    return escaped.replace(/@([^\s@]+)/g, '<span class="chat-mention">@$1</span>');
}

function updateTrabalhoTecnico(trabalhoId, idTecnico, selectEl) {
    const url = trabalhosBaseUrl + '/' + trabalhoId + '/tecnico';
    const body = JSON.stringify({ id_tecnico: idTecnico ? parseInt(idTecnico, 10) : null });
    selectEl.disabled = true;
    fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: body
    })
    .then(r => r.json())
    .then(data => {
        selectEl.disabled = false;
        if (data.success) {
            const nome = data.tecnico_nome || 'Em aberto';
            selectEl.title = nome;
            const card = document.querySelector('.kanban-card[data-trabalho-id="' + trabalhoId + '"]');
            if (card) {
                const span = card.querySelector('.card-tecnico-nome');
                if (span) span.textContent = nome;
            }
        }
    })
    .catch(() => { selectEl.disabled = false; });
}

function submitObservacao(ev) {
    ev.preventDefault();
    const negocioId = document.getElementById('modal-negocio-id').value;
    const input = document.getElementById('modal-observacao-input');
    const text = input.value.trim();
    if (!text) return;
    input.disabled = true;
    fetch(negociosBaseUrl + '/' + negocioId + '/observacoes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ observacao: text })
    })
    .then(r => r.json())
    .then(data => {
        input.value = '';
        input.disabled = false;
        const list = document.getElementById('modal-observacoes-list');
        const emptyMsg = list.querySelector('p.text-slate-500');
        if (emptyMsg) emptyMsg.remove();
        const div = document.createElement('div');
        div.className = 'rounded-lg bg-white border border-slate-200 p-2.5 shadow-sm';
        div.innerHTML = `
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <span class="font-medium text-slate-700">${escapeHtml(data.user_name)}</span>
                <span>${escapeHtml(data.created_at)}</span>
            </div>
            <p class="text-sm text-slate-800 whitespace-pre-wrap">${formatMentionHtml(data.observacao)}</p>
        `;
        list.appendChild(div);
        list.scrollTop = list.scrollHeight;
    })
    .catch(() => { input.disabled = false; });
}

document.getElementById('modal-trabalhos-negocio').addEventListener('click', function(e) {
    if (e.target === this) closeModalTrabalhos();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const dd = document.getElementById('modal-mention-dropdown');
        if (dd && !dd.classList.contains('hidden')) { dd.classList.add('hidden'); e.preventDefault(); }
        else closeModalTrabalhos();
    }
});

const modalObservacaoInput = document.getElementById('modal-observacao-input');
const modalMentionDropdown = document.getElementById('modal-mention-dropdown');

function hideMentionDropdown() {
    modalMentionDropdown.classList.add('hidden');
}

function showMentionDropdown(fragment) {
    const data = window.modalMentionData || { tecnicos: [], trabalhos: [] };
    const frag = (fragment || '').toLowerCase();
    const match = (label) => !frag || label.toLowerCase().includes(frag);
    const users = data.tecnicos.filter(m => match(m.label));
    const works = data.trabalhos.filter(m => match(m.label));
    if (users.length === 0 && works.length === 0) {
        modalMentionDropdown.innerHTML = '<div class="mention-item text-slate-400 px-3 py-2">Nenhum resultado</div>';
    } else {
        let html = '';
        if (users.length > 0) {
            html += '<div class="mention-section">Utilizadores</div>';
            users.forEach(m => { html += '<div class="mention-item" data-label="' + escapeHtml(m.label) + '">' + escapeHtml(m.label) + '</div>'; });
        }
        if (works.length > 0) {
            html += '<div class="mention-section">Trabalhos</div>';
            works.forEach(m => { html += '<div class="mention-item mention-trabalho" data-label="' + escapeHtml(m.label) + '">' + escapeHtml(m.label) + '</div>'; });
        }
        modalMentionDropdown.innerHTML = html;
    }
    modalMentionDropdown.classList.remove('hidden');
}

modalObservacaoInput.addEventListener('input', function() {
    const ta = this;
    const val = ta.value;
    const pos = ta.selectionStart || val.length;
    const atIdx = val.lastIndexOf('@', pos - 1);
    if (atIdx === -1) { hideMentionDropdown(); return; }
    const fragment = val.substring(atIdx + 1, pos);
    if (/\s/.test(fragment)) { hideMentionDropdown(); return; }
    showMentionDropdown(fragment);
});

modalObservacaoInput.addEventListener('blur', function() {
    setTimeout(hideMentionDropdown, 150);
});

modalMentionDropdown.addEventListener('mousedown', function(e) {
    const item = e.target.closest('.mention-item[data-label]');
    if (!item) return;
    e.preventDefault();
    const label = item.getAttribute('data-label');
    const ta = modalObservacaoInput;
    const val = ta.value;
    const pos = ta.selectionStart || val.length;
    const atIdx = val.lastIndexOf('@', pos - 1);
    if (atIdx === -1) return;
    const newVal = val.substring(0, atIdx) + '@' + label + ' ' + val.substring(pos);
    ta.value = newVal;
    ta.focus();
    const newPos = atIdx + label.length + 2;
    ta.setSelectionRange(newPos, newPos);
    hideMentionDropdown();
});

updatePrazoAlerts();
setInterval(updatePrazoAlerts, 60000);
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') updatePrazoAlerts();
});
</script>
@endpush
@endsection
