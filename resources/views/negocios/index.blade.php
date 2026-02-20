@extends('layouts.app')

@section('title', ' - Negócios')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Negócios</h1>
        <p class="mt-1 text-slate-600">Gerir negócios e orçamentos.</p>
    </div>
    <a href="{{ route('negocios.create') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-700">Novo negócio</a>
</div>

<form method="get" class="mt-6 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar por designação ou requerente..." class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
    <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        <option value="">Todos os estados</option>
        @foreach(\App\Models\Negocio::STATUS as $key => $label)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600">Pesquisar</button>
</form>

<div class="mt-6 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Designação</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Requerente</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Processo</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-600">Criado</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-600">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            @forelse($negocios as $n)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">#{{ $n->id }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ Str::limit($n->designacao, 40) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $n->requerente->nome }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [
                                'pendente' => 'bg-amber-100 text-amber-800',
                                'aceite' => 'bg-emerald-100 text-emerald-800',
                                'cancelado' => 'bg-red-100 text-red-800',
                                'em_trabalho' => 'bg-blue-100 text-blue-800',
                                'concluido' => 'bg-slate-100 text-slate-800',
                                'faturado' => 'bg-purple-100 text-purple-800',
                            ];
                            $color = $statusColors[$n->status] ?? 'bg-slate-100 text-slate-800';
                        @endphp
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $color }}">{{ \App\Models\Negocio::STATUS[$n->status] ?? $n->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        @if($n->processo)
                            <a href="{{ route('processos.show', $n->processo) }}" class="text-blue-600 hover:underline">{{ $n->processo->referencia }}</a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $n->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('negocios.edit', $n) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                            <form method="post" action="{{ route('negocios.destroy', $n) }}" class="inline ml-2" onsubmit="return confirm('Eliminar este negócio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">Nenhum negócio encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($negocios->hasPages())
        <div class="border-t border-slate-200 px-4 py-3">{{ $negocios->links() }}</div>
    @endif
</div>
@endsection
