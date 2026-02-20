@extends('layouts.app')

@section('title', ' - Editar imóvel')

@section('content')
<div class="mb-6">
    <a href="{{ route('imoveis.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar imóvel</h1>
</div>

<form method="post" action="{{ route('imoveis.update', ['imovel' => $imovel]) }}" class="max-w-3xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')

    <x-input label="NIP" name="nip" :value="$imovel->nip" />
    <x-input label="Morada" name="morada" :value="$imovel->morada" required />
    <div class="grid gap-4 sm:grid-cols-3">
        <x-select label="Distrito" name="id_distrito" :options="$distritos->pluck('nome', 'id')->all()" :selected="$imovel->id_distrito" placeholder="—" />
        <x-select label="Concelho" name="id_concelho" :options="[]" placeholder="—" />
        <x-select label="Freguesia" name="id_freguesia" :options="[]" placeholder="—" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Código postal" name="cod_postal" :value="$imovel->cod_postal" required />
        <x-input label="Localidade" name="localidade_imovel" :value="$imovel->localidade_imovel" required />
    </div>
    <x-input label="Coordenadas" name="coordenadas" :value="$imovel->coordenadas" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Potência" name="potencia" type="number" step="0.01" :value="$imovel->potencia" />
        <x-input label="Tensão" name="tensao" :value="$imovel->tensao" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Área (m²)" name="area_imovel" type="number" step="0.01" :value="$imovel->area_imovel" />
        <x-input label="Pisos" name="pisos" type="number" min="0" :value="$imovel->pisos" />
    </div>
    <x-input label="Tipo de imóvel" name="tipo_imovel" :value="$imovel->tipo_imovel" />
    <x-select label="Loja" name="id_loja" :options="$lojas->pluck('nome', 'id')->all()" :selected="$imovel->id_loja" placeholder="—" />
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">PTS (ex: 600kVA)</label>
            <input type="text" name="pts" value="{{ old('pts', is_array($imovel->pts) ? implode(', ', $imovel->pts) : '') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('pts')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">GGs (ex: 250kVA)</label>
            <input type="text" name="ggs" value="{{ old('ggs', is_array($imovel->ggs) ? implode(', ', $imovel->ggs) : '') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('ggs')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">PCVEs (ex: 50kVA)</label>
            <input type="text" name="pcves" value="{{ old('pcves', is_array($imovel->pcves) ? implode(', ', $imovel->pcves) : '') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('pcves')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <x-textarea label="Descrição" name="descricao" :value="$imovel->descricao" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Atualizar</button>
        <a href="{{ route('imoveis.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Cancelar</a>
    </div>
</form>

@push('scripts')
<script>
(function() {
    const distritoSelect = document.querySelector('select[name="id_distrito"]');
    const concelhoSelect = document.querySelector('select[name="id_concelho"]');
    const freguesiaSelect = document.querySelector('select[name="id_freguesia"]');

    if (!distritoSelect || !concelhoSelect) return;

    const distritoInicial = {{ $imovel->id_distrito ?? 'null' }};
    const concelhoInicial = {{ $imovel->id_concelho ?? 'null' }};
    const freguesiaInicial = {{ $imovel->id_freguesia ?? 'null' }};
    const concelhosIniciais = @json($concelhos->where('id_distrito', $imovel->id_distrito)->pluck('nome', 'id')->all());
    const freguesiasIniciais = @json($freguesias->where('id_concelho', $imovel->id_concelho)->pluck('nome', 'id')->all());

    function carregarConcelhos(distritoId, manterSelecionado = null) {
        if (!distritoId) {
            concelhoSelect.innerHTML = '<option value="">—</option>';
            if (freguesiaSelect) {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
            }
            return Promise.resolve();
        }

        concelhoSelect.disabled = true;
        concelhoSelect.innerHTML = '<option value="">A carregar...</option>';

        return fetch(`/api/concelhos/distrito/${distritoId}`)
            .then(response => response.json())
            .then(data => {
                concelhoSelect.innerHTML = '<option value="">—</option>';
                data.forEach(concelho => {
                    const option = document.createElement('option');
                    option.value = concelho.id;
                    option.textContent = concelho.nome;
                    if (manterSelecionado && concelho.id == manterSelecionado) {
                        option.selected = true;
                    }
                    concelhoSelect.appendChild(option);
                });
                concelhoSelect.disabled = false;

                if (freguesiaSelect && manterSelecionado === null) {
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

    function carregarFreguesias(concelhoId, manterSelecionado = null) {
        if (!concelhoId || !freguesiaSelect) {
            if (freguesiaSelect) {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
            }
            return Promise.resolve();
        }

        freguesiaSelect.disabled = true;
        freguesiaSelect.innerHTML = '<option value="">A carregar...</option>';

        return fetch(`/api/freguesias/concelho/${concelhoId}`)
            .then(response => response.json())
            .then(data => {
                freguesiaSelect.innerHTML = '<option value="">—</option>';
                data.forEach(freguesia => {
                    const option = document.createElement('option');
                    option.value = freguesia.id;
                    option.textContent = freguesia.nome;
                    if (manterSelecionado && freguesia.id == manterSelecionado) {
                        option.selected = true;
                    }
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

    // Carregar dados iniciais
    if (distritoInicial) {
        carregarConcelhos(distritoInicial, concelhoInicial).then(() => {
            if (concelhoInicial && freguesiaInicial) {
                carregarFreguesias(concelhoInicial, freguesiaInicial);
            }
        });
    } else if (Object.keys(concelhosIniciais).length > 0) {
        // Fallback: preencher com dados iniciais se não houver distrito selecionado
        concelhoSelect.innerHTML = '<option value="">—</option>';
        Object.entries(concelhosIniciais).forEach(([id, nome]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = nome;
            if (id == concelhoInicial) option.selected = true;
            concelhoSelect.appendChild(option);
        });
        
        if (freguesiaSelect && Object.keys(freguesiasIniciais).length > 0) {
            freguesiaSelect.innerHTML = '<option value="">—</option>';
            Object.entries(freguesiasIniciais).forEach(([id, nome]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nome;
                if (id == freguesiaInicial) option.selected = true;
                freguesiaSelect.appendChild(option);
            });
        }
    }
})();
</script>
@endpush
@endsection
