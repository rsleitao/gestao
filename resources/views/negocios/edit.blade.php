@extends('layouts.app')

@section('title', ' - Editar negócio')

@section('content')
<div class="mb-6">
    <a href="{{ route('negocios.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar negócio</h1>
</div>

<form method="post" action="{{ route('negocios.update', $negocio) }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')

    <x-select label="Requerente" name="id_requerente" :options="$requerentes->pluck('nome', 'id')->all()" :selected="$negocio->id_requerente" required placeholder="Selecione o requerente" />
    <x-select label="A quem faturar" name="id_requerente_fatura" :options="$requerentes->pluck('nome', 'id')->all()" :selected="$negocio->id_requerente_fatura" placeholder="Selecione..." />
    <x-select label="Imóvel (opcional)" name="id_imovel" :options="$imoveis->mapWithKeys(fn($i) => [$i->id => $i->morada . ' - ' . ($i->localidade_imovel ?? '')])->all()" :selected="$negocio->id_imovel" placeholder="—" />
    <x-input label="Designação" name="designacao" :value="$negocio->designacao" required />
    <x-select label="Estado" name="status" :options="\App\Models\Negocio::STATUS" :selected="$negocio->status" required />
    <x-textarea label="Observações" name="observacoes" :value="$negocio->observacoes" />

    @if($negocio->processo)
        <div class="rounded-lg bg-blue-50 p-4">
            <p class="text-sm font-medium text-blue-800">Processo associado:</p>
            <a href="{{ route('processos.edit', $negocio->processo) }}" class="text-blue-600 hover:underline">{{ $negocio->processo->referencia }}</a>
        </div>
    @endif

    @if($negocio->data_convertido)
        <p class="text-sm text-slate-600">Convertido em: {{ $negocio->data_convertido->format('d/m/Y H:i') }}</p>
    @endif

    @if($negocio->data_faturado)
        <p class="text-sm text-slate-600">Faturado em: {{ $negocio->data_faturado->format('d/m/Y H:i') }}</p>
    @endif

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('negocios.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>

{{-- Secção de Itens do Negócio --}}
<div class="mt-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-800">Itens do Negócio</h2>
        <div class="flex items-center gap-4">
            <div class="text-lg font-bold text-slate-800">Total: {{ $negocio->total_formatado }}</div>
            <select id="select-servico-edit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">Carregar serviço...</option>
                @foreach($servicos as $s)
                    <option value="{{ $s->id }}" data-nome="{{ $s->nome }}" data-preco="{{ $s->preco_base ?? 0 }}" data-descricao="{{ $s->descricao ?? '' }}">{{ $s->nome }} @if($s->preco_base)({{ number_format($s->preco_base, 2, ',', ' ') }} €)@endif</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Lista de itens existentes --}}
    @if($negocio->itens->count() > 0)
        <div class="mb-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Descrição</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Preço</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Qtd</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Prazo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-slate-600">Tipo</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Total</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($negocio->itens as $item)
                        <tr class="hover:bg-slate-50" id="item-row-{{ $item->id }}" data-item-id="{{ $item->id }}"
                            data-descricao="{{ e($item->descricao) }}"
                            data-preco="{{ $item->preco }}"
                            data-quantidade="{{ $item->quantidade }}"
                            data-prazo="{{ $item->prazo_data?->format('Y-m-d') ?? '' }}"
                            data-tipo="{{ $item->tipo_trabalho ?? '' }}"
                            data-total="{{ $item->total_formatado }}"
                            data-prazo-display="{{ $item->prazo_data ? $item->prazo_data->format('d/m/Y') : '—' }}"
                            data-tipo-display="{{ $item->tipo_trabalho ? (\App\Models\NegocioItem::TIPOS_TRABALHO[$item->tipo_trabalho] ?? $item->tipo_trabalho) : '—' }}">
                            {{-- Modo visualização --}}
                            <td class="px-4 py-2 font-medium text-slate-800 cell-descricao">{{ $item->descricao }}</td>
                            <td class="px-4 py-2 text-right text-slate-600 cell-preco">{{ number_format($item->preco, 2, ',', ' ') }} €</td>
                            <td class="px-4 py-2 text-right text-slate-600 cell-quantidade">{{ number_format($item->quantidade, 2, ',', ' ') }}</td>
                            <td class="px-4 py-2 text-right text-slate-600 cell-prazo">{{ $item->prazo_data ? $item->prazo_data->format('d/m/Y') : '—' }}</td>
                            <td class="px-4 py-2 text-slate-600 cell-tipo">
                                @if($item->tipo_trabalho)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">{{ \App\Models\NegocioItem::TIPOS_TRABALHO[$item->tipo_trabalho] ?? $item->tipo_trabalho }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right font-medium text-slate-800 cell-total">{{ $item->total_formatado }}</td>
                            <td class="px-4 py-2 text-right cell-acoes">
                                <button type="button" onclick="entrarModoEdicao({{ $item->id }})" class="text-slate-600 hover:text-slate-900 text-sm">Editar</button>
                                <form method="post" action="{{ route('negocios.itens.destroy', [$negocio, $item]) }}" class="inline ml-2" onsubmit="return confirm('Eliminar este item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Botão para adicionar item manualmente --}}
    <div class="mt-4 flex justify-end">
        <button type="button" onclick="mostrarFormManual()" id="btn-adicionar-manual" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">+ Adicionar Trabalho Manual</button>
    </div>

    {{-- Formulário para adicionar/editar item (escondido por padrão) --}}
    <div id="form-item" class="mt-4 hidden rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-3 font-medium text-slate-800" id="form-title">Adicionar Item</h3>
        <form method="post" action="{{ route('negocios.itens.store', $negocio) }}" id="form-item-form">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <x-input label="Descrição" name="descricao" id="item-descricao" required />
                </div>
                <x-input label="Preço (€)" name="preco" type="number" step="0.01" min="0" id="item-preco" required />
                <x-input label="Quantidade" name="quantidade" type="number" step="0.01" min="0.01" id="item-quantidade" value="1" required />
                <div>
                    <label for="item-prazo" class="mb-1 block text-sm font-medium text-slate-700">Prazo</label>
                    <input type="date" name="prazo_data" id="item-prazo" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label for="item-tipo" class="mb-1 block text-sm font-medium text-slate-700">Tipo <span class="text-red-500">*</span></label>
                    <select name="tipo_trabalho" id="item-tipo" required class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Selecione...</option>
                        <option value="licenciamento">Licenciamento</option>
                        <option value="execucao">Execução</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-700">Adicionar</button>
                <button type="button" onclick="cancelarEdicao()" id="btn-cancelar" class="hidden rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
                <button type="button" onclick="esconderFormManual()" id="btn-cancelar-manual" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal para escolher tipo de trabalho --}}
