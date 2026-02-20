@extends('layouts.app')

@section('title', ' - Processos')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Processos</h1>
        <p class="mt-1 text-slate-600">Gerir processos.</p>
    </div>
    <a href="{{ route('processos.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">
        Novo processo
    </a>
</div>

<form method="get" class="mt-6 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por código (ex.: 0014 ou 26-0014)"
        class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <select name="id_loja" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todas as lojas</option>
        @foreach($lojas as $loja)
            <option value="{{ $loja->id }}" @selected(request('id_loja') == $loja->id)>{{ $loja->nome }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
    @if(request()->hasAny(['q', 'id_loja']))
        <a href="{{ route('processos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Limpar</a>
    @endif
</form>

<div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Loja</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Designação</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Requerente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Distrito</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Concelho</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($processos as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono font-medium text-slate-800">{{ $p->codigo_formatado }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->imovel->loja->nome ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $p->designacao ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $p->requerente->nome }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->imovel->distrito->nome ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $p->imovel->concelho->nome ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('processos.show', $p) }}" class="text-slate-600 hover:text-slate-900">Ver</a>
                            <a href="{{ route('processos.edit', $p) }}" class="ml-2 text-slate-600 hover:text-slate-900">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Nenhum processo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($processos->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $processos->links() }}
        </div>
    @endif
</div>
@endsection
