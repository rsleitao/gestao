@extends('layouts.app')

@section('title', ' - Freguesias')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-slate-800">Freguesias</h1>
    <p class="mt-1 text-slate-600">Gerir freguesias: editar nome.</p>
</div>

<form method="get" class="mt-6 flex flex-wrap gap-2" id="filtro-freguesias">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar..." class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <select name="id_distrito" id="filtro-distrito" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todos os distritos</option>
        @foreach($distritos as $d)
            <option value="{{ $d->id }}" @selected(request('id_distrito') == $d->id)>{{ $d->nome }}</option>
        @endforeach
    </select>
    <select name="id_concelho" id="filtro-concelho" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todos os concelhos</option>
        @foreach($concelhos as $c)
            <option value="{{ $c->id }}" data-distrito="{{ $c->id_distrito }}" @selected(request('id_concelho') == $c->id)>{{ $c->nome }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
    @if(request()->hasAny(['q', 'id_distrito', 'id_concelho']))
        <a href="{{ route('freguesias.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Limpar</a>
    @endif
</form>

<div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Nome</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Concelho</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            @forelse($freguesias as $f)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $f->nome }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $f->concelho->nome ?? '—' }} ({{ $f->concelho->distrito->nome ?? '' }})</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('freguesias.edit', $f) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-slate-500">Nenhuma freguesia encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($freguesias->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">{{ $freguesias->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    const distritoSelect = document.getElementById('filtro-distrito');
    const concelhoSelect = document.getElementById('filtro-concelho');
    const form = document.getElementById('filtro-freguesias');
    
    if (!distritoSelect || !concelhoSelect) return;
    
    const concelhosIniciais = Array.from(concelhoSelect.options).map(opt => ({
        value: opt.value,
        text: opt.textContent.trim(), // Usar textContent para pegar apenas o texto visível
        distrito: opt.dataset.distrito
    }));
    
    function atualizarConcelhos(distritoId) {
        const valorSelecionado = concelhoSelect.value;
        concelhoSelect.innerHTML = '<option value="">Todos os concelhos</option>';
        
        concelhosIniciais.forEach(concelho => {
            if (!concelho.value) return; // Pular a opção "Todos os concelhos"
            if (!distritoId || concelho.distrito == distritoId) {
                const option = document.createElement('option');
                option.value = concelho.value;
                option.textContent = concelho.text;
                option.dataset.distrito = concelho.distrito;
                if (concelho.value == valorSelecionado && (!distritoId || concelho.distrito == distritoId)) {
                    option.selected = true;
                }
                concelhoSelect.appendChild(option);
            }
        });
    }
    
    distritoSelect.addEventListener('change', function() {
        atualizarConcelhos(this.value);
        // Opcional: submeter automaticamente o formulário quando mudar o distrito
        // form.submit();
    });
    
    // Inicializar com o distrito selecionado (se houver)
    const distritoInicial = distritoSelect.value;
    if (distritoInicial) {
        atualizarConcelhos(distritoInicial);
    }
})();
</script>
@endpush
@endsection
