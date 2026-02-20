@extends('layouts.app')

@section('title', ' - Kanban Negócios')

@section('main_class')
max-w-[95vw]
@endsection

@section('content')
<div class="mx-auto max-w-[88rem]">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kanban de Negócios</h1>
            <p class="mt-1 text-sm text-slate-600">Arraste os cartões entre colunas para alterar o estado</p>
        </div>
        <a href="{{ route('negocios.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Ver Lista</a>
    </div>

@php
    $columnColors = [
        'pendente' => 'bg-amber-50 border-amber-200',
        'aceite' => 'bg-emerald-50 border-emerald-200',
        'cancelado' => 'bg-red-50 border-red-200',
        'em_trabalho' => 'bg-blue-50 border-blue-200',
        'concluido' => 'bg-slate-100 border-slate-200',
        'faturado' => 'bg-slate-50 border-slate-200',
    ];
    $cardBorderColors = [
        'pendente' => 'border-l-4 border-l-amber-500',
        'aceite' => 'border-l-4 border-l-emerald-500',
        'cancelado' => 'border-l-4 border-l-red-500',
        'em_trabalho' => 'border-l-4 border-l-blue-500',
        'concluido' => 'border-l-4 border-l-slate-400',
        'faturado' => 'border-l-4 border-l-slate-500',
    ];
@endphp