<div id="modal-tipo-trabalho" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">Escolher Tipo de Trabalho</h3>
            <button onclick="fecharModalTipoTrabalho()" class="text-slate-400 hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <p class="mb-4 text-sm text-slate-600" id="modal-servico-nome"></p>
        <div class="grid gap-3">
            <button onclick="selecionarTipoTrabalho('licenciamento')" class="flex items-center gap-3 rounded-lg border-2 border-blue-200 bg-blue-50 p-4 text-left transition hover:border-blue-400 hover:bg-blue-100">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-slate-800">Licenciamento</div>
                    <div class="text-sm text-slate-600">Trabalho de licenciamento</div>
                </div>
            </button>
            <button onclick="selecionarTipoTrabalho('execucao')" class="flex items-center gap-3 rounded-lg border-2 border-green-200 bg-green-50 p-4 text-left transition hover:border-green-400 hover:bg-green-100">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-slate-800">Execução</div>
                    <div class="text-sm text-slate-600">Trabalho de execução</div>
                </div>
            </button>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="fecharModalTipoTrabalho()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let itemEditando = null;
let servicoSelecionado = null;

function mostrarModalTipoTrabalho(servicoNome) {
    document.getElementById('modal-servico-nome').textContent = `Serviço: ${servicoNome}`;
    document.getElementById('modal-tipo-trabalho').classList.remove('hidden');
    document.getElementById('modal-tipo-trabalho').classList.add('flex');
}

