@extends('layouts.app')

@section('title', ' - Serviços')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Serviços</h1>
        <p class="mt-1 text-slate-600">Catálogo de serviços.</p>
    </div>
    <a href="{{ route('servicos.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">
        Novo serviço
    </a>
</div>

<form method="get" class="mt-6 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por código ou nome..."
        class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <select name="ativo" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todos</option>
        <option value="1" @selected(request('ativo') === '1')>Ativos</option>
        <option value="0" @selected(request('ativo') === '0')>Inativos</option>
    </select>
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
</form>

<div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Preço base</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($servicos as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $s->codigo }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $s->nome }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $s->preco_base !== null ? number_format($s->preco_base, 2, ',', ' ') . ' €' : '—' }}</td>
                        <td class="px-4 py-3">
                            @if($s->ativo)
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Ativo</span>
                            @else
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('servicos.edit', $s) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                            <form method="post" action="{{ route('servicos.destroy', $s) }}" class="inline ml-2" onsubmit="return confirm('Eliminar este serviço?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum serviço encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($servicos->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $servicos->links() }}
        </div>
    @endif
</div>
@endsection