<div class="flex gap-3 overflow-x-auto pb-4 justify-center" id="kanban-container">
    @foreach(\App\Models\Negocio::STATUS as $statusKey => $statusLabel)
        <div class="kanban-column flex-shrink-0 w-56 min-w-56 rounded-lg border p-3 {{ $columnColors[$statusKey] ?? 'bg-slate-50 border-slate-200' }}" data-status="{{ $statusKey }}">
            <div class="mb-3 flex items-center justify-between gap-1">
                <h2 class="min-w-0 flex-1 break-words text-sm font-semibold text-slate-800">{{ $statusLabel }}</h2>
                <span class="flex-shrink-0 rounded-full bg-white/80 px-1.5 py-0.5 text-xs font-medium text-slate-700 shadow-sm">{{ $negociosPorStatus[$statusKey]->count() }}</span>
            </div>
            <div class="kanban-dropzone min-h-[120px] space-y-2" ondrop="drop(event)" ondragover="allowDrop(event)">
                @foreach($negociosPorStatus[$statusKey] as $negocio)
                    @php
                        $temProcessoCriadoPorEste = $negocio->processo && $negocio->processo->id_negocio_origem == $negocio->id;
                    @endphp
                    <div class="kanban-card group cursor-move rounded border border-slate-200 bg-white p-2.5 shadow-sm transition hover:shadow {{ $cardBorderColors[$statusKey] ?? '' }}" 
                         draggable="true" 
                         ondragstart="drag(event)" 
                         data-negocio-id="{{ $negocio->id }}"
                         data-status="{{ $negocio->status }}"
                         data-tem-processo-criado="{{ $temProcessoCriadoPorEste ? '1' : '0' }}">
                        <div class="mb-1 flex items-start justify-between gap-1">
                            <a href="{{ route('negocios.edit', $negocio) }}" class="min-w-0 flex-1 truncate text-sm font-medium text-slate-800 hover:text-slate-600" title="{{ $negocio->designacao }}">
                                {{ Str::limit($negocio->designacao, 22) }}
                            </a>
                        </div>
                        <div class="text-xs text-slate-600">
                            <div class="flex items-center gap-1 truncate">
                                <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="truncate">{{ $negocio->requerente->nome ?? '—' }}</span>
                            </div>
                            @if($negocio->imovel)
                                <div class="mt-0.5 flex items-center gap-1 truncate">
                                    <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span class="truncate">{{ Str::limit($negocio->imovel->morada, 18) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-slate-100 pt-1.5">
                            <div class="text-xs font-semibold text-slate-800">{{ $negocio->total_formatado }}</div>
                            <div class="text-[11px] text-slate-500">{{ $negocio->itens->count() }} {{ $negocio->itens->count() === 1 ? 'item' : 'itens' }}</div>
                        </div>
                        @if($negocio->processo)
                            <div class="mt-1.5 text-[11px]">
                                <span class="rounded bg-blue-100 px-1.5 py-0.5 text-blue-800">{{ $negocio->processo->codigo_formatado }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
</div>

{{-- Modal aviso "Em trabalho" (apenas informativo) --}}
<div id="modal-em-trabalho" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Estado "Em trabalho"</h3>
        </div>
        <p class="mb-6 text-sm text-slate-600">
            O estado <strong>Em trabalho</strong> é atribuído automaticamente quando existirem trabalhos em execução associados a este negócio (futuro Kanban de trabalhos).
        </p>
        <div class="flex justify-end">
            <button type="button" id="modal-em-trabalho-ok" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">OK</button>
        </div>
    </div>
</div>

{{-- Modal aviso "Concluído" (apenas informativo) --}}
<div id="modal-concluido" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Estado "Concluído"</h3>
        </div>
        <p class="mb-6 text-sm text-slate-600">
            O estado <strong>Concluído</strong> é atribuído automaticamente quando todos os trabalhos associados a este negócio estiverem concluídos (futuro Kanban de trabalhos).
        </p>
        <div class="flex justify-end">
            <button type="button" id="modal-concluido-ok" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">OK</button>
        </div>
    </div>
</div>

{{-- Modal aviso bloqueio de movimento --}}
<div id="modal-bloqueio" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 id="modal-bloqueio-titulo" class="text-lg font-semibold text-slate-800">Movimento não permitido</h3>
        </div>
        <p id="modal-bloqueio-texto" class="mb-6 text-sm text-slate-600"></p>
        <div class="flex justify-end">
            <button type="button" id="modal-bloqueio-ok" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">OK</button>
        </div>
    </div>
</div>

{{-- Modal reverter para Pendente ou Cancelado --}}
<div id="modal-reverter-pendente" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center gap-3">
            <div id="modal-reverter-icon" class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 id="modal-reverter-titulo" class="text-lg font-semibold text-slate-800">Reverter para Pendente</h3>
        </div>
        <p id="modal-reverter-texto" class="mb-6 text-sm text-slate-600">
            Ao voltar para <strong>Pendente</strong>, o processo criado por este negócio (se existir) e os dados associados serão eliminados. Os itens do negócio mantêm-se, mas a ligação ao processo será removida.
        </p>
        <p class="mb-6 text-sm text-slate-500">
            Deseja continuar?
        </p>
        <div class="flex justify-end gap-3">
            <button type="button" id="modal-reverter-cancelar" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
            <button type="button" id="modal-reverter-confirmar" class="rounded-lg bg-amber-600 px-4 py-2 text-sm text-white hover:bg-amber-700">Sim, reverter</button>
        </div>
    </div>
</div>

@push('styles')
<style>
.kanban-column {
    min-height: 320px;
}

.kanban-card {
    transition: all 0.2s;
}

.kanban-card.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
}

.kanban-dropzone.drag-over {
    background-color: #e2e8f0;
    border: 2px dashed #64748b;
    border-radius: 8px;
}

.kanban-card:hover {
    cursor: grab;
}

.kanban-card:active {
    cursor: grabbing;
}
</style>
@endpush

@push('scripts')
<script>
const updateStatusUrl = '{{ route("negocios.update-status", ":id") }}';
const csrfToken = '{{ csrf_token() }}';

let draggedElement = null;

function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
}

function drag(ev) {
    draggedElement = ev.target.closest('.kanban-card');
    ev.dataTransfer.effectAllowed = 'move';
    ev.dataTransfer.setData('text/html', draggedElement.outerHTML);
    draggedElement.classList.add('dragging');
}

let pendingDrop = null;

function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');
    
    if (!draggedElement) return;
    
    const dropzone = ev.currentTarget.closest('.kanban-dropzone');
    const column = ev.currentTarget.closest('.kanban-column');
    const newStatus = column.dataset.status;
    const negocioId = draggedElement.dataset.negocioId;
    const oldStatus = draggedElement.dataset.status;
    
    if (newStatus === oldStatus) {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        return;
    }
    
    // Bloquear: Pendente -> Faturado
    if (oldStatus === 'pendente' && newStatus === 'faturado') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-bloqueio');
        document.getElementById('modal-bloqueio-titulo').textContent = 'Movimento não permitido';
        document.getElementById('modal-bloqueio-texto').textContent = 'Não é possível passar diretamente de "Pendente" para "Faturado". O negócio deve passar por "Aceite", "Em Trabalho" e "Concluído" primeiro.';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // Bloquear: Aceite -> Faturado
    if (oldStatus === 'aceite' && newStatus === 'faturado') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-bloqueio');
        document.getElementById('modal-bloqueio-titulo').textContent = 'Movimento não permitido';
        document.getElementById('modal-bloqueio-texto').textContent = 'Não é possível passar diretamente de "Aceite" para "Faturado". O negócio deve passar por "Em Trabalho" e "Concluído" primeiro.';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // "Em trabalho" é definido automaticamente pelo Kanban de trabalhos (futuro)
    if (newStatus === 'em_trabalho') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-em-trabalho');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // "Concluído" é definido automaticamente quando todos os trabalhos estiverem concluídos (futuro)
    if (newStatus === 'concluido' && oldStatus !== 'faturado') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-concluido');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // Concluído só pode passar para Faturado
    if (oldStatus === 'concluido' && newStatus !== 'faturado') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-bloqueio');
        document.getElementById('modal-bloqueio-titulo').textContent = 'Movimento não permitido';
        document.getElementById('modal-bloqueio-texto').textContent = 'Negócios concluídos só podem passar para o estado "Faturado".';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // Faturado só pode voltar para Concluído
    if (oldStatus === 'faturado' && newStatus !== 'concluido') {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
        const modal = document.getElementById('modal-bloqueio');
        document.getElementById('modal-bloqueio-titulo').textContent = 'Movimento não permitido';
        document.getElementById('modal-bloqueio-texto').textContent = 'Negócios faturados só podem voltar para o estado "Concluído".';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return;
    }
    
    // Faturado -> Concluído: mostrar modal de confirmação
    if (oldStatus === 'faturado' && newStatus === 'concluido') {
        pendingDrop = { dropzone, newStatus, negocioId, oldStatus };
        const icon = document.getElementById('modal-reverter-icon');
        const titulo = document.getElementById('modal-reverter-titulo');
        const texto = document.getElementById('modal-reverter-texto');
        const btnConfirmar = document.getElementById('modal-reverter-confirmar');
        
        icon.className = 'flex h-12 w-12 items-center justify-center rounded-full bg-slate-100';
        icon.querySelector('svg').className = 'h-6 w-6 text-slate-600';
        titulo.textContent = 'Reverter para Concluído';
        texto.innerHTML = 'Ao voltar para <strong>Concluído</strong>, o negócio deixará de estar faturado. Deseja continuar?';
        btnConfirmar.textContent = 'Sim, reverter';
        btnConfirmar.className = 'rounded-lg bg-slate-600 px-4 py-2 text-sm text-white hover:bg-slate-700';
        
        document.getElementById('modal-reverter-pendente').classList.remove('hidden');
        document.getElementById('modal-reverter-pendente').classList.add('flex');
        return;
    }
    
    // Reverter de Aceite para Pendente: mostrar modal de confirmação
    if (newStatus === 'pendente' && oldStatus === 'aceite') {
        pendingDrop = { dropzone, newStatus, negocioId, oldStatus };
        const icon = document.getElementById('modal-reverter-icon');
        const titulo = document.getElementById('modal-reverter-titulo');
        const texto = document.getElementById('modal-reverter-texto');
        const btnConfirmar = document.getElementById('modal-reverter-confirmar');
        
        icon.className = 'flex h-12 w-12 items-center justify-center rounded-full bg-amber-100';
        icon.querySelector('svg').className = 'h-6 w-6 text-amber-600';
        titulo.textContent = 'Reverter para Pendente';
        texto.innerHTML = draggedElement.dataset.temProcessoCriado === '1'
            ? 'Ao voltar para <strong>Pendente</strong>, o <strong>processo criado por este negócio será eliminado</strong> e os dados associados (trabalhos, etc.) serão removidos. Os itens do negócio mantêm-se. Deseja continuar?'
            : 'Ao voltar para <strong>Pendente</strong>, a ligação ao processo será removida. Os itens do negócio mantêm-se. Deseja continuar?';
        btnConfirmar.textContent = 'Sim, reverter';
        btnConfirmar.className = 'rounded-lg bg-amber-600 px-4 py-2 text-sm text-white hover:bg-amber-700';
        
        document.getElementById('modal-reverter-pendente').classList.remove('hidden');
        document.getElementById('modal-reverter-pendente').classList.add('flex');
        return;
    }
    
    // Cancelar negócio: mostrar modal de confirmação se tem processo criado
    if (newStatus === 'cancelado' && draggedElement.dataset.temProcessoCriado === '1') {
        pendingDrop = { dropzone, newStatus, negocioId, oldStatus };
        const icon = document.getElementById('modal-reverter-icon');
        const titulo = document.getElementById('modal-reverter-titulo');
        const texto = document.getElementById('modal-reverter-texto');
        const btnConfirmar = document.getElementById('modal-reverter-confirmar');
        
        icon.className = 'flex h-12 w-12 items-center justify-center rounded-full bg-red-100';
        icon.querySelector('svg').className = 'h-6 w-6 text-red-600';
        titulo.textContent = 'Cancelar Negócio';
        texto.innerHTML = 'Ao cancelar este negócio, o <strong>processo criado por este negócio será eliminado</strong> e todos os dados associados (trabalhos, etc.) serão removidos. Os itens do negócio mantêm-se. Esta ação não pode ser desfeita. Deseja continuar?';
        btnConfirmar.textContent = 'Sim, cancelar';
        btnConfirmar.className = 'rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700';
        
        document.getElementById('modal-reverter-pendente').classList.remove('hidden');
        document.getElementById('modal-reverter-pendente').classList.add('flex');
        return;
    }
    
    executarMovimento(draggedElement, dropzone, newStatus, oldStatus, negocioId);
    draggedElement = null;
}