function fecharModalTipoTrabalho() {
    document.getElementById('modal-tipo-trabalho').classList.add('hidden');
    document.getElementById('modal-tipo-trabalho').classList.remove('flex');
    servicoSelecionado = null;
    // Reset select
    const select = document.getElementById('select-servico-edit');
    if (select) select.value = '';
}

function selecionarTipoTrabalho(tipo) {
    if (!servicoSelecionado) {
        fecharModalTipoTrabalho();
        return;
    }
    
    const option = servicoSelecionado;
    
    // Criar formulário temporário e submeter
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("negocios.itens.store", $negocio) }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const descricao = document.createElement('input');
    descricao.type = 'hidden';
    descricao.name = 'descricao';
    descricao.value = option.dataset.nome;
    form.appendChild(descricao);
    
    const preco = document.createElement('input');
    preco.type = 'hidden';
    preco.name = 'preco';
    preco.value = option.dataset.preco || '0';
    form.appendChild(preco);
    
    const quantidade = document.createElement('input');
    quantidade.type = 'hidden';
    quantidade.name = 'quantidade';
    quantidade.value = '1';
    form.appendChild(quantidade);
    
    const tipoInput = document.createElement('input');
    tipoInput.type = 'hidden';
    tipoInput.name = 'tipo_trabalho';
    tipoInput.value = tipo;
    form.appendChild(tipoInput);
    
    document.body.appendChild(form);
    form.submit();
    
    fecharModalTipoTrabalho();
}

const updateUrlBase = '{{ route("negocios.itens.update", [$negocio, ":id"]) }}';
const destroyUrlBase = '{{ route("negocios.itens.destroy", [$negocio, ":id"]) }}';
const csrfToken = '{{ csrf_token() }}';

