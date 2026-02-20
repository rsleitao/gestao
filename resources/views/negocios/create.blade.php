@extends('layouts.app')

@section('title', ' - Novo negócio')

@section('content')
<div class="mb-6">
    <a href="{{ route('negocios.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo negócio</h1>
</div>

<form method="post" action="{{ route('negocios.store') }}" id="form-negocio" class="space-y-6">
    @csrf

    {{-- Escolher tipo de negócio --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Tipo de Negócio</h2>
        <div class="flex gap-4">
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 p-4 hover:bg-slate-50">
                <input type="radio" name="tipo_negocio" value="novo" checked onchange="toggleTipoNegocio()" class="h-4 w-4 text-slate-600">
                <span class="font-medium">Novo Negócio</span>
            </label>
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 p-4 hover:bg-slate-50">
                <input type="radio" name="tipo_negocio" value="processo_existente" onchange="toggleTipoNegocio()" class="h-4 w-4 text-slate-600">
                <span class="font-medium">Negócio de Processo Existente</span>
            </label>
        </div>
    </div>

    {{-- Opção: Processo Existente --}}
    <div id="opcao-processo-existente" class="hidden rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">Processo Existente</h2>
        <x-select label="Selecione o processo" name="id_processo" :options="$processos->mapWithKeys(fn($p) => [$p->id => $p->referencia . ' - ' . ($p->requerente->nome ?? '') . ($p->imovel ? ' (' . $p->imovel->morada . ')' : '')])->all()" placeholder="Selecione..." :required="false" />
        <div id="info-processo" class="mt-4 hidden rounded-lg bg-blue-50 p-4">
            <p class="text-sm"><strong>Requerente:</strong> <span id="proc-requerente"></span></p>
            <p class="text-sm"><strong>Imóvel:</strong> <span id="proc-imovel"></span></p>
        </div>
    </div>

    {{-- Opção: Novo Negócio --}}
    <div id="opcao-novo-negocio" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Dados do Negócio</h2>
        <x-select label="Requerente" name="id_requerente" :options="$requerentes->pluck('nome', 'id')->all()" required placeholder="Selecione o requerente" />
        <x-select label="A quem faturar" name="id_requerente_fatura" :options="$requerentes->pluck('nome', 'id')->all()" required placeholder="Selecione..." />
        
        <h3 class="mt-6 text-md font-semibold text-slate-800">Novo Imóvel</h3>
        <x-input label="NIP" name="imovel_nip" />
        <x-input label="Morada" name="imovel_morada" required />
        <div class="grid gap-4 sm:grid-cols-3">
            <x-select label="Distrito" name="imovel_id_distrito" :options="$distritos->pluck('nome', 'id')->all()" placeholder="—" />
            <x-select label="Concelho" name="imovel_id_concelho" :options="[]" placeholder="—" />
            <x-select label="Freguesia" name="imovel_id_freguesia" :options="[]" placeholder="—" />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input label="Código postal" name="imovel_cod_postal" required />
            <x-input label="Localidade" name="imovel_localidade" required />
        </div>
        <x-input label="Coordenadas" name="imovel_coordenadas" />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input label="Potência" name="imovel_potencia" type="number" step="0.01" />
            <x-input label="Tensão" name="imovel_tensao" />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input label="Área (m²)" name="imovel_area_imovel" type="number" step="0.01" />
            <x-input label="Pisos" name="imovel_pisos" type="number" min="0" />
        </div>
        <x-input label="Tipo de imóvel" name="imovel_tipo_imovel" />
        <x-select label="Loja" name="imovel_id_loja" :options="$lojas->pluck('nome', 'id')->all()" placeholder="—" />
        <x-textarea label="Descrição do imóvel" name="imovel_descricao" />
    </div>

    {{-- Dados comuns --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <x-input label="Designação" name="designacao" required />
        <x-textarea label="Observações" name="observacoes" />
    </div>

    {{-- Secção de Trabalhos (Itens) --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Trabalhos</h2>
            <div class="flex items-center gap-4">
                <div class="text-lg font-bold text-slate-800">Total: <span id="total-negocio">0,00 €</span></div>
                <select id="select-servico" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                    <option value="">Carregar serviço...</option>
                    @foreach($servicos as $s)
                        <option value="{{ $s->id }}" data-nome="{{ $s->nome }}" data-preco="{{ $s->preco_base ?? 0 }}" data-descricao="{{ $s->descricao ?? '' }}">{{ $s->nome }} @if($s->preco_base)({{ number_format($s->preco_base, 2, ',', ' ') }} €)@endif</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="itens-container" class="space-y-3">
            {{-- Itens serão adicionados aqui via JavaScript --}}
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" onclick="adicionarItem()" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">+ Adicionar Trabalho</button>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar Negócio</button>
        <a href="{{ route('negocios.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>

@push('scripts')
<script>
let itemIndex = 0;
const processos = @json($processos->map(fn($p) => ['id' => $p->id, 'requerente' => $p->requerente->nome ?? '', 'imovel' => $p->imovel ? $p->imovel->morada . ' - ' . ($p->imovel->localidade_imovel ?? '') : 'Sem imóvel'])->all());

function toggleTipoNegocio() {
    const tipo = document.querySelector('input[name="tipo_negocio"]:checked').value;
    const opcaoNovo = document.getElementById('opcao-novo-negocio');
    const opcaoProcesso = document.getElementById('opcao-processo-existente');
    
    // Campos obrigatórios do novo negócio
    const requerenteSelect = document.querySelector('select[name="id_requerente"]');
    const requerenteFaturaSelect = document.querySelector('select[name="id_requerente_fatura"]');
    const imovelMorada = document.querySelector('input[name="imovel_morada"]');
    const imovelCodPostal = document.querySelector('input[name="imovel_cod_postal"]');
    const imovelLocalidade = document.querySelector('input[name="imovel_localidade"]');
    const processoSelect = document.querySelector('select[name="id_processo"]');
    
    if (tipo === 'processo_existente') {
        opcaoNovo.classList.add('hidden');
        opcaoProcesso.classList.remove('hidden');
        
        // Remover required dos campos ocultos do novo negócio
        [requerenteSelect, requerenteFaturaSelect, imovelMorada, imovelCodPostal, imovelLocalidade].forEach(campo => {
            if (campo) {
                campo.removeAttribute('required');
                campo.closest('div')?.classList.add('hidden');
            }
        });
        
        // Tornar obrigatório o campo de processo
        if (processoSelect) {
            processoSelect.setAttribute('required', 'required');
        }
    } else {
        opcaoNovo.classList.remove('hidden');
        opcaoProcesso.classList.add('hidden');
        
        // Restaurar required nos campos do novo negócio
        if (requerenteSelect) {
            requerenteSelect.setAttribute('required', 'required');
            requerenteSelect.closest('div')?.classList.remove('hidden');
        }
        if (requerenteFaturaSelect) {
            requerenteFaturaSelect.setAttribute('required', 'required');
            requerenteFaturaSelect.closest('div')?.classList.remove('hidden');
        }
        if (imovelMorada) {
            imovelMorada.setAttribute('required', 'required');
            imovelMorada.closest('div')?.classList.remove('hidden');
        }
        if (imovelCodPostal) {
            imovelCodPostal.setAttribute('required', 'required');
            imovelCodPostal.closest('div')?.classList.remove('hidden');
        }
        if (imovelLocalidade) {
            imovelLocalidade.setAttribute('required', 'required');
            imovelLocalidade.closest('div')?.classList.remove('hidden');
        }
        
        // Remover required do campo de processo
        if (processoSelect) {
            processoSelect.removeAttribute('required');
        }
    }
}

// Carregar dados do processo quando selecionado
document.querySelector('select[name="id_processo"]')?.addEventListener('change', function() {
    if (this.value) {
        const processo = processos.find(p => p.id == this.value);
        if (processo) {
            document.getElementById('proc-requerente').textContent = processo.requerente;
            document.getElementById('proc-imovel').textContent = processo.imovel;
            document.getElementById('info-processo').classList.remove('hidden');
        }
    } else {
        document.getElementById('info-processo').classList.add('hidden');
    }
});

function adicionarItem(servico = null) {
    const container = document.getElementById('itens-container');
    const index = itemIndex++;
    
    const itemHtml = `
        <div class="item-row rounded-lg border border-slate-200 bg-slate-50 p-4" data-index="${index}">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Descrição <span class="text-red-500">*</span></label>
                    <input type="text" name="itens[${index}][descricao]" value="${servico?.nome || ''}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Preço (€) <span class="text-red-500">*</span></label>
                    <input type="number" name="itens[${index}][preco]" step="0.01" min="0" value="${servico?.preco || ''}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Quantidade <span class="text-red-500">*</span></label>
                    <input type="number" name="itens[${index}][quantidade]" step="0.01" min="0.01" value="1" required class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Prazo <span class="text-red-500">*</span></label>
                    <input type="date" name="itens[${index}][prazo_data]" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tipo <span class="text-red-500">*</span></label>
                    <select name="itens[${index}][tipo_trabalho]" required class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                        <option value="">Selecione...</option>
                        <option value="licenciamento">Licenciamento</option>
                        <option value="execucao">Execução</option>
                    </select>
                </div>
            </div>
            <div class="mt-2 flex justify-end">
                <button type="button" onclick="removerItem(${index})" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    atualizarTotal();
    adicionarListeners(index);
}

function removerItem(index) {
    const item = document.querySelector(`.item-row[data-index="${index}"]`);
    if (item) {
        item.remove();
        atualizarTotal();
    }
}

function adicionarListeners(index) {
    const row = document.querySelector(`.item-row[data-index="${index}"]`);
    if (row) {
        const inputs = row.querySelectorAll('input[name*="[preco]"], input[name*="[quantidade]"]');
        inputs.forEach(input => {
            input.addEventListener('input', atualizarTotal);
        });
    }
}

function atualizarTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const preco = parseFloat(row.querySelector('input[name*="[preco]"]')?.value || 0);
        const quantidade = parseFloat(row.querySelector('input[name*="[quantidade]"]')?.value || 0);
        total += preco * quantidade;
    });
    document.getElementById('total-negocio').textContent = total.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

// Carregar serviço
document.getElementById('select-servico')?.addEventListener('change', function() {
    if (this.value) {
        const option = this.options[this.selectedIndex];
        const servico = {
            nome: option.dataset.nome,
            preco: option.dataset.preco,
            descricao: option.dataset.descricao
        };
        adicionarItem(servico);
        this.value = '';
    }
});

// Garantir estado inicial correto ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    toggleTipoNegocio();
});

// Cascata Distrito -> Concelho -> Freguesia
(function() {
    const distritoSelect = document.querySelector('select[name="imovel_id_distrito"]');
    const concelhoSelect = document.querySelector('select[name="imovel_id_concelho"]');
    const freguesiaSelect = document.querySelector('select[name="imovel_id_freguesia"]');

    if (!distritoSelect || !concelhoSelect) return;

    function carregarConcelhos(distritoId) {
        if (!distritoId) {
            concelhoSelect.innerHTML = '<option value="">—</option>';
            if (freguesiaSelect) {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
            }
            return;
        }

        concelhoSelect.disabled = true;
        concelhoSelect.innerHTML = '<option value="">A carregar...</option>';

        fetch(`/api/concelhos/distrito/${distritoId}`)
            .then(response => response.json())
            .then(data => {
                concelhoSelect.innerHTML = '<option value="">—</option>';
                data.forEach(concelho => {
                    const option = document.createElement('option');
                    option.value = concelho.id;
                    option.textContent = concelho.nome;
                    concelhoSelect.appendChild(option);
                });
                concelhoSelect.disabled = false;

                if (freguesiaSelect) {
                    freguesiaSelect.innerHTML = '<option value="">—</option>';
                    freguesiaSelect.value = '';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar concelhos:', error);
                concelhoSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                concelhoSelect.disabled = false;
            });
    }

    function carregarFreguesias(concelhoId) {
        if (!concelhoId || !freguesiaSelect) {
            if (freguesiaSelect) {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
            }
            return;
        }

        freguesiaSelect.disabled = true;
        freguesiaSelect.innerHTML = '<option value="">A carregar...</option>';

        fetch(`/api/freguesias/concelho/${concelhoId}`)
            .then(response => response.json())
            .then(data => {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
                data.forEach(freguesia => {
                    const option = document.createElement('option');
                    option.value = freguesia.id;
                    option.textContent = freguesia.nome;
                    freguesiaSelect.appendChild(option);
                });
                freguesiaSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao carregar freguesias:', error);
                freguesiaSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                freguesiaSelect.disabled = false;
            });
    }

    distritoSelect.addEventListener('change', function() {
        carregarConcelhos(this.value);
    });

    concelhoSelect.addEventListener('change', function() {
        carregarFreguesias(this.value);
    });
})();
</script>
@endpush
@endsection
