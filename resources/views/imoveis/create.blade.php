@extends('layouts.app')

@section('title', ' - Novo imóvel')

@section('content')
<div class="mb-6">
    <a href="{{ route('imoveis.index') }}" class="text-slate-600 hover:text-slate-900">&larr; Voltar</a>
    <h1 class="mt-2 text-2xl font-bold text-slate-800">Novo imóvel</h1>
</div>

<form method="post" action="{{ route('imoveis.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf

    <x-input label="NIP" name="nip" />
    <x-input label="Morada" name="morada" required />
    <div class="grid gap-4 sm:grid-cols-3">
        <x-select label="Distrito" name="id_distrito" :options="$distritos->pluck('nome', 'id')->all()" placeholder="—" />
        <x-select label="Concelho" name="id_concelho" :options="[]" placeholder="—" />
        <x-select label="Freguesia" name="id_freguesia" :options="[]" placeholder="—" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Código postal" name="cod_postal" required />
        <x-input label="Localidade" name="localidade_imovel" required />
    </div>
    <x-input label="Coordenadas" name="coordenadas" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Potência" name="potencia" type="number" step="0.01" />
        <x-input label="Tensão" name="tensao" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Área (m²)" name="area_imovel" type="number" step="0.01" />
        <x-input label="Pisos" name="pisos" type="number" min="0" />
    </div>
    <x-input label="Tipo de imóvel" name="tipo_imovel" />
    <x-select label="Loja" name="id_loja" :options="$lojas->pluck('nome', 'id')->all()" placeholder="—" />
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">PTS (ex: 600kVA, 600kVA)</label>
            <input type="text" name="pts" value="{{ old('pts') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('pts')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">GGs (ex: 250kVA)</label>
            <input type="text" name="ggs" value="{{ old('ggs') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('ggs')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">PCVEs (ex: 50kVA)</label>
            <input type="text" name="pcves" value="{{ old('pcves') }}" placeholder="Separar por vírgula" class="w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
            @error('pcves')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <x-textarea label="Descrição" name="descricao" :value="old('descricao')" />

    <div class="flex gap-3 pt-4">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">Guardar</button>
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