function escapeAttr(s) {
    return (s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function entrarModoEdicao(itemId) {
    const row = document.getElementById('item-row-' + itemId);
    if (!row || row.classList.contains('editing')) return;
    row.classList.add('editing');
    const d = row.dataset;
    const descricao = escapeAttr(d.descricao);
    const preco = d.preco || '';
    const quantidade = d.quantidade || '';
    const prazo = d.prazo || '';
    const tipo = d.tipo || '';
    const updateUrl = updateUrlBase.replace(':id', itemId);
    const totalDisplay = d.total || '—';
    row.innerHTML = `
        <td class="px-4 py-2">
            <input type="text" form="form-edit-${itemId}" name="descricao" value="${descricao}" required class="w-full rounded border border-slate-300 px-2 py-1 text-sm">
        </td>
        <td class="px-4 py-2 text-right">
            <input type="number" form="form-edit-${itemId}" name="preco" step="0.01" min="0" value="${preco}" required class="w-24 rounded border border-slate-300 px-2 py-1 text-right text-sm">
        </td>
        <td class="px-4 py-2 text-right">
            <input type="number" form="form-edit-${itemId}" name="quantidade" step="0.01" min="0.01" value="${quantidade}" required class="w-20 rounded border border-slate-300 px-2 py-1 text-right text-sm">
        </td>
        <td class="px-4 py-2">
            <input type="date" form="form-edit-${itemId}" name="prazo_data" value="${prazo}" class="rounded border border-slate-300 px-2 py-1 text-sm">
        </td>
        <td class="px-4 py-2">
            <select form="form-edit-${itemId}" name="tipo_trabalho" required class="rounded border border-slate-300 px-2 py-1 text-sm">
                <option value="">Selecione...</option>
                <option value="licenciamento" ${tipo === 'licenciamento' ? 'selected' : ''}>Licenciamento</option>
                <option value="execucao" ${tipo === 'execucao' ? 'selected' : ''}>Execução</option>
            </select>
        </td>
        <td class="px-4 py-2 text-right font-medium text-slate-800">${totalDisplay}</td>
        <td class="px-4 py-2 text-right">
            <form id="form-edit-${itemId}" method="post" action="${updateUrl}" class="inline">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="PUT">
                <button type="submit" class="rounded bg-slate-800 px-3 py-1 text-sm text-white hover:bg-slate-700">Guardar</button>
                <button type="button" onclick="sairModoEdicao(${itemId})" class="ml-2 rounded border border-slate-300 px-3 py-1 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
            </form>
        </td>
    `;
}

function sairModoEdicao(itemId) {
    const row = document.getElementById('item-row-' + itemId);
    if (!row) return;
    const d = row.dataset;
    const tipoDisplay = d.tipoDisplay || '—';
    const tipoClass = d.tipo ? 'rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800' : 'text-slate-400';
    const destroyUrl = destroyUrlBase.replace(':id', itemId);
    row.classList.remove('editing');
    row.innerHTML = `
        <td class="px-4 py-2 font-medium text-slate-800 cell-descricao">${(d.descricao || '').replace(/</g, '&lt;')}</td>
        <td class="px-4 py-2 text-right text-slate-600 cell-preco">${parseFloat(d.preco || 0).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €</td>
        <td class="px-4 py-2 text-right text-slate-600 cell-quantidade">${parseFloat(d.quantidade || 0).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        <td class="px-4 py-2 text-right text-slate-600 cell-prazo">${d.prazoDisplay || '—'}</td>
        <td class="px-4 py-2 text-slate-600 cell-tipo"><span class="${tipoClass}">${tipoDisplay}</span></td>
        <td class="px-4 py-2 text-right font-medium text-slate-800 cell-total">${d.total || '—'}</td>
        <td class="px-4 py-2 text-right cell-acoes">
            <button type="button" onclick="entrarModoEdicao(${itemId})" class="text-slate-600 hover:text-slate-900 text-sm">Editar</button>
            <form method="post" action="${destroyUrl}" class="inline ml-2" onsubmit="return confirm('Eliminar este item?');">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
            </form>
        </td>
    `;
}

function cancelarEdicao() {
    const form = document.getElementById('form-item-form');
    if (!form) return;
    document.getElementById('form-title').textContent = 'Adicionar Item';
    form.reset();
    form.action = '{{ route("negocios.itens.store", $negocio) }}';
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    document.getElementById('btn-cancelar').classList.add('hidden');
    if (document.getElementById('btn-cancelar-manual')) document.getElementById('btn-cancelar-manual').classList.remove('hidden');
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.textContent = 'Adicionar';
}

function mostrarFormManual() {
    document.getElementById('form-item').classList.remove('hidden');
    document.getElementById('btn-adicionar-manual').classList.add('hidden');
    document.getElementById('btn-cancelar-manual').classList.remove('hidden');
    document.getElementById('btn-cancelar').classList.add('hidden');
    // Reset form para modo "adicionar"
    const form = document.getElementById('form-item-form');
    document.getElementById('form-title').textContent = 'Adicionar Item';
    form.reset();
    document.getElementById('item-quantidade').value = '1';
    form.action = '{{ route("negocios.itens.store", $negocio) }}';
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    form.querySelector('button[type="submit"]').textContent = 'Adicionar';
    itemEditando = null;
    document.getElementById('form-item').scrollIntoView({ behavior: 'smooth' });
}

function esconderFormManual() {
    document.getElementById('form-item').classList.add('hidden');
    document.getElementById('btn-adicionar-manual').classList.remove('hidden');
    document.getElementById('btn-cancelar-manual').classList.add('hidden');
    document.getElementById('btn-cancelar').classList.add('hidden');
    const form = document.getElementById('form-item-form');
    form.reset();
    form.action = '{{ route("negocios.itens.store", $negocio) }}';
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    document.getElementById('form-title').textContent = 'Adicionar Item';
    form.querySelector('button[type="submit"]').textContent = 'Adicionar';
    itemEditando = null;
}

// Carregar serviço na edição - mostrar modal para escolher tipo
document.getElementById('select-servico-edit')?.addEventListener('change', function() {
    if (this.value) {
        const option = this.options[this.selectedIndex];
        servicoSelecionado = option;
        mostrarModalTipoTrabalho(option.dataset.nome);
    }
});

// Fechar modal ao clicar fora
document.getElementById('modal-tipo-trabalho')?.addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalTipoTrabalho();
    }
});
</script>
@endpush
@endsection