function executarMovimento(draggedEl, dropzone, newStatus, oldStatus, negocioId) {
    draggedEl.remove();
    draggedEl.dataset.status = newStatus;
    draggedEl.classList.remove('dragging');
    // Aplicar cor do card da nova coluna
    const cardColors = { pendente: 'border-l-amber-500', aceite: 'border-l-emerald-500', cancelado: 'border-l-red-500', em_trabalho: 'border-l-blue-500', concluido: 'border-l-slate-400', faturado: 'border-l-slate-500' };
    draggedEl.classList.remove('border-l-amber-500', 'border-l-emerald-500', 'border-l-red-500', 'border-l-blue-500', 'border-l-slate-400', 'border-l-slate-500');
    if (cardColors[newStatus]) draggedEl.classList.add('border-l-4', cardColors[newStatus]);
    dropzone.appendChild(draggedEl);
    updateColumnCount(oldStatus);
    updateColumnCount(newStatus);
    updateNegocioStatus(negocioId, newStatus);
}

function fecharModalReverter() {
    document.getElementById('modal-reverter-pendente').classList.add('hidden');
    document.getElementById('modal-reverter-pendente').classList.remove('flex');
    if (draggedElement) {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
    }
    pendingDrop = null;
}

document.getElementById('modal-em-trabalho-ok').addEventListener('click', function() {
    document.getElementById('modal-em-trabalho').classList.add('hidden');
    document.getElementById('modal-em-trabalho').classList.remove('flex');
});
document.getElementById('modal-em-trabalho').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
});

document.getElementById('modal-concluido-ok').addEventListener('click', function() {
    document.getElementById('modal-concluido').classList.add('hidden');
    document.getElementById('modal-concluido').classList.remove('flex');
});
document.getElementById('modal-concluido').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
});

document.getElementById('modal-bloqueio-ok').addEventListener('click', function() {
    document.getElementById('modal-bloqueio').classList.add('hidden');
    document.getElementById('modal-bloqueio').classList.remove('flex');
});
document.getElementById('modal-bloqueio').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
});

document.getElementById('modal-reverter-cancelar').addEventListener('click', fecharModalReverter);
document.getElementById('modal-reverter-confirmar').addEventListener('click', function() {
    if (pendingDrop && draggedElement) {
        executarMovimento(draggedElement, pendingDrop.dropzone, pendingDrop.newStatus, pendingDrop.oldStatus, pendingDrop.negocioId);
        draggedElement = null;
    }
    fecharModalReverter();
    pendingDrop = null;
});
document.getElementById('modal-reverter-pendente').addEventListener('click', function(e) {
    if (e.target === this) fecharModalReverter();
});

// Remover drag-over quando sair da zona
document.querySelectorAll('.kanban-dropzone').forEach(zone => {
    zone.addEventListener('dragleave', function(e) {
        e.currentTarget.classList.remove('drag-over');
    });
});

function updateColumnCount(status) {
    const column = document.querySelector(`.kanban-column[data-status="${status}"]`);
    if (column) {
        const count = column.querySelectorAll('.kanban-card').length;
        const countElement = column.querySelector('.mb-4 span');
        if (countElement) {
            countElement.textContent = count;
        }
    }
}

function updateNegocioStatus(negocioId, newStatus) {
    const url = updateStatusUrl.replace(':id', negocioId);
    
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Opcional: mostrar notificação de sucesso
            console.log('Estado atualizado:', data.message);
        } else {
            console.error('Erro ao atualizar estado');
            // Recarregar a página em caso de erro
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        // Recarregar a página em caso de erro
        window.location.reload();
    });
}
</script>
@endpush
@endsection
